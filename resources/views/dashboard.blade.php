<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chat Console - Antigravity Engine</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-animate {
            animation: slide-up 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-[#08070d] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-hidden selection:bg-purple-500/30 selection:text-purple-200">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-25">
        <div class="absolute -top-[30%] -left-[10%] w-[60%] h-[60%] rounded-full bg-radial from-violet-600/35 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[30%] -right-[10%] w-[60%] h-[60%] rounded-full bg-radial from-pink-600/25 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Main Outer Container -->
    <div class="h-full flex overflow-hidden w-full relative">
        
        <!-- SIDEBAR PANEL (Left - Width 350px) -->
        <aside class="w-full sm:w-[360px] flex-shrink-0 border-r border-white/5 bg-[#0b0a12]/80 backdrop-blur-xl flex flex-col justify-between h-full relative z-20">
            
            <!-- Sidebar Header -->
            <div class="px-5 py-4 border-b border-white/5 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-tr from-violet-500 to-indigo-600 shadow-md">
                            <svg class="h-4.5 w-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-black tracking-tight text-white">Antigravity Chat</h2>
                    </div>

                    <!-- Connection Status Badge -->
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-bold bg-white/5 border border-white/10 text-gray-300">
                        <span id="socket-status-dot" class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span id="socket-status-text">WS Offline</span>
                    </span>
                </div>

                <!-- Search Input Bar -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        id="user-search"
                        type="text"
                        placeholder="Search users..."
                        class="w-full pl-9 pr-4 py-2 bg-white/5 border border-white/5 hover:border-white/10 focus:border-violet-500/50 rounded-xl text-xs text-white placeholder-gray-500 focus:outline-none transition-all duration-200"
                    />
                </div>

                <!-- Tabs Switcher -->
                <div class="grid grid-cols-3 gap-1 bg-black/35 p-1 rounded-xl border border-white/5">
                    <button id="tab-chats" onclick="switchTab('chats')" class="py-1.5 rounded-lg text-[10px] font-bold text-center cursor-pointer transition-all duration-200 bg-violet-600 text-white">
                        Chats
                    </button>
                    <button id="tab-discover" onclick="switchTab('discover')" class="py-1.5 rounded-lg text-[10px] font-bold text-center cursor-pointer transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5">
                        Discover
                    </button>
                    <button id="tab-requests" onclick="switchTab('requests')" class="py-1.5 rounded-lg text-[10px] font-bold text-center cursor-pointer relative transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5">
                        Requests
                        <span id="requests-badge" class="hidden absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[8px] font-black text-white"></span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content Viewport -->
            <div id="users-directory" class="flex-1 overflow-y-auto px-3 py-3 space-y-1.5 no-scrollbar">
                <div class="text-center text-xs text-gray-500 py-12 animate-pulse">Loading directory...</div>
            </div>

            <!-- User profile footer -->
            <div class="px-4 py-3 border-t border-white/5 bg-black/20 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-violet-500 to-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-md">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white truncate leading-none mb-0.5">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-gray-500 truncate leading-none">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Logout Action -->
                <form method="POST" action="/api/logout">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg border border-rose-500/20 text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 cursor-pointer transition-all duration-150" title="Logout">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN ACTIVE WINDOW (Right) -->
        <main class="flex-1 flex flex-col justify-between h-full bg-[#0c0b15]/40 relative z-10">
            
            <!-- Default Welcome State (When no active chat is opened) -->
            <div id="chat-welcome-state" class="flex-1 flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-violet-600/10 to-indigo-600/10 flex items-center justify-center text-violet-400 mb-4 border border-violet-500/15">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-white mb-1">Select a chat to begin</h3>
                <p class="text-xs text-gray-500 max-w-[280px]">Select a connection from your sidebar or find new users to start messaging privately.</p>
                <div class="mt-6 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-white/5 border border-white/10 text-gray-400">
                    <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    E2E Socket Connection
                </div>
            </div>

            <!-- Active Conversation Container (Hidden until user starts chat) -->
            <div id="chat-active-state" class="hidden flex-1 flex flex-col h-full overflow-hidden">
                
                <!-- Chat Partner Header -->
                <div class="px-6 py-4 border-b border-white/5 bg-[#0b0a12]/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs" id="chat-header-avatar">
                            U
                        </div>
                        <div>
                            <h4 id="chat-header-name" class="text-xs font-bold text-white leading-none mb-0.5">Active User</h4>
                            <p id="chat-header-status" class="text-[9px] text-gray-500 leading-none">Conversation secure & encrypted</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Feed -->
                <div id="chat-messages" class="flex-1 overflow-y-auto px-6 py-6 space-y-4 no-scrollbar bg-black/10">
                    <!-- Dynamic chat messages go here -->
                </div>

                <!-- Chat Input Form -->
                <form id="chat-form" class="px-6 py-4 border-t border-white/5 bg-[#0b0a12]/40 flex gap-3 items-center">
                    <input
                        id="input-message"
                        type="text"
                        required
                        maxlength="4000"
                        autocomplete="off"
                        placeholder="Type a message..."
                        class="flex-1 bg-white/5 border border-white/5 hover:border-white/10 focus:border-violet-500/50 rounded-2xl px-5 py-3 text-xs text-white placeholder-gray-500 focus:outline-none transition-all duration-200"
                    />
                    <button
                        id="btn-send-message"
                        type="submit"
                        class="flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 hover:scale-[1.03] active:scale-[0.97] transition-all duration-150 text-white shadow-md cursor-pointer"
                    >
                        <svg class="w-4 h-4 translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </main>
    </div>

    <!-- COLLAPSIBLE DEV OPERATIONS CONSOLE TRAY (Bottom Overlay) -->
    <div id="console-tray" class="fixed bottom-0 left-0 right-0 z-50 bg-[#07060b] border-t border-white/10 transition-transform duration-300 transform translate-y-full flex flex-col h-[250px] shadow-2xl">
        <div class="px-5 py-2.5 bg-black/40 flex items-center justify-between border-b border-white/5">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">WebSocket Terminal Log</span>
            </div>
            <button onclick="toggleConsole()" class="text-[9px] text-gray-500 hover:text-white font-bold cursor-pointer">
                Close Terminal
            </button>
        </div>
        <div id="console-logs" class="flex-1 p-4 text-[10px] code-font text-gray-400 overflow-y-auto space-y-1.5 no-scrollbar bg-black/20">
            <div class="text-violet-400/80">[system] Dev Terminal initialized.</div>
        </div>
    </div>

    <!-- FLOATING CONSOLE TOGGLE BUTTON -->
    <button onclick="toggleConsole()" class="fixed bottom-4 right-4 z-40 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 hover:border-white/20 transition-all duration-200 cursor-pointer shadow-lg text-[9px] font-extrabold uppercase tracking-wider">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
        </svg>
        WS Console
    </button>

    <!-- Script side handling -->
    <script>
        let activeTab = 'chats'; // 'chats', 'discover', 'requests'
        let searchQuery = '';
        let usersCache = [];
        let activeChatUserId = null;
        let authUserId = null;
        let onlineUsers = new Set();
        
        // Console tray logic
        let consoleOpen = false;
        function toggleConsole() {
            const tray = document.getElementById('console-tray');
            consoleOpen = !consoleOpen;
            if (consoleOpen) {
                tray.classList.remove('translate-y-full');
            } else {
                tray.classList.add('translate-y-full');
            }
        }

        // Switch Directory Tab
        function switchTab(tab) {
            activeTab = tab;
            const tabs = ['chats', 'discover', 'requests'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                if (t === tab) {
                    btn.className = 'py-1.5 rounded-lg text-[10px] font-bold text-center cursor-pointer transition-all duration-200 bg-violet-600 text-white';
                } else {
                    btn.className = 'py-1.5 rounded-lg text-[10px] font-bold text-center cursor-pointer transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5';
                }
            });
            renderUsersList();
        }

        // Update Active Partner Status in Chat Header
        function updateActivePartnerStatus() {
            if (!activeChatUserId) return;
            
            const isOnline = onlineUsers.has(activeChatUserId);
            const statusText = document.getElementById('chat-header-status');
            
            if (isOnline) {
                statusText.innerHTML = `<span class="inline-flex items-center gap-1 text-emerald-400 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>online</span>`;
            } else {
                statusText.innerHTML = `<span class="inline-flex items-center gap-1 text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>offline</span>`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            authUserId = {{ Auth::id() }};
            
            // UI References
            const usersDirectory = document.getElementById('users-directory');
            const chatMessagesContainer = document.getElementById('chat-messages');
            const chatHeaderName = document.getElementById('chat-header-name');
            const chatHeaderAvatar = document.getElementById('chat-header-avatar');
            const chatWelcomeState = document.getElementById('chat-welcome-state');
            const chatActiveState = document.getElementById('chat-active-state');
            const chatForm = document.getElementById('chat-form');
            const inputMessage = document.getElementById('input-message');
            const btnSendMessage = document.getElementById('btn-send-message');
            const userSearchInput = document.getElementById('user-search');
            const requestsBadge = document.getElementById('requests-badge');

            const wsDot = document.getElementById('socket-status-dot');
            const wsText = document.getElementById('socket-status-text');
            const logConsole = document.getElementById('console-logs');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Logger utility
            function addLog(message, type = 'info') {
                const time = new Date().toLocaleTimeString().split(' ')[0];
                const logEntry = document.createElement('div');
                let colorClass = 'text-gray-500';
                if (type === 'success') colorClass = 'text-emerald-400';
                if (type === 'error') colorClass = 'text-rose-400';
                if (type === 'info') colorClass = 'text-violet-400/80';
                
                logEntry.className = `${colorClass}`;
                logEntry.innerHTML = `<span class="text-gray-700">[${time}]</span> ${message}`;
                logConsole.appendChild(logEntry);
                logConsole.scrollTop = logConsole.scrollHeight;
            }

            // Fetch User Directory
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

            // Search filter handler
            userSearchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase().trim();
                renderUsersList();
            });

            // Render Contacts List
            window.renderUsersList = () => {
                usersDirectory.innerHTML = '';
                
                // 1. Filter by Search Query
                let filtered = usersCache;
                if (searchQuery !== '') {
                    filtered = filtered.filter(u => 
                        u.name.toLowerCase().includes(searchQuery) || 
                        u.email.toLowerCase().includes(searchQuery)
                    );
                }

                // 2. Filter by Active Tab
                let currentList = [];
                if (activeTab === 'chats') {
                    currentList = filtered.filter(u => u.status === 'accepted');
                } else if (activeTab === 'discover') {
                    currentList = filtered.filter(u => u.status === 'none');
                } else if (activeTab === 'requests') {
                    currentList = filtered.filter(u => u.status === 'pending_sent' || u.status === 'pending_received');
                }

                // Update Request Badge
                const pendingInvites = usersCache.filter(u => u.status === 'pending_received').length;
                if (pendingInvites > 0) {
                    requestsBadge.textContent = pendingInvites;
                    requestsBadge.classList.remove('hidden');
                } else {
                    requestsBadge.classList.add('hidden');
                }

                // Render Empty State
                if (currentList.length === 0) {
                    let message = 'No conversations started yet.';
                    if (activeTab === 'discover') message = 'No new users to explore.';
                    if (activeTab === 'requests') message = 'No pending chat requests.';
                    
                    usersDirectory.innerHTML = `<div class="text-center text-xs text-gray-600 py-10">${message}</div>`;
                    return;
                }

                currentList.forEach(user => {
                    const userDiv = document.createElement('div');
                    userDiv.className = `p-3 rounded-xl border flex flex-col gap-2 transition-all duration-200 ${
                        activeChatUserId === user.id 
                            ? 'bg-violet-600/10 border-violet-500/20' 
                            : 'bg-white/5 border-white/0 hover:border-white/5'
                    }`;

                    let actionHtml = '';

                    if (user.status === 'none') {
                        actionHtml = `
                            <button onclick="sendRequest(${user.id})" class="w-full py-1.5 text-[9px] font-bold bg-violet-600 hover:bg-violet-500 text-white rounded-lg cursor-pointer transition-colors shadow-sm">
                                Connect
                            </button>
                        `;
                    } else if (user.status === 'pending_sent') {
                        actionHtml = `
                            <button disabled class="w-full py-1.5 text-[9px] font-bold bg-white/5 border border-white/10 text-gray-500 rounded-lg cursor-not-allowed">
                                Outgoing Pending
                            </button>
                        `;
                    } else if (user.status === 'pending_received') {
                        actionHtml = `
                            <div class="flex gap-1.5 w-full">
                                <button onclick="acceptRequest(${user.request_id})" class="flex-1 py-1.5 text-[9px] font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg cursor-pointer transition-colors shadow-sm">
                                    Accept
                                </button>
                                <button onclick="declineRequest(${user.request_id})" class="flex-1 py-1.5 text-[9px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 rounded-lg cursor-pointer transition-colors">
                                    Decline
                                </button>
                            </div>
                        `;
                    } else if (user.status === 'accepted') {
                        actionHtml = `
                            <button onclick="startChat(${user.id}, '${escapeHtml(user.name)}')" class="w-full py-1.5 text-[9px] font-bold bg-violet-500/10 border border-violet-500/20 hover:bg-violet-500/20 text-violet-300 rounded-lg cursor-pointer transition-all">
                                ${activeChatUserId === user.id ? 'Open Chat' : 'Chat'}
                            </button>
                        `;
                    }

                    const isOnline = onlineUsers.has(user.id);
                    const statusDot = isOnline 
                        ? `<span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border border-[#0b0a12] shadow-sm"></span>` 
                        : '';

                    userDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-violet-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-md">
                                ${user.name.substring(0,2).toUpperCase()}
                                ${statusDot}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-white truncate">${escapeHtml(user.name)}</p>
                                <p class="text-[9px] text-gray-500 truncate">${escapeHtml(user.email)}</p>
                            </div>
                        </div>
                        <div class="pt-0.5">${actionHtml}</div>
                    `;
                    usersDirectory.appendChild(userDiv);
                });
            };

            // Actions Handlers
            window.sendRequest = async (receiverId) => {
                addLog(`Sending connection request to user #${receiverId}...`);
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
                        addLog('Request sent successfully', 'success');
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
                        addLog('Request declined', 'info');
                        fetchUsers();
                        if (activeChatUserId && usersCache.find(u => u.request_id === requestId)?.id === activeChatUserId) {
                            closeActiveChat();
                        }
                    }
                } catch (e) {
                    addLog('Network error declining request', 'error');
                }
            };

            // Switch to chat partner
            window.startChat = async (userId, name) => {
                activeChatUserId = userId;
                chatHeaderName.textContent = name;
                chatHeaderAvatar.textContent = name.substring(0, 2).toUpperCase();
                
                updateActivePartnerStatus();
                
                chatWelcomeState.classList.add('hidden');
                chatActiveState.classList.remove('hidden');
                renderUsersList(); // Update active selection
                
                chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-gray-500 py-12 animate-pulse">Loading history...</div>';
                
                await fetchMessages(userId);
            };

            function closeActiveChat() {
                activeChatUserId = null;
                chatActiveState.classList.add('hidden');
                chatWelcomeState.classList.remove('hidden');
                renderUsersList();
            }

            async function fetchMessages(userId) {
                try {
                    const res = await fetch(`/api/direct-messages/${userId}`);
                    const data = await res.json();
                    if (data.success) {
                        chatMessagesContainer.innerHTML = '';
                        if (data.messages.length === 0) {
                            chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-gray-600 py-12">No messages yet. Send a message to start conversation!</div>';
                        } else {
                            data.messages.forEach(msg => appendMessage(msg, true));
                        }
                    } else {
                        chatMessagesContainer.innerHTML = `<div class="text-center text-xs text-rose-500 py-12">${data.message}</div>`;
                    }
                } catch (e) {
                    chatMessagesContainer.innerHTML = '<div class="text-center text-xs text-rose-500 py-12">Failed to retrieve message logs</div>';
                }
            }

            function appendMessage(msg, isHistory = false) {
                const isMe = msg.sender_id === authUserId;
                const messageWrapper = document.createElement('div');
                messageWrapper.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} gap-1 ${isHistory ? '' : 'message-animate'}`;
                
                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const bubbleStyle = isMe 
                    ? 'bg-gradient-to-tr from-violet-600 to-indigo-600 text-white rounded-2xl rounded-tr-none shadow-sm' 
                    : 'bg-white/5 border border-white/5 text-gray-200 rounded-2xl rounded-tl-none shadow-sm';
                
                messageWrapper.innerHTML = `
                    <span class="text-[8px] text-gray-500 px-1.5">${time}</span>
                    <div class="px-4 py-2.5 text-xs max-w-[80%] break-words leading-relaxed ${bubbleStyle}">
                        ${escapeHtml(msg.message)}
                    </div>
                `;
                chatMessagesContainer.appendChild(messageWrapper);
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
            }

            // Submit message Form
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const messageVal = inputMessage.value.trim();
                if (!messageVal || !activeChatUserId) return;

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
                            message: messageVal
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        inputMessage.value = '';
                        appendMessage(data.message);
                        addLog('Message sent', 'success');
                    }
                } catch (e) {
                    addLog('Failed to transmit message', 'error');
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

            // Reverb WebSockets Hooks via Vite
            try {
                if (window.Echo) {
                    addLog('[WS] Reverb WebSockets active via compiled assets.', 'info');

                    // Bind connection state handlers
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

                    // Set initial state if already connected
                    if (window.Echo.connector.pusher.connection.state === 'connected') {
                        wsDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
                        wsText.textContent = 'WS Connected';
                    }

                    // Join Chat Presence Channel to track online statuses
                    window.Echo.join('chat')
                        .here((users) => {
                            users.forEach(u => onlineUsers.add(u.id));
                            addLog(`[Presence] Joined channel. ${users.length} users online.`, 'info');
                            renderUsersList();
                            updateActivePartnerStatus();
                        })
                        .joining((user) => {
                            onlineUsers.add(user.id);
                            addLog(`[Presence] ${user.name} came online.`, 'success');
                            renderUsersList();
                            updateActivePartnerStatus();
                        })
                        .leaving((user) => {
                            onlineUsers.delete(user.id);
                            addLog(`[Presence] ${user.name} went offline.`, 'info');
                            renderUsersList();
                            updateActivePartnerStatus();
                        })
                        .error((error) => {
                            addLog(`[Presence] Failed to join: ${error.message}`, 'error');
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
                } else {
                    addLog('[WS] Echo is not initialized on window object.', 'error');
                }
            } catch (e) {
                addLog(`[WS] Failed to initialize: ${e.message}`, 'error');
            }

            // Init directory load
            fetchUsers();
        });
    </script>
</body>
</html>
