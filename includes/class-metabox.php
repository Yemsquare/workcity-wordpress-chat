<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Workcity_Chat_Metabox {
    public static function add_boxes() {
        add_meta_box(
            'workcity_chat_product',
            __('Product Context', 'workcity-chat'),
            [__CLASS__, 'render_box'],
            'chat_session',
            'side',
            'default'
        );
    }

   public static function render_box( $post ) {
    wp_nonce_field('workcity_chat_meta', 'workcity_chat_meta_nonce');
    $product_id   = get_post_meta($post->ID, '_workcity_chat_product_id', true);
    $participants = get_post_meta($post->ID, '_workcity_chat_participants', true);
    if ( ! is_array($participants) ) $participants = [];

    echo '<p><label for="workcity_chat_product_id">'. esc_html__('WooCommerce Product ID', 'workcity-chat') .'</label></p>';
    echo '<input type="number" name="workcity_chat_product_id" value="'. esc_attr($product_id) .'" style="width:100%;" />';

    echo '<hr><p><strong>'.esc_html__('Participants (User IDs)', 'workcity-chat').'</strong></p>';
    echo '<textarea name="workcity_chat_participants" style="width:100%;" rows="3">'. esc_textarea(implode(',', $participants)) .'</textarea>';
    echo '<p>'.esc_html__('Comma-separated user IDs (merchant, designer, agent).', 'workcity-chat').'</p>';
}

public static function save_meta( $post_id ) {
    if ( ! isset($_POST['workcity_chat_meta_nonce']) || ! wp_verify_nonce($_POST['workcity_chat_meta_nonce'], 'workcity_chat_meta') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    if ( isset($_POST['workcity_chat_product_id']) ) {
        update_post_meta($post_id, '_workcity_chat_product_id', intval($_POST['workcity_chat_product_id']));
    }
    if ( isset($_POST['workcity_chat_participants']) ) {
        $ids = array_filter(array_map('intval', explode(',', $_POST['workcity_chat_participants'])));
        update_post_meta($post_id, '_workcity_chat_participants', $ids);
    }
}
