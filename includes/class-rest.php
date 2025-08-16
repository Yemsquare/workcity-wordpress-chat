<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Workcity_Chat_REST {
    public static function register_routes() {
        register_rest_route('workcity/v1', '/start-chat', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'start_chat'],
            'permission_callback' => function() { return is_user_logged_in(); }
        ]);

        register_rest_route('workcity/v1', '/send-message', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'send_message'],
            'permission_callback' => [__CLASS__, 'check_session_permission']
        ]);

        register_rest_route('workcity/v1', '/get-messages', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'get_messages'],
            'permission_callback' => [__CLASS__, 'check_session_permission']
        ]);

        register_rest_route('workcity/v1', '/mark-read', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'mark_read'],
            'permission_callback' => [__CLASS__, 'check_session_permission']
        ]);

        register_rest_route('workcity/v1', '/upload', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'upload_file'],
            'permission_callback' => [__CLASS__, 'check_session_permission']
        ]);
    }

    public static function check_session_permission( WP_REST_Request $request ) {
        if ( ! is_user_logged_in() ) return false;
        $session_id = intval( $request->get_param('session_id') );
        if ( ! $session_id ) return true; // allow if not tied to a session (e.g. upload pre-check)
        $post = get_post($session_id);
        if ( ! $post || $post->post_type !== 'chat_session' ) return false;
        $user_id = get_current_user_id();

        // Author or shop manager/admin (manage_woocommerce) has access.
        if ( intval($post->post_author) === $user_id || current_user_can('manage_woocommerce') ) {
            return true;
        }
        // TODO: extend to designers/agents/merchants via participants meta.
        return false;
    }

    public static function start_chat( WP_REST_Request $request ) {
        $product_id  = intval( $request->get_param('product_id') );
        $title_parts = ['Chat', 'User', get_current_user_id()];
        if ($product_id) { $title_parts[] = 'Product'; $title_parts[] = $product_id; }

        $post_id = wp_insert_post([
            'post_type'   => 'chat_session',
            'post_title'  => implode(' - ', $title_parts),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ]);
        if ( is_wp_error($post_id) ) {
            return new WP_REST_Response(['error' => $post_id->get_error_message()], 500);
        }
        if ($product_id) {
            update_post_meta($post_id, '_workcity_chat_product_id', $product_id);
        }
        return new WP_REST_Response(['session_id' => $post_id], 201);
    }

    public static function send_message( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'workcity_chat_messages';
        $session_id   = intval( $request->get_param('session_id') );
        $message      = wp_kses_post( $request->get_param('message') );
        $attachment_id= intval( $request->get_param('attachment_id') );

        if ( ! $session_id || (! $message && ! $attachment_id) ) {
            return new WP_REST_Response(['error' => 'Missing content'], 400);
        }
        $post = get_post($session_id);
        if ( ! $post || $post->post_type !== 'chat_session' ) {
            return new WP_REST_Response(['error' => 'Invalid chat session'], 404);
        }
        $sender_id = get_current_user_id();
        $content   = $message;

        if ( $attachment_id ) {
            $url = wp_get_attachment_url($attachment_id);
            $content .= sprintf('<p><a href="%s" target="_blank">%s</a></p>', esc_url($url), basename($url));
        }

        $wpdb->insert($table, [
            'session_id' => $session_id,
            'sender_id'  => $sender_id,
            'message'    => $content,
            'is_read'    => 0,
            'created_at' => current_time('mysql'),
        ]);

        return new WP_REST_Response([
            'id'         => $wpdb->insert_id,
            'session_id' => $session_id,
            'sender_id'  => $sender_id,
            'message'    => $content,
            'created_at' => current_time('mysql')
        ], 201);
    }

    public static function get_messages( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'workcity_chat_messages';
        $session_id = intval( $request->get_param('session_id') );
        $since_id   = intval( $request->get_param('since_id') );

        $post = get_post($session_id);
        if ( ! $post || $post->post_type !== 'chat_session' ) {
            return new WP_REST_Response(['error' => 'Invalid chat session'], 404);
        }

        if ( $since_id > 0 ) {
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE session_id=%d AND id>%d ORDER BY id ASC",
                $session_id, $since_id
            ), ARRAY_A );
        } else {
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE session_id=%d ORDER BY id ASC",
                $session_id
            ), ARRAY_A );
        }
        return new WP_REST_Response(['messages' => $rows], 200);
    }

    public static function mark_read( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'workcity_chat_messages';
        $session_id = intval( $request->get_param('session_id') );
        if ( ! $session_id ) {
            return new WP_REST_Response(['error' => 'Missing session_id'], 400);
        }
        $wpdb->update($table, ['is_read' => 1], ['session_id' => $session_id]);
        return new WP_REST_Response(['status' => 'ok'], 200);
    }

    public static function upload_file( WP_REST_Request $request ) {
        if ( empty($_FILES['file']) ) {
            return new WP_REST_Response(['error' => 'No file uploaded'], 400);
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded = wp_handle_upload($_FILES['file'], ['test_form' => false]);

        if ( isset($uploaded['error']) ) {
            return new WP_REST_Response(['error' => $uploaded['error']], 500);
        }

        $filetype = wp_check_filetype($uploaded['file']);
        $attachment = [
            'guid'           => $uploaded['url'],
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name(basename($uploaded['file'])),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
        $attach_id = wp_insert_attachment($attachment, $uploaded['file']);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return new WP_REST_Response(['attachment_id' => $attach_id, 'url' => wp_get_attachment_url($attach_id)], 201);
    }
}
