<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Private Chat Console - Antigravity Engine</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .code-font { font-family: 'JetBrains Mono', monospace; }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-animate {
            animation: slide-up 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Hide scrollbars but keep functionality */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-[#0a0910] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-x-hidden selection:bg-purple-500/30 selection:text-purple-200">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute -top-[40%] -left-[20%] w-[80%] h-[80%] rounded-full bg-radial from-violet-600/30 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[40%] -right-[20%] w-[80%] h-[80%] rounded-full bg-radial from-pink-600/20 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Header / Nav -->
    <header class="w-full px-6 py-4 border-b border-white/5 bg-black/20 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-violet-500 to-pink-500 shadow-lg shadow-violet-500/25">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                        Antigravity Engine
                    </h1>
                    <p class="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Private Secure Messaging</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- User details -->
                <div class="flex items-center gap-2 bg-white/5 border border-white/15 px-3 py-1.5 rounded-xl">
                    <div class="w-2 h-2 rounded-full bg-violet-400"></div>
                    <span class="text-xs font-semibold text-gray-200">{{ Auth::user()->name }}</span>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-white/5 border border-white/10 text-gray-300">
                    <span id="socket-status-dot" class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span id="socket-status-text" class="text-[10px]">WS Offline</span>
                </span>

                <!-- Logout -->
                <form method="POST" action="/api/logout">
                    @csrf
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-xl border border-rose-500/30 text-rose-400 hover:bg-rose-500/10 cursor-pointer transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- COLUMN 1: User Directory List & Logs (Col Span 4) -->
        <div class="lg:col-span-4 flex flex-col gap-6 relative group">
            <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-15 blur"></div>
            <div class="relative w-full h-full rounded-xl bg-[#12111a]/80 border border-white/10 backdrop-blur-xl px-4 py-5 shadow-xl flex flex-col justify-between gap-6 min-h-[500px]">
                
                <!-- Users Viewport -->
                <div class="flex flex-col flex-1 min-h-0">
                    <h3 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-4 px-1">Directory</h3>
                    <div id="users-directory" class="flex-1 overflow-y-auto space-y-2 pr-1 no-scrollbar">
                        <div class="text-center text-xs text-gray-500 py-8">Loading users...</div>
                    </div>
                </div>

                <!-- WebSockets log, relocated here -->
                <div class="w-full rounded-xl bg-black/40 border border-white/5 backdrop-blur-md px-4 py-3 shadow-md flex flex-col justify-between min-h-[160px] max-h-[220px]">
                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-white/5">
                        <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider">WebSocket Operations Log</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500/70"></span>
                    </div>
                    <div id="console-logs" class="flex-1 text-[9px] code-font text-gray-400 h-28 overflow-y-auto space-y-1 pr-1 no-scrollbar">
                        <div class="text-violet-400/80">[system] Connecting websocket clients...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMN 2: Private Chat Room (Col Span 8) -->
        <div class="lg:col-span-8 flex flex-col relative group">
            <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-pink-600 to-violet-600 opacity-20 blur"></div>
            <div class="relative w-full h-full min-h-[500px] rounded-xl bg-[#12111a]/80 border border-white/10 backdrop-blur-xl px-6 py-6 shadow-xl flex flex-col justify-between">
                
                <!-- Active Chat Partner Header -->
                <div class="border-b border-white/5 pb-4 mb-4 flex items-center justify-between">
                    <div>
                        <h4 id="chat-header-name" class="text-sm font-bold text-white">Select a Partner</h4>
                        <p id="chat-header-status" class="text-[10px] text-gray-500">Pick a connected user from the directory to start</p>
                    </div>
                </div>

                <!-- Chat Feed Viewport -->
                <div id="chat-messages" class="flex-1 overflow-y-auto space-y-3.5 pr-2 mb-4 no-scrollbar min-h-[320px]">
                    <div class="h-full flex flex-col items-center justify-center text-center p-8">
                        <div class="w-12 h-12 rounded-full bg-violet-600/10 flex items-center justify-center text-violet-400 mb-3 border border-violet-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-400 font-semibold">Private Chat Room</p>
                        <p class="text-[10px] text-gray-600 mt-1 max-w-[200px]">Send connection requests to other users and chat privately once accepted.</p>
                    </div>
                </div>

                <!-- Chat Input Form -->
                <form id="chat-form" class="hidden flex gap-2.5 items-center pt-3 border-t border-white/5">
                    <input
                        id="input-message"
                        type="text"
                        required
                        maxlength="4000"
                        autocomplete="off"
                        placeholder="Type a private message..."
                        class="flex-1 bg-white/5 border border-white/10 hover:border-white/20 focus:border-violet-500/50 rounded-xl px-4 py-3.5 text-xs text-white placeholder-gray-500 focus:outline-none transition-all duration-200"
                    />
                    <button
                        id="btn-send-message"
                        type="submit"
                        class="flex items-center justify-center h-11 w-11 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 hover:scale-105 active:scale-95 transition-all duration-200 text-white cursor-pointer"
                    >
                        <svg class="w-4 h-4 translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 text-center text-[10px] text-gray-600 border-t border-white/5 bg-black/10">
        © 2026 Antigravity Platform. All rights reserved.
    </footer>

    <!-- WebSocket Client Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <!-- Client-side logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const authUserId = {{ Auth::id() }};
            let activeChatUserId = null;
            let usersCache = [];

            // UI Elements
            const usersDirectory = document.getElementById('users-directory');
            const chatMessagesContainer = document.getElementById('chat-messages');
            const chatHeaderName = document.getElementById('chat-header-name');
            const chatHeaderStatus = document.getElementById('chat-header-status');
            const chatForm = document.getElementById('chat-form');
            const inputMessage = document.getElementById('input-message');
            const btnSendMessage = document.getElementById('btn-send-message');

            const wsDot = document.getElementById('socket-status-dot');
            const wsText = document.getElementById('socket-status-text');
            const logConsole = document.getElementById('console-logs');

            // CSRF helper
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Logger helper
            function addLog(message, type = 'info') {
                const time = new Date().toLocaleTimeString().split(' ')[0];
                const logEntry = document.createElement('div');
                let colorClass = 'text-gray-400';
                if (type === 'success') colorClass = 'text-emerald-400';
                if (type === 'error') colorClass = 'text-rose-400';
                if (type === 'info') colorClass = 'text-violet-400/80';
                
                logEntry.className = `${colorClass}`;
                logEntry.innerHTML = `<span class="text-gray-600">[${time}]</span> ${message}`;
                logConsole.appendChild(logEntry);
                logConsole.scrollTop = logConsole.scrollHeight;
            }

            // Fetch User Directory List
            async function fetchUsers() {
                try {
                    const res = await fetch('/api/users', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        usersCache = data.users;
                        renderUsersList();
                    }
                } catch (e) {
                    addLog('Failed to fetch user directory', 'error');
                }
            }

            // Render Users list with statuses
            function renderUsersList() {
                usersDirectory.innerHTML = '';
                if (usersCache.length === 0) {
                    usersDirectory.innerHTML = '<div class="text-center text-xs text-gray-600 py-6">No other users registered yet.</div>';
                    return;
                }

                usersCache.forEach(user => {
                    const userDiv = document.createElement('div');
                    userDiv.className = `p-3 rounded-xl border flex flex-col gap-2 transition-all duration-200 ${
                        activeChatUserId === user.id 
                            ? 'bg-violet-600/10 border-violet-500/30' 
                            : 'bg-white/5 border-white/5 hover:border-white/10'
                    }`;

                    let actionHtml = '';

                    if (user.status === 'none') {
                        actionHtml = `
                            <button onclick="sendRequest(${user.id})" class="w-full py-1 text-[10px] font-bold bg-violet-600 hover:bg-violet-500 text-white rounded-lg cursor-pointer transition-colors">
                                Connect
                            </button>
                        `;
                    } else if (user.status === 'pending_sent') {
                        actionHtml = `
                            <button disabled class="w-full py-1 text-[10px] font-bold bg-white/5 border border-white/10 text-gray-500 rounded-lg cursor-not-allowed">
                                Request Pending
                            </button>
                        `;
                    } else if (user.status === 'pending_received') {
                        actionHtml = `
                            <div class="flex gap-1.5 w-full">
                                <button onclick="acceptRequest(${user.request_id})" class="flex-1 py-1 text-[10px] font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg cursor-pointer transition-colors">
                                    Accept
                                </button>
                                <button onclick="declineRequest(${user.request_id})" class="flex-1 py-1 text-[10px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 rounded-lg cursor-pointer transition-colors">
                                    Decline
                                </button>
                            </div>
                        `;
                    } else if (user.status === 'accepted') {
                        actionHtml = `
                            <button onclick="startChat(${user.id}, '${escapeHtml(user.name)}')" class="w-full py-1 text-[10px] font-bold bg-violet-500/10 border border-violet-500/30 hover:bg-violet-500/20 text-violet-300 rounded-lg cursor-pointer transition-all">
                                ${activeChatUserId === user.id ? 'Active Chat' : 'Open Chat'}
                            </button>
                        `;
                    }

                    userDiv.innerHTML = `
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-violet-500 to-pink-500 text-white flex items-center justify-center font-bold text-xs">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-white truncate">${escapeHtml(user.name)}</p>
                                <p class="text-[9px] text-gray-500 truncate">${escapeHtml(user.email)}</p>
                            </div>
                        </div>
                        <div class="pt-1">${actionHtml}</div>
                    `;
                    usersDirectory.appendChild(userDiv);
                });
            }

            // Connection actions
            window.sendRequest = async (receiverId) => {
                addLog(`Sending chat request to user #${receiverId}...`);
                try {
                    const res = await fetch('/api/chat-requests', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ receiver_id: receiverId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        addLog('Chat request sent successfully', 'success');
                        fetchUsers();
                    } else {
                        addLog(data.message || 'Failed to send request', 'error');
                    }
                } catch (e) {
                    addLog('Network error sending request', 'error');
                }
            };

            window.acceptRequest = async (requestId) => {
                addLog(`Accepting request #${requestId}...`);
                try {
                    const res = await fetch(`/api/chat-requests/${requestId}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        addLog('Chat request accepted', 'success');
                        fetchUsers();
                    }
                } catch (e) {
                    addLog('Network error accepting request', 'error');
                }
            };

            window.declineRequest = async (requestId) => {
                addLog(`Declining request #${requestId}...`);
                try {
                    const res = await fetch(`/api/chat-requests/${requestId}/decline`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        addLog('Request declined/deleted', 'info');
                        fetchUsers();
                        if (activeChatUserId && usersCache.find(u => u.request_id === requestId)?.id === activeChatUserId) {
                            closeActiveChat();
                        }
                    }
                } catch (e) {
                    addLog('Network error declining request', 'error');
                }
            };

            // Chat room handling
            window.startChat = async (userId, name) => {
                activeChatUserId = userId;
                chatHeaderName.textContent = name;
                chatHeaderStatus.textContent = 'Conversation is secure and encrypted';
                chatForm.classList.remove('hidden');
                renderUsersList(); // Update active selection style
                
                chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-gray-500 py-8 animate-pulse">Loading messages...</div>';
                
                await fetchMessages(userId);
            };

            function closeActiveChat() {
                activeChatUserId = null;
                chatHeaderName.textContent = 'Select a Partner';
                chatHeaderStatus.textContent = 'Pick a connected user from the directory to start';
                chatForm.classList.add('hidden');
                chatMessagesContainer.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center p-8">
                        <div class="w-12 h-12 rounded-full bg-violet-600/10 flex items-center justify-center text-violet-400 mb-3 border border-violet-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-400 font-semibold">Connection Terminated</p>
                    </div>
                `;
                renderUsersList();
            }

            async function fetchMessages(userId) {
                try {
                    const res = await fetch(`/api/direct-messages/${userId}`);
                    const data = await res.json();
                    if (data.success) {
                        chatMessagesContainer.innerHTML = '';
                        if (data.messages.length === 0) {
                            chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-gray-600 py-8">Connected! Write a message to begin.</div>';
                        } else {
                            data.messages.forEach(msg => appendMessage(msg, true));
                        }
                    } else {
                        chatMessagesContainer.innerHTML = `<div class="text-center text-xs text-rose-500 py-8">${data.message}</div>`;
                    }
                } catch (e) {
                    chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-rose-500 py-8">Failed to load message history</div>';
                }
            }

            function appendMessage(msg, isHistory = false) {
                const isMe = msg.sender_id === authUserId;
                const messageEl = document.createElement('div');
                messageEl.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} gap-1 ${isHistory ? '' : 'message-animate'}`;
                
                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const bubbleBg = isMe ? 'bg-violet-600/20 border-violet-500/20 text-gray-100' : 'bg-white/5 border-white/5 text-gray-200';
                
                messageEl.innerHTML = `
                    <span class="text-[8px] text-gray-500 px-1">${time}</span>
                    <div class="border rounded-2xl ${isMe ? 'rounded-tr-none' : 'rounded-tl-none'} px-3.5 py-2 text-xs max-w-[85%] break-words leading-relaxed shadow-sm ${bubbleBg}">
                        ${escapeHtml(msg.message)}
                    </div>
                `;
                chatMessagesContainer.appendChild(messageEl);
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
            }

            // Send message submit
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = inputMessage.value.trim();
                if (!text || !activeChatUserId) return;

                inputMessage.disabled = true;
                btnSendMessage.disabled = true;

                try {
                    const res = await fetch('/api/direct-messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            receiver_id: activeChatUserId,
                            message: text
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        inputMessage.value = '';
                        appendMessage(data.message);
                        addLog('Private message sent', 'success');
                    }
                } catch (e) {
                    addLog('Error sending message', 'error');
                } finally {
                    inputMessage.disabled = false;
                    btnSendMessage.disabled = false;
                    inputMessage.focus();
                }
            });

            // Escape HTML helper
            function escapeHtml(str) {
                const temp = document.createElement('div');
                temp.textContent = str;
                return temp.innerHTML;
            }

            // Reverb WebSockets Initialize
            try {
                window.Pusher = Pusher;
                const wsHost = window.location.hostname;
                const wsPort = {{ env('REVERB_PORT', 8080) }};

                addLog(`[WS] Initializing connection to ws://${wsHost}:${wsPort}...`);

                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: '{{ env("REVERB_APP_KEY") }}',
                    wsHost: wsHost,
                    wsPort: wsPort,
                    wssPort: wsPort,
                    forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                });

                window.Echo.connector.pusher.connection.bind('connected', () => {
                    wsDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
                    wsText.textContent = 'WS Connected';
                    addLog('[WS] Connected successfully', 'success');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    wsDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
                    wsText.textContent = 'WS Offline';
                    addLog('[WS] Disconnected', 'error');
                });

                // Listen to Private User channel
                window.Echo.private(`user.${authUserId}`)
                    .listen('ChatRequestSent', (e) => {
                        addLog(`[WS] Incoming chat invitation from "${e.sender.name}"`, 'info');
                        fetchUsers();
                    })
                    .listen('ChatRequestAccepted', (e) => {
                        addLog(`[WS] Connection invitation accepted by "${e.receiver.name}"!`, 'success');
                        fetchUsers();
                    })
                    .listen('DirectMessageSent', (e) => {
                        addLog(`[WS] New private message received`, 'info');
                        if (activeChatUserId === e.sender_id) {
                            appendMessage(e);
                        } else {
                            // Update sidebar statuses dynamically
                            fetchUsers();
                        }
                    });

            } catch (e) {
                addLog(`[WS] Failed to initialize: ${e.message}`, 'error');
            }

            // Init loads
            fetchUsers();
        });
    </script>
</body>
</html>
