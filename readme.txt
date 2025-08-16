=== Workcity WordPress Chat ===
Contributors: workcitydev
Tags: chat, woocommerce, realtime, react, messaging
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A real-time chat system for WooCommerce, built with React (wp-element) and custom REST API endpoints.

== Description ==

Workcity WordPress Chat is a custom plugin that provides a lightweight but extensible **chat system** for WooCommerce-enabled sites.

It features:
* Custom Post Type `chat_session` for conversations
* React-based frontend using WordPress’s built-in `wp-element`
* Real-time messaging via AJAX polling (upgradeable to WebSockets)
* WooCommerce product context linked to chats
* Role-based access (buyers, merchants, designers, agents, admins)
* File uploads in chat
* Dark/Light mode toggle
* Typing indicator and presence polling

This plugin was built as part of a **WordPress Developer Assessment**, but is structured for real-world extensibility.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/workcity-wordpress-chat` directory, or install the ZIP via WordPress admin.
2. Activate the plugin through the 'Plugins' menu.
3. Add the shortcode `[workcity_chat]` to any page.
   * Optionally pass a product context:
     `[workcity_chat product_id="123"]`
4. Log in as a user and open the chat page to start a session.

== Frequently Asked Questions ==

= Who can access a chat session? =
- The session author (buyer)
- Assigned participants (merchant, designer, agent — set in chat session meta)
- Shop Managers and Admins

= Where are messages stored? =
In a custom table `wp_workcity_chat_messages`, created on plugin activation.

= Can this be made “truly real-time”? =
Yes. The plugin uses AJAX polling by default for portability, but can be extended to WebSockets (via Pusher, Socket.io, or Ably).

= Does it support notifications? =
Not yet. You can extend `send-message` to trigger email or push notifications.

== Screenshots ==
1. Chat UI (light mode)
2. Chat UI (dark mode)
3. File upload in chat
4. Typing indicator
5. Product-linked chat

== Changelog ==

= 1.0.0 =
* Initial release
* Chat sessions via CPT
* REST API for messaging
* React (wp-element) frontend
* File uploads
* Typing indicator
* Dark/Light toggle

== Upgrade Notice ==

= 1.0.0 =
First stable release of Workcity WordPress Chat.

== License ==

This plugin is licensed under the GPL v2 or later.  
You are free to modify and distribute it under the same license terms.

