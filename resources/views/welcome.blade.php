<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Real-Time Counter WebSocket Console</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .code-font {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom glow effect animations */
        @keyframes pulse-glow {
            0%, 100% {
                filter: drop-shadow(0 0 15px rgba(167, 139, 250, 0.25));
            }
            50% {
                filter: drop-shadow(0 0 30px rgba(167, 139, 250, 0.5));
            }
        }
        .counter-glow {
            animation: pulse-glow 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full bg-[#0a0910] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-x-hidden selection:bg-purple-500/30 selection:text-purple-200">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute -top-[40%] -left-[20%] w-[80%] h-[80%] rounded-full bg-radial from-violet-600/30 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[40%] -right-[20%] w-[80%] h-[80%] rounded-full bg-radial from-pink-600/20 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute top-[30%] left-[50%] -translate-x-1/2 w-[60%] h-[60%] rounded-full bg-radial from-blue-600/10 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Header / Nav -->
    <header class="w-full px-6 py-5 border-b border-white/5 bg-black/10 backdrop-blur-md">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
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
                    <p class="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Laravel Reverb WebSockets</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white/5 border border-white/10 text-gray-300">
                    <span id="socket-status-dot" class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span id="socket-status-text">WS Offline</span>
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md relative group">
            <!-- Background Glow behind the card -->
            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-25 blur transition duration-1000 group-hover:opacity-35 group-hover:duration-200"></div>

            <!-- Card Container -->
            <div class="relative w-full rounded-2xl bg-[#12111a]/80 border border-white/10 backdrop-blur-xl px-8 py-10 shadow-2xl flex flex-col items-center">
                <!-- Status Badge -->
                <div id="status-badge" class="mb-6 inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-violet-500/10 border border-violet-500/20 text-violet-300 transition-all duration-300">
                    <span id="status-dot" class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
                    <span id="status-text">Loading initial value...</span>
                </div>

                <h2 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold mb-2">Live WebSocket Counter</h2>

                <!-- Counter Display -->
                <div class="relative h-44 flex items-center justify-center select-none w-full">
                    <div id="counter-value" class="text-8xl font-black bg-clip-text text-transparent bg-gradient-to-r from-violet-200 via-pink-100 to-indigo-200 counter-glow filter drop-shadow-[0_0_15px_rgba(167,139,250,0.2)] tracking-tight">
                        --
                    </div>
                </div>

                <!-- Increment/Decrement Buttons -->
                <div class="flex items-center gap-6 w-full mt-6 justify-center">
                    <!-- Decrement Button -->
                    <button
                        id="btn-decrement"
                        disabled
                        class="flex items-center justify-center w-16 h-16 rounded-full border border-white/10 bg-white/5 hover:bg-white/10 hover:scale-105 active:scale-95 transition-all duration-200 text-white hover:text-pink-300 hover:border-pink-500/30 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white/5 disabled:hover:scale-100 cursor-pointer shadow-lg"
                        aria-label="Decrement counter"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                    </button>

                    <!-- Increment Button -->
                    <button
                        id="btn-increment"
                        disabled
                        class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 hover:scale-105 active:scale-95 transition-all duration-200 text-white disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 cursor-pointer shadow-lg shadow-violet-600/30"
                        aria-label="Increment counter"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                <!-- Database stats -->
                <div class="w-full border-t border-white/5 mt-8 pt-6 flex justify-between text-xs text-gray-500 font-semibold px-2">
                    <span>Database ID: <span class="text-gray-400 code-font">default</span></span>
                    <span>Broker: <span class="text-violet-400 font-bold">Reverb</span></span>
                </div>
            </div>
        </div>

        <!-- Terminal Console Logger -->
        <div class="w-full max-w-md mt-6 rounded-xl bg-black/40 border border-white/5 backdrop-blur-md px-5 py-4 shadow-xl">
            <div class="flex items-center justify-between mb-2 pb-2 border-b border-white/5">
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">WebSocket Operations Log</span>
                <span class="w-2 h-2 rounded-full bg-violet-500/70"></span>
            </div>
            <div id="console-logs" class="text-[11px] code-font text-gray-400 h-28 overflow-y-auto space-y-1.5 scrollbar-thin scrollbar-thumb-white/10 pr-2">
                <div class="text-violet-400/80">[system] Connecting websocket clients...</div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-6 text-center text-xs text-gray-600 border-t border-white/5 bg-black/5">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                © {{ date('Y') }} Antigravity Platform. All rights reserved.
            </div>
            <div class="flex gap-4">
                <span class="text-gray-500">Broadcaster: Reverb</span>
                <span class="text-gray-500">PHP: v8.4</span>
            </div>
        </div>
    </footer>

    <!-- WebSocket Client Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <!-- Script logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counterEl = document.getElementById('counter-value');
            const btnInc = document.getElementById('btn-increment');
            const btnDec = document.getElementById('btn-decrement');
            const statusBadge = document.getElementById('status-badge');
            const statusDot = document.getElementById('status-dot');
            const statusText = document.getElementById('status-text');
            const wsDot = document.getElementById('socket-status-dot');
            const wsText = document.getElementById('socket-status-text');
            const logConsole = document.getElementById('console-logs');

            // Add console logs in UI
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

            // Update badge UI states
            function setStatus(state) {
                if (state === 'syncing') {
                    statusBadge.className = 'mb-6 inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 border border-amber-500/20 text-amber-300 transition-all duration-300';
                    statusDot.className = 'w-2 h-2 rounded-full bg-amber-400 animate-pulse';
                    statusText.textContent = 'Syncing...';
                } else if (state === 'success') {
                    statusBadge.className = 'mb-6 inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 transition-all duration-300';
                    statusDot.className = 'w-2 h-2 rounded-full bg-emerald-400';
                    statusText.textContent = 'Synced with DB';
                } else if (state === 'error') {
                    statusBadge.className = 'mb-6 inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 border border-rose-500/20 text-rose-300 transition-all duration-300';
                    statusDot.className = 'w-2 h-2 rounded-full bg-rose-400';
                    statusText.textContent = 'Sync Error';
                }
            }

            // Disable / Enable control buttons
            function setControlsDisabled(disabled) {
                btnInc.disabled = disabled;
                btnDec.disabled = disabled;
            }

            // Fetch initial counter from API
            async function fetchCounter() {
                setStatus('syncing');
                addLog('GET /api/counter -> Fetching initial value');
                try {
                    const res = await fetch('/api/counter');
                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    const data = await res.json();
                    
                    if (data.success) {
                        counterEl.textContent = data.value;
                        setStatus('success');
                        setControlsDisabled(false);
                        addLog(`GET /api/counter -> 200 OK (value: ${data.value})`, 'success');
                    } else {
                        throw new Error('Response success flag is false');
                    }
                } catch (err) {
                    setStatus('error');
                    addLog('GET /api/counter -> FAIL (' + err.message + ')', 'error');
                }
            }

            // Send Counter Update
            async function updateCounter(action) {
                setStatus('syncing');
                setControlsDisabled(true);
                addLog(`POST /api/counter/${action} -> Requesting change`);
                
                counterEl.classList.remove('scale-100');
                counterEl.classList.add('scale-95', 'opacity-80', 'transition-all', 'duration-150');

                try {
                    const res = await fetch(`/api/counter/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    const data = await res.json();

                    if (data.success) {
                        // Optimistic update locally
                        counterEl.textContent = data.value;
                        counterEl.classList.remove('scale-95', 'opacity-80');
                        counterEl.classList.add('scale-100');

                        setStatus('success');
                        setControlsDisabled(false);
                        addLog(`POST /api/counter/${action} -> 200 OK`, 'success');
                    } else {
                        throw new Error('Response success flag is false');
                    }
                } catch (err) {
                    setStatus('error');
                    setControlsDisabled(false);
                    counterEl.classList.remove('scale-95', 'opacity-80');
                    addLog(`POST /api/counter/${action} -> FAIL (` + err.message + ')', 'error');
                }
            }

            btnInc.addEventListener('click', () => updateCounter('increment'));
            btnDec.addEventListener('click', () => updateCounter('decrement'));

            // Initialize Laravel Echo for Reverb WebSockets
            try {
                window.Pusher = Pusher;
                
                // Use current location hostname dynamically to support localhost, local IPs, and proxies
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

                // Listen to connection status events
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    wsDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
                    wsText.textContent = 'WS Connected';
                    addLog('[WS] Connection established successfully', 'success');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    wsDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
                    wsText.textContent = 'WS Offline';
                    addLog('[WS] Connection disconnected', 'error');
                });

                window.Echo.connector.pusher.connection.bind('error', (err) => {
                    addLog(`[WS] Connection error: ${err.message || 'unknown'}`, 'error');
                });

                // Listen on public channel for live broadcast updates
                window.Echo.channel('counter')
                    .listen('CounterUpdated', (e) => {
                        addLog(`[Broadcast] Received CounterUpdated (value: ${e.value})`, 'info');
                        
                        // Dynamic value updating and micro-animation
                        counterEl.textContent = e.value;
                        counterEl.classList.remove('scale-95');
                        counterEl.classList.add('scale-105');
                        
                        setTimeout(() => {
                            counterEl.classList.remove('scale-105');
                            counterEl.classList.add('scale-100');
                        }, 150);

                        setStatus('success');
                    });

            } catch (e) {
                addLog(`[WS] Failed to initialize Echo: ${e.message}`, 'error');
            }

            // Load initial state
            fetchCounter();
        });
    </script>
</body>
</html>
