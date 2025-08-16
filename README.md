# Workcity WordPress Chat

A custom WordPress plugin that provides a **real-time chat system** integrated with WooCommerce.  
It uses a **React (wp-element) frontend**, **custom REST API endpoints**, and a **custom database table** for messages.

---

## ✨ Features

- **Custom Post Type**: `chat_session` for managing chat threads.
- **React Frontend (wp-element)**: Lightweight, no build step required.
- **Shortcode**: `[workcity_chat product_id="123"]` to embed chat on any page.
- **Real-time messaging**: AJAX polling every 2.5s (can be swapped for WebSockets).
- **WooCommerce Integration**: Attach product context to chat sessions.
- **Role-based permissions**:
  - Chat **author (buyer)**
  - **Assigned participants** (merchant, designer, agent)
  - **Shop manager / Admin**
- **Typing indicator**: See when other users are typing.
- **Presence polling**: Shows which participants are active in the session.
- **File uploads**: Send images or files inside chat.
- **Dark/Light mode toggle** with state saved in `localStorage`.
- **Read/Unread status** stored in DB (extendable to per-participant).

---

## 📂 Installation

1. Copy the folder into `wp-content/plugins/workcity-wordpress-chat/`
   or upload the ZIP via **Plugins → Add New → Upload Plugin**.
2. Activate **Workcity WordPress Chat** in the WP Admin.
3. Permalinks must be enabled (e.g. "Post name").
4. Add the shortcode `[workcity_chat]` to any page.
   - Optionally pass a product:  
     ```php
     [workcity_chat product_id="123"]
     ```

---

## 🚀 Usage

1. **Logged-in users only** can use the chat.
2. When a user loads the page, a `chat_session` is auto-created (or reused).
3. Messages are saved in a custom DB table `wp_workcity_chat_messages`.
4. Participants can upload files, toggle dark/light mode, and see typing status.

---

## 🔌 REST API Endpoints

All routes are under `/wp-json/workcity/v1/` and require authentication.

### Sessions
- `POST /start-chat`
  ```json
  { "product_id": 123 }
  → { "session_id": 45 }
