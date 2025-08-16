(function() {
  const { createElement: h, useState, useEffect } = wp.element;
  const renderFn = wp.element.render || (window.ReactDOM && window.ReactDOM.render);

  function apiFetch(path, options) {
    options = options || {};
    options.headers = Object.assign({}, options.headers || {}, {
      'X-WP-Nonce': WORKCITY_CHAT.nonce
    });
    return fetch(WORKCITY_CHAT.restURL + path, options).then(res => res.json());
  }

  function ChatApp() {
    const [sessionId, setSessionId] = useState(null);
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [sinceId, setSinceId] = useState(0);
    const [file, setFile] = useState(null);
    const [dark, setDark] = useState(localStorage.getItem('wcChatDark') === '1');
    const [presence, setPresence] = useState([]);

    const rootEl = document.getElementById('workcity-chat-root');
    const productId = rootEl ? parseInt(rootEl.getAttribute('data-product-id') || '0', 10) : 0;

    // Start chat session
    useEffect(() => {
      if (!WORKCITY_CHAT.isLoggedIn) return;
      apiFetch('start-chat', {
        method: 'POST',
        body: JSON.stringify({ product_id: productId }),
        headers: { 'Content-Type': 'application/json' }
      }).then(data => {
        if (data && data.session_id) setSessionId(data.session_id);
      });
    }, []);

    // Poll for messages
    useEffect(() => {
      if (!sessionId) return;
      const interval = setInterval(() => {
        apiFetch(`get-messages?session_id=${sessionId}${sinceId ? '&since_id=' + sinceId : ''}`)
          .then(data => {
            if (data && Array.isArray(data.messages) && data.messages.length) {
              setMessages(prev => prev.concat(data.messages));
              const maxId = data.messages.reduce((m, row) => Math.max(m, parseInt(row.id, 10)), sinceId);
              setSinceId(maxId);
            }
          });
      }, 2500);
      return () => clearInterval(interval);
    }, [sessionId, sinceId]);

    // Poll for presence (typing)
    useEffect(() => {
      if (!sessionId) return;
      const interval = setInterval(() => {
        apiFetch(`presence?session_id=${sessionId}`)
          .then(data => {
            if (data && data.participants) setPresence(data.participants);
          });
      }, 3000);
      return () => clearInterval(interval);
    }, [sessionId]);

    // Send message / file
    function send() {
      if (!input.trim() && !file) return;

      if (file) {
        const formData = new FormData();
        formData.append('file', file);
        apiFetch('upload?session_id=' + sessionId, { method: 'POST', body: formData })
          .then(res => {
            if (res && res.attachment_id) {
              apiFetch('send-message', {
                method: 'POST',
                body: JSON.stringify({ session_id: sessionId, attachment_id: res.attachment_id }),
                headers: { 'Content-Type': 'application/json' }
              });
            }
            setFile(null);
          });
      }

      if (input.trim()) {
        apiFetch('send-message', {
          method: 'POST',
          body: JSON.stringify({ session_id: sessionId, message: input.trim() }),
          headers: { 'Content-Type': 'application/json' }
        }).then(row => {
          if (row && row.id) {
            setMessages(prev => prev.concat([row]));
            setSinceId(row.id);
            setInput('');
          }
        });
      }
    }

    // Input typing handler
    function onInputChange(e) {
      setInput(e.target.value);
      if (!sessionId) return;
      apiFetch('typing', {
        method: 'POST',
        body: JSON.stringify({ session_id: sessionId, is_typing: e.target.value.length > 0 }),
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Dark mode toggle
    function toggleDark() {
      const next = !dark;
      setDark(next);
      localStorage.setItem('wcChatDark', next ? '1' : '0');
    }

    if (!WORKCITY_CHAT.isLoggedIn) {
      return h('div', { className: 'wc-chat-card' }, h('p', null, 'Please log in to start a chat.'));
    }

    return h('div', { className: 'wc-chat-card' + (dark ? ' dark' : '') },
      h('div', { className: 'wc-chat-header' },
        h('div', { className: 'wc-chat-title' }, 'Workcity Chat'),
        h('div', null, h('button', { onClick: toggleDark }, dark ? 'Light' : 'Dark'))
      ),
      h('div', { className: 'wc-chat-body' },
        messages.map(m => h('div', { key: m.id, className: 'wc-chat-msg ' + (parseInt(m.sender_id,10) === WORKCITY_CHAT.user.id ? 'me' : 'them') },
          h('div', { className: 'wc-chat-bubble', dangerouslySetInnerHTML: { __html: m.message } })
        ))
      ),
      h('div', { className: 'wc-chat-typing' },
        presence.filter(p => p.id !== WORKCITY_CHAT.user.id && p.typing)
                .map(p => h('div', { key: p.id }, `${p.name} is typing...`))
      ),
      h('div', { className: 'wc-chat-input' },
        h('input', { type: 'text', value: input, onChange: onInputChange, placeholder: 'Type a message...' }),
        h('input', { type: 'file', onChange: e => setFile(e.target.files[0]) }),
        h('button', { onClick: send }, 'Send')
      )
    );
  }

  function mount() {
    const root = document.getElementById('workcity-chat-root');
    if (!root || !renderFn) return;
    renderFn(h(ChatApp), root);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
