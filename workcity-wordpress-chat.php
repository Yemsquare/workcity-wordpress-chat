<?php 
/** 
 * Plugin name: WorkCity WordPress Chat
 * Description: A starter chat system integrated with WooCommerce. Provides CPT, REST endpoints, shortcode with a minimal React UI.
 * Version: 1.0.0
 * Author: Dev Yemsquare
 * Author URI: https://www.squaretech.online
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: workcity-wordpress-chat
 * Domain Path: /languages 
 * Requires at least: 6.0
 *  
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

defined( 'WORKCITY_CHAT_VERSION' ) or define( 'WORKCITY_CHAT_VERSION', '1.0.0' );
defined( 'WORKCITY_CHAT_PLUGIN_DIR' ) or define( 'WORKCITY_CHAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'WORKCITY_CHAT_PLUGIN_URL' ) or define( 'WORKCITY_CHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WORKCITY_CHAT_PLUGIN_DIR . 'includes/class-workcity-chat-plugin.php';
require_once WORKCITY_CHAT_PLUGIN_DIR . 'includes/class-workcity-chat-cpt.php';
require_once WORKCITY_CHAT_PLUGIN_DIR . 'includes/class-workcity-chat-rest.php';




