<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduHelper — AI Tutor</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .chat-wrapper {
            width: 100%;
            max-width: 680px;
            height: 100vh;
            max-height: 780px;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        /* Header */
        .chat-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .chat-header .avatar {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .chat-header .info h1 {
            font-size: 17px;
            font-weight: 700;
        }

        .chat-header .info p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        /* Topics bar */
        .topics-bar {
            background: #f8f7ff;
            border-bottom: 1px solid #e8e4ff;
            padding: 10px 20px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .topic-chip {
            background: #ede9fe;
            color: #5b21b6;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }

        .topic-chip:hover { background: #ddd6fe; }

        /* Messages */
        #chatBox {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            scroll-behavior: smooth;
        }

        #chatBox::-webkit-scrollbar { width: 5px; }
        #chatBox::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        .message {
            display: flex;
            gap: 10px;
            max-width: 85%;
            animation: fadeUp 0.25s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .message.user { align-self: flex-end; flex-direction: row-reverse; }
        .message.bot  { align-self: flex-start; }

        .msg-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .message.user .msg-avatar { background: #4f46e5; color: #fff; }
        .message.bot  .msg-avatar { background: #ede9fe; }

        .bubble {
            padding: 11px 16px;
            border-radius: 18px;
            font-size: 14.5px;
            line-height: 1.55;
            word-break: break-word;
        }

        .message.user .bubble {
            background: #4f46e5;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .message.bot .bubble {
            background: #f3f4f6;
            color: #1f2937;
            border-bottom-left-radius: 4px;
        }

        /* Typing indicator */
        .typing-bubble {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 12px 16px;
            background: #f3f4f6;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
        }

        .typing-bubble span {
            width: 7px;
            height: 7px;
            background: #9ca3af;
            border-radius: 50%;
            animation: bounce 1.2s infinite;
        }

        .typing-bubble span:nth-child(2) { animation-delay: 0.2s; }
        .typing-bubble span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30%            { transform: translateY(-6px); }
        }

        /* Input area */
        .chat-footer {
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        #chatForm {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #message {
            flex: 1;
            padding: 12px 18px;
            border: 1.5px solid #e5e7eb;
            border-radius: 30px;
            font-size: 14.5px;
            outline: none;
            transition: border-color 0.2s;
            background: #f9fafb;
        }

        #message:focus { border-color: #4f46e5; background: #fff; }

        #sendBtn {
            width: 46px;
            height: 46px;
            background: #4f46e5;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.1s;
            flex-shrink: 0;
        }

        #sendBtn:hover  { background: #4338ca; }
        #sendBtn:active { transform: scale(0.93); }
        #sendBtn:disabled { background: #a5b4fc; cursor: not-allowed; }

        #sendBtn svg { width: 20px; height: 20px; fill: #fff; }

        .error-bubble {
            background: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>

<div class="chat-wrapper">

    <div class="chat-header">
        <div class="avatar">🎓</div>
        <div class="info">
            <h1>EduHelper AI</h1>
            <p><span class="status-dot"></span>Online · Your personal tutor</p>
        </div>
    </div>

    <div class="topics-bar">
        <button class="topic-chip" onclick="quickAsk('Tell me about the Solar System')">🪐 Solar System</button>
        <button class="topic-chip" onclick="quickAsk('Explain fractions to me')">➗ Fractions</button>
        <button class="topic-chip" onclick="quickAsk('How does the Water Cycle work?')">💧 Water Cycle</button>
    </div>

    <div id="chatBox">
        <div class="message bot">
            <div class="msg-avatar">🎓</div>
            <div class="bubble">
                Hi there! 👋 I'm <strong>EduHelper</strong>, your AI tutor. I can help you with <strong>Solar System</strong>, <strong>Fractions</strong>, and the <strong>Water Cycle</strong>. What would you like to learn today?
            </div>
        </div>
    </div>

    <div class="chat-footer">
        <form id="chatForm">
            <input
                type="text"
                id="message"
                placeholder="Ask me anything about the topics above…"
                autocomplete="off"
                maxlength="500"
            />
            <button type="submit" id="sendBtn" title="Send">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </form>
    </div>

</div>

<script>
    const chatBox  = document.getElementById('chatBox');
    const form     = document.getElementById('chatForm');
    const input    = document.getElementById('message');
    const sendBtn  = document.getElementById('sendBtn');

    function scrollBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function appendMessage(role, text, isError = false) {
        const wrap = document.createElement('div');
        wrap.className = `message ${role}`;

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar';
        avatar.textContent = role === 'user' ? '🧑' : '🎓';

        const bubble = document.createElement('div');
        bubble.className = 'bubble' + (isError ? ' error-bubble' : '');
        bubble.textContent = text;

        wrap.appendChild(avatar);
        wrap.appendChild(bubble);
        chatBox.appendChild(wrap);
        scrollBottom();
        return wrap;
    }

    function showTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'message bot';
        wrap.id = 'typing-indicator';

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar';
        avatar.textContent = '🎓';

        const bubble = document.createElement('div');
        bubble.className = 'typing-bubble';
        bubble.innerHTML = '<span></span><span></span><span></span>';

        wrap.appendChild(avatar);
        wrap.appendChild(bubble);
        chatBox.appendChild(wrap);
        scrollBottom();
    }

    function removeTyping() {
        const el = document.getElementById('typing-indicator');
        if (el) el.remove();
    }

    async function sendMessage(text) {
        if (!text.trim()) return;

        appendMessage('user', text);
        input.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const res = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            removeTyping();

            if (!res.ok) {
                appendMessage('bot', data.message || 'Something went wrong. Please try again.', true);
            } else {
                appendMessage('bot', data.reply);
            }
        } catch (err) {
            removeTyping();
            appendMessage('bot', 'Network error. Please check your connection.', true);
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(input.value);
    });

    function quickAsk(text) {
        input.value = text;
        sendMessage(text);
    }

    input.focus();
</script>

</body>
</html>
