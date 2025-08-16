/* Minimal styles + dark mode */
.workcity-chat-root { max-width: 420px; }
.wc-chat-card {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Helvetica Neue",Arial;
}
.wc-chat-card.dark { background: #1f2937; color: #f3f4f6; }
.wc-chat-card.dark .wc-chat-header { background: #111827; color: #f9fafb; }
.wc-chat-card.dark .wc-chat-body { background: #1f2937; }
.wc-chat-card.dark .wc-chat-bubble { background: #374151; color: #f9fafb; }
.wc-chat-card.dark .wc-chat-msg.me .wc-chat-bubble { background: #2563eb; }

.wc-chat-header {
  padding: 10px 12px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
}
.wc-chat-title { font-weight: 600; }
.wc-chat-body {
  height: 300px;
  overflow-y: auto;
  padding: 12px;
  background: #ffffff;
}
.wc-chat-msg { display: flex; margin-bottom: 10px; }
.wc-chat-msg.me { justify-content: flex-end; }
.wc-chat-bubble {
  max-width: 75%;
  padding: 8px 10px;
  border-radius: 10px;
  background: #f3f4f6;
  word-break: break-word;
}
.wc-chat-msg.me .wc-chat-bubble { background: #e0f2fe; }
.wc-chat-input {
  display: flex;
  gap: 6px;
  padding: 10px;
  border-top: 1px solid #e5e7eb;
}
.wc-chat-input input[type="text"], .wc-chat-input input[type="file"] {
  flex: 1;
  padding: 8px 10px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
}
.wc-chat-input button {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  background: #111827;
  color: white;
  border-radius: 8px;
  cursor: pointer;
}
.wc-chat-typing {
  padding: 6px 12px;
  font-size: 12px;
  color: #6b7280;
  font-style: italic;
}
.wc-chat-card.dark .wc-chat-typing { color: #d1d5db; }
