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
- **POST** `/start-chat`
  ```json
  { "product_id": 123 }
  → { "session_id": 45 }
  ```

### Messages
- **POST** `/send-message`
  ```json
  { "session_id": 45, "message": "Hello" }
  ```

- With an attachment:
  ```json
  { "session_id": 45, "attachment_id": 99 }
  ```

- **GET** `/get-messages?session_id=45&since_id=0`  
  Returns message history. Use `since_id` for incremental updates.

- **POST** `/mark-read`
  ```json
  { "session_id": 45 }
  ```

### File Uploads
- **POST** `/upload?session_id=45`  
  Multipart form-data: `file=@/path/to/file.png`  
  → Returns `attachment_id` and `url`.

### Presence / Typing
- **POST** `/typing`
  ```json
  { "session_id": 45, "is_typing": true }
  ```

- **GET** `/presence?session_id=45`  
  → Returns participants with typing state.

---

## 🛠️ Technical Details

### Frontend
- Built with **WordPress’s built-in React (`wp-element`)**.
- Main code: `assets/js/chat-app.js`.
- Styles: `assets/css/chat.css` (supports dark/light mode).

### Database
- Messages are stored in:
  ```
  wp_workcity_chat_messages
  ```
- Table is created automatically on plugin activation.

### Permissions
- Allowed roles:
  - **Author (buyer)**
  - **Participants** (set via `chat_session` meta box in wp-admin)
  - **Shop manager / Admin**
- Permissions handled by `check_session_permission()` in `class-rest.php`.

### Participants
- Stored as post meta `_workcity_chat_participants` (array of user IDs).
- Editable in wp-admin via a custom meta box.

---

## 🔮 Future Improvements

- **WebSockets**: Replace polling with WebSockets for instant updates.
- **Per-user read receipts**: Track read status per participant.
- **Push/email notifications**: Notify users when new messages arrive.
- **Enhanced UI**: Add avatars, product thumbnails, chat list view.
- **Participant assignment UI**: Dropdowns for merchants, designers, and agents.
- **Scalability**: Integrate Redis or an external service (e.g., Pusher/Ably).

---

## 📜 License

GPL v2 or later.
