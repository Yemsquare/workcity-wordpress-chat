<?php
/**
 * Plugin Name: Workcity WordPress Chat
 * Plugin URI: https://example.com/
 * Description: A starter chat system integrated with WooCommerce. Provides CPT, REST endpoints, shortcode with a minimal React UI.
 * Version: 0.1.0
 * Author: Dev Yemsquare
 * License: GPLv2 or later
 * Text Domain: workcity-chat
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WORKCITY_CHAT_VERSION', '0.1.0' );
define( 'WORKCITY_CHAT_DIR', plugin_dir_path( __FILE__ ) );
define( 'WORKCITY_CHAT_URL', plugin_dir_url( __FILE__ ) );

require_once WORKCITY_CHAT_DIR . 'includes/class-activator.php';
require_once WORKCITY_CHAT_DIR . 'includes/class-rest.php';
require_once WORKCITY_CHAT_DIR . 'includes/class-metabox.php';

class Workcity_Chat_Plugin {
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('workcity_chat', [$this, 'render_shortcode']);

        register_activation_hook(__FILE__, ['Workcity_Chat_Activator', 'activate']);
        add_action('rest_api_init', ['Workcity_Chat_REST', 'register_routes']);

        add_action('add_meta_boxes', ['Workcity_Chat_Metabox', 'add_boxes']);
        add_action('save_post_chat_session', ['Workcity_Chat_Metabox', 'save_meta']);
    }

    public function register_cpt() {
        $labels = [
            'name' => __('Chat Sessions', 'workcity-chat'),
            'singular_name' => __('Chat Session', 'workcity-chat'),
        ];
        $args = [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'author'],
            'has_archive' => false,
            'rewrite' => false,
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-format-chat',
        ];
        register_post_type('chat_session', $args);
    }

    public function enqueue_assets() {
        wp_enqueue_style('workcity-chat-css', WORKCITY_CHAT_URL . 'assets/css/chat.css', [], WORKCITY_CHAT_VERSION);

        // Use WP’s React wrapper + apiFetch; no build step
        wp_enqueue_script(
            'workcity-chat-app',
            WORKCITY_CHAT_URL . 'assets/js/chat-app.js',
            ['wp-element', 'wp-i18n', 'wp-api-fetch'],
            WORKCITY_CHAT_VERSION,
            true
        );

        $current_user = wp_get_current_user();
        $data = [
            'restURL'    => esc_url_raw( rest_url('workcity/v1/') ),
            'nonce'      => wp_create_nonce('wp_rest'),
            'user'       => [
                'id'   => get_current_user_id(),
                'name' => $current_user ? $current_user->display_name : 'Guest',
            ],
            'isLoggedIn' => is_user_logged_in(),
        ];
        wp_localize_script('workcity-chat-app', 'WORKCITY_CHAT', $data);
    }

    public function render_shortcode($atts = []) {
        $atts = shortcode_atts([
            'product_id' => 0,
        ], $atts, 'workcity_chat');

        ob_start(); ?>
        <div id="workcity-chat-root"
             class="workcity-chat-root"
             data-product-id="<?php echo esc_attr($atts['product_id']); ?>">
        </div>
        <?php return ob_get_clean();
    }
}

new Workcity_Chat_Plugin();
