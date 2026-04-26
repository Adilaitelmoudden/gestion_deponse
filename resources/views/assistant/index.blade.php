@extends('layouts.app')

@section('title', 'Assistant IA')
@section('header', 'Assistant IA')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- ── Header card ─────────────────────────────────── --}}
        <div class="card mb-3" style="border-top: 3px solid #7c3aed">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    <i class="fas fa-robot text-white"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="color:#4f46e5">Assistant IA — Conseiller financier</div>
                    <div class="small text-muted">كيجاوبك على أسئلتك باستعمال بياناتك الحقيقية</div>
                </div>
                <div class="ms-auto">
                    <span class="badge" id="statusBadge" style="background:#dcfce7;color:#16a34a;font-size:11px">
                        <i class="fas fa-circle me-1" style="font-size:8px"></i>En ligne
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Suggestions rapides ──────────────────────────── --}}
        <div id="suggestions" class="mb-3 d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                💸 فين مشا فلوسي هذا الشهر؟
            </button>
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                📊 كيفاش هو الوضع المالي ديالي؟
            </button>
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                💡 عطيني نصيحة باش نوفر أكثر
            </button>
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                🎯 واش أنا فـ الطريق الصحيح لأهدافي؟
            </button>
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                📅 شنو عندي يتحاسب هذا الشهر؟
            </button>
            <button class="btn btn-sm btn-outline-secondary suggestion-btn" onclick="sendSuggestion(this)">
                📈 مقارنة الشهر الحالي بالشهر اللي فات
            </button>
        </div>

        {{-- ── Chat window ─────────────────────────────────── --}}
        <div class="card" style="height:480px;display:flex;flex-direction:column">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="small fw-semibold text-muted">
                    <i class="fas fa-comments me-1"></i>Conversation
                </span>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" id="clearBtn" onclick="clearChat()" title="Effacer la conversation">
                    <i class="fas fa-trash-alt me-1"></i><span class="small">Effacer</span>
                </button>
            </div>

            {{-- Messages area --}}
            <div id="chatMessages" class="flex-grow-1 overflow-auto p-3" style="scroll-behavior:smooth">
                {{-- Welcome message --}}
                <div class="msg-row ai">
                    <div class="ai-avatar"><i class="fas fa-robot text-white" style="font-size:12px"></i></div>
                    <div class="msg-bubble-ai">
                        مرحبا! 👋 أنا المساعد المالي ديالك. كنعرف بياناتك الحقيقية — الدخل، الدخارج، الميزانيات والأهداف ديالك.<br><br>
                        سؤل مني أي حاجة عن وضعك المالي وغادي نجاوبك بالأرقام الحقيقية! 📊
                    </div>
                </div>
            </div>

            {{-- Input area --}}
            <div class="card-footer p-2" style="border-top:1px solid var(--color-card-border,#e2e8f0)">
                <div class="d-flex gap-2">
                    <input type="text" id="chatInput" class="form-control form-control-sm"
                           placeholder="اكتب سؤالك هنا..." autocomplete="off"
                           onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendMessage();}">
                    <button class="btn btn-primary btn-sm px-3" id="sendBtn" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Disclaimer ──────────────────────────────────── --}}
        <div class="mt-2 text-center">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                المساعد كيشتغل فقط على بياناتك — ما كيشاركش معلوماتك مع حد.
            </small>
        </div>

    </div>
</div>

<style>
#chatMessages { background: var(--color-card-bg, #fff); }
.msg-bubble-user {
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    color: #fff;
    border-radius: 18px 18px 4px 18px;
    padding: 10px 14px;
    max-width: 80%;
    font-size: 13.5px;
    line-height: 1.55;
    word-break: break-word;
}
.msg-bubble-ai {
    background: var(--color-surface-hover, #f0f0f8);
    color: var(--color-text-primary, #1e2433);
    border-radius: 18px 18px 18px 4px;
    padding: 10px 14px;
    max-width: 85%;
    font-size: 13.5px;
    line-height: 1.65;
    word-break: break-word;
}
.msg-row { display:flex; gap:10px; margin-bottom:14px; align-items:flex-end; }
.msg-row.user { flex-direction:row-reverse; }
.ai-avatar {
    width:32px; height:32px; border-radius:50%;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.typing-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:#94a3b8; margin:0 2px; animation:blink 1.2s infinite; }
.typing-dot:nth-child(2){ animation-delay:.2s; }
.typing-dot:nth-child(3){ animation-delay:.4s; }
@keyframes blink { 0%,80%,100%{opacity:.2} 40%{opacity:1} }
.suggestion-btn { font-size:12px; border-radius:20px; padding:4px 12px; }
.suggestion-btn:hover { background: #f0f0f8; }
</style>

<script>
let chatHistory = [];
const messagesEl = document.getElementById('chatMessages');
const inputEl    = document.getElementById('chatInput');
const sendBtn    = document.getElementById('sendBtn');

// CSRF token — read from meta tag or cookie
function getCsrf() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta && meta.content) return meta.content;
    // fallback: read from XSRF-TOKEN cookie (Laravel sets this automatically)
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function appendMsg(role, html) {
    const row = document.createElement('div');
    row.className = 'msg-row ' + role;

    if (role === 'ai') {
        const avatar = document.createElement('div');
        avatar.className = 'ai-avatar';
        avatar.innerHTML = '<i class="fas fa-robot text-white" style="font-size:12px"></i>';
        row.appendChild(avatar);
    }

    const bubble = document.createElement('div');
    bubble.className = role === 'user' ? 'msg-bubble-user' : 'msg-bubble-ai';
    bubble.innerHTML  = html;
    row.appendChild(bubble);

    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return bubble;
}

function showTyping() {
    const row = document.createElement('div');
    row.className = 'msg-row ai';
    row.id = 'typingRow';
    const avatar = document.createElement('div');
    avatar.className = 'ai-avatar';
    avatar.innerHTML = '<i class="fas fa-robot text-white" style="font-size:12px"></i>';
    row.appendChild(avatar);
    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble-ai';
    bubble.innerHTML = '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>';
    row.appendChild(bubble);
    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
}

function removeTyping() {
    const el = document.getElementById('typingRow');
    if (el) el.remove();
}

function formatReply(text) {
    return text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
}

function setStatus(online) {
    const badge = document.getElementById('statusBadge');
    if (online) {
        badge.style.background = '#dcfce7';
        badge.style.color = '#16a34a';
        badge.innerHTML = '<i class="fas fa-circle me-1" style="font-size:8px"></i>En ligne';
    } else {
        badge.style.background = '#fee2e2';
        badge.style.color = '#dc2626';
        badge.innerHTML = '<i class="fas fa-circle me-1" style="font-size:8px"></i>Hors ligne';
    }
}

async function sendMessage() {
    const msg = inputEl.value.trim();
    if (!msg) return;

    // Hide suggestions after first message
    document.getElementById('suggestions').style.display = 'none';

    inputEl.value = '';
    sendBtn.disabled = true;
    inputEl.disabled = true;

    appendMsg('user', msg.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'));
    showTyping();

    try {
        const res = await fetch('/assistant/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrf(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ message: msg, history: chatHistory }),
        });

        removeTyping();

        // Handle 419 CSRF expired — try to refresh and retry once
        if (res.status === 419) {
            try {
                // Refresh the CSRF token by hitting a lightweight endpoint
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
            } catch(_) {}
            appendMsg('ai', '⚠️ انتهت صلاحية الجلسة (CSRF). حاول تعاود تحديث الصفحة (F5).');
            setStatus(false);
            sendBtn.disabled = false; inputEl.disabled = false; inputEl.focus();
            return;
        }

        // Parse JSON safely
        let data;
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            // Server returned HTML (redirect, error page, etc.)
            const bodyText = await res.text();
            console.error('Non-JSON response from server:', res.status, bodyText.substring(0, 500));
            if (res.status === 302 || bodyText.includes('login') || bodyText.includes('redirect')) {
                appendMsg('ai', '⚠️ انتهات جلستك. <a href="/login" style="color:#4f46e5">اضغط هنا تعاود تسجيل الدخول</a>.');
            } else {
                appendMsg('ai', '⚠️ السيرفر رجع جواب غير متوقع (HTTP ' + res.status + '). راجع الـ logs ديال Laravel.');
            }
            setStatus(false);
            sendBtn.disabled = false; inputEl.disabled = false; inputEl.focus();
            return;
        }

        let rawBody = '';
        try {
            rawBody = await res.text();
            // Strip BOM and any leading comment lines before JSON
            const cleaned = rawBody.replace(/^\uFEFF/, '').replace(/^\s*\/\/[^\n]*\n/gm, '').trim();
            data = JSON.parse(cleaned);
        } catch(jsonErr) {
            console.error('JSON parse error. Raw body was:', rawBody.substring(0, 800));
            appendMsg('ai', '⚠️ خطأ داخلي. راجع Laravel logs.');
            sendBtn.disabled = false; inputEl.disabled = false; inputEl.focus();
            return;
        }

        // Handle auth errors (401)
        if (res.status === 401) {
            const redirectUrl = data.redirect || '/login';
            appendMsg('ai', '⚠️ انتهات جلستك. <a href="' + redirectUrl + '" style="color:#4f46e5">اضغط هنا تعاود تسجيل الدخول</a>.');
            setStatus(false);
            sendBtn.disabled = false; inputEl.disabled = false; inputEl.focus();
            return;
        }

        if (!res.ok) {
            const errMsg = data.message || data.error || data.reply || ('HTTP ' + res.status);
            appendMsg('ai', '⚠️ خطأ: ' + errMsg);
        } else {
            setStatus(true);
            const reply = data.reply || '⚠️ ما وصلتش جواب.';
            appendMsg('ai', formatReply(reply));
            chatHistory.push({ role: 'user', content: msg });
            chatHistory.push({ role: 'assistant', content: reply });
            if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);
        }
    } catch (e) {
        removeTyping();
        console.error('Fetch error:', e);
        appendMsg('ai', '⚠️ خطأ في الاتصال: ' + e.message);
        setStatus(false);
    }

    sendBtn.disabled = false;
    inputEl.disabled = false;
    inputEl.focus();
}

function sendSuggestion(btn) {
    inputEl.value = btn.textContent.trim().replace(/^[^\s]+\s/, '');
    sendMessage();
}

function clearChat() {
    chatHistory = [];
    messagesEl.innerHTML = `
        <div class="msg-row ai">
            <div class="ai-avatar"><i class="fas fa-robot text-white" style="font-size:12px"></i></div>
            <div class="msg-bubble-ai">تم مسح المحادثة. كيفاش نقدر نعاونك؟ 😊</div>
        </div>`;
    document.getElementById('suggestions').style.display = 'flex';
    setStatus(true);
}
</script>
@endsection
