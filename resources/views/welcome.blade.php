<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antigravity Private Chat Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        @keyframes subtle-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .float-animation {
            animation: subtle-float 6s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full bg-[#0a0910] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-x-hidden selection:bg-purple-500/30 selection:text-purple-200">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-45">
        <div class="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-radial from-violet-600/25 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[30%] -right-[10%] w-[70%] h-[70%] rounded-full bg-radial from-pink-600/20 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute top-[25%] left-[50%] -translate-x-1/2 w-[60%] h-[60%] rounded-full bg-radial from-blue-600/10 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Header / Nav -->
    <header class="w-full px-6 py-5 border-b border-white/5 bg-black/10 backdrop-blur-md sticky top-0 z-50">
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
                    <p class="text-[9px] text-gray-500 font-medium tracking-wider uppercase">Laravel Reverb WebSockets</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs px-4 py-2 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white font-semibold shadow-lg shadow-violet-600/25 transition-all duration-200">
                        Enter Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs px-3.5 py-2 rounded-xl border border-white/10 hover:bg-white/5 text-gray-300 font-semibold transition-colors duration-200">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="text-xs px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold transition-colors duration-200">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Landing Showcase -->
    <main class="flex-1 flex flex-col justify-center items-center py-16 px-6">
        
        <!-- Hero Section -->
        <div class="max-w-3xl text-center flex flex-col items-center mb-16 float-animation">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-violet-500/10 border border-violet-500/20 text-violet-300 mb-6">
                <span class="w-1 h-1 rounded-full bg-violet-400"></span>
                Now Live on WebSockets
            </span>

            <h2 class="text-4xl sm:text-6xl font-black tracking-tight text-white mb-6 leading-none">
                Instant. Secure.<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-pink-300 to-indigo-300">
                    Real-Time Chat.
                </span>
            </h2>

            <p class="text-sm sm:text-base text-gray-400 max-w-xl leading-relaxed mb-8 font-medium">
                A next-generation private messaging platform. Connect securely, send direct invitation requests, and experience sub-millisecond chat speeds.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-3.5 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-sm font-bold text-white shadow-xl shadow-violet-600/35 transition-all duration-200">
                        Open Chat Lobby
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3.5 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-sm font-bold text-white shadow-xl shadow-violet-600/35 transition-all duration-200">
                        Start Chatting
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-xl border border-white/10 hover:border-white/20 hover:bg-white/5 text-sm font-bold text-gray-300 transition-all duration-200">
                        Sign In to Profile
                    </a>
                @endauth
            </div>
        </div>

        <!-- Features Grid -->
        <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-12 border-t border-white/5">
            <!-- Card 1 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-xl bg-[#12111a]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Reverb WebSockets</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Broadcast messages instantly with sub-millisecond latency using self-hosted socket infrastructure.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-xl bg-[#12111a]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Private Channels</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">All chat operations are protected by private socket channels for total security and data segregation.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-xl bg-[#12111a]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Connection Handshake</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Users must send and accept chat requests before they can messaging, ensuring a spam-free inbox.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-xl bg-[#12111a]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Flutter Compatible</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Stateless token APIs are optimized to support both browser web clients and Flutter mobile applications.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-6 text-center text-xs text-gray-600 border-t border-white/5 bg-black/10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                © 2026 Antigravity Platform. All rights reserved.
            </div>
            <div class="flex gap-4">
                <span class="text-gray-500">Broadcaster: Reverb WebSockets</span>
                <span class="text-gray-500">PHP: v8.4</span>
            </div>
        </div>
    </footer>
</body>
</html>
