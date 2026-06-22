<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antigravity Chat - Instant Real-Time Messaging</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }
        .mockup-float {
            animation: float 6s infinite ease-in-out;
        }

        .gradient-glow {
            box-shadow: 0 0 50px -10px rgba(124, 58, 237, 0.3);
        }
    </style>
</head>
<body class="h-full bg-[#09080f] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-x-hidden selection:bg-purple-500/30 selection:text-purple-200">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-radial from-violet-600/20 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[30%] -right-[10%] w-[70%] h-[70%] rounded-full bg-radial from-pink-600/15 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute top-[25%] left-[50%] -translate-x-1/2 w-[60%] h-[60%] rounded-full bg-radial from-blue-600/10 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Header / Nav -->
    <header class="w-full px-6 py-5 border-b border-white/5 bg-black/10 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400">
                        Antigravity Chat
                    </h1>
                    <p class="text-[9px] text-gray-500 font-bold tracking-widest uppercase">Secure WebSockets</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs px-4 py-2 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold shadow-lg shadow-violet-600/20 transition-all duration-200">
                        Enter Chat Console
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs px-4 py-2 rounded-xl border border-white/10 hover:bg-white/5 text-gray-300 font-bold transition-all duration-200">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="text-xs px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold shadow-lg shadow-violet-600/20 transition-all duration-200">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Landing Showcase -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-12 lg:py-20 flex flex-col gap-20">
        
        <!-- Hero Grid Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Headline Area -->
            <div class="lg:col-span-7 text-left flex flex-col items-start">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-violet-500/10 border border-violet-500/20 text-violet-300 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                    Powered by Laravel Reverb
                </span>

                <h2 class="text-4xl sm:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                    Instant Secure<br>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-pink-400 to-indigo-300">
                        Private Messaging.
                    </span>
                </h2>

                <p class="text-sm sm:text-base text-gray-400 leading-relaxed mb-8 font-medium max-w-lg">
                    A premium chatting platform designed for speed and confidentiality. Connect securely with peers, approve connection invitations, and chat in real-time over lightning-fast WebSockets.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-sm font-bold text-white shadow-xl shadow-violet-600/20 transition-all duration-200">
                            Open Chat Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-sm font-bold text-white shadow-xl shadow-violet-600/20 transition-all duration-200">
                            Get Started Free
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl border border-white/10 hover:border-white/20 hover:bg-white/5 text-sm font-bold text-gray-300 transition-all duration-200">
                            Sign In to Account
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Visual Mockup Area -->
            <div class="lg:col-span-5 flex justify-center items-center mockup-float">
                <div class="w-full max-w-md bg-[#13121c]/90 border border-white/10 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-md gradient-glow">
                    <!-- Mock Header -->
                    <div class="px-4 py-3.5 border-b border-white/5 bg-black/20 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-violet-500 to-pink-500 text-white flex items-center justify-center font-bold text-xs shadow-md">
                                AV
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Alice Vance</h4>
                                <p class="text-[9px] text-emerald-400 font-extrabold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    online
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                        </div>
                    </div>
                    <!-- Mock Messages -->
                    <div class="p-4 space-y-4 min-h-[240px] flex flex-col justify-end text-left bg-black/10">
                        <!-- Message Left -->
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-[8px] text-gray-500 px-1">1:34 PM</span>
                            <div class="bg-white/5 border border-white/5 text-gray-200 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs max-w-[80%] leading-relaxed shadow-sm">
                                Hey Bob! Did you set up the WebSocket server?
                            </div>
                        </div>
                        <!-- Message Right -->
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[8px] text-gray-500 px-1">1:35 PM</span>
                            <div class="bg-violet-600/20 border border-violet-500/20 text-gray-100 rounded-2xl rounded-tr-none px-3.5 py-2 text-xs max-w-[80%] leading-relaxed shadow-sm">
                                Yes! Just integrated Laravel Reverb. The direct messages deliver in sub-milliseconds! 🚀
                            </div>
                        </div>
                        <!-- Message Left -->
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-[8px] text-gray-500 px-1">1:35 PM</span>
                            <div class="bg-white/5 border border-white/5 text-gray-200 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs max-w-[80%] leading-relaxed shadow-sm">
                                Wow, this is incredibly fast! And it's completely secure.
                            </div>
                        </div>
                    </div>
                    <!-- Mock Input -->
                    <div class="p-3 border-t border-white/5 bg-black/20 flex gap-2.5 items-center">
                        <div class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5 text-[10px] text-gray-500">
                            Type a private message...
                        </div>
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                            <svg class="w-4 h-4 translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-12 border-t border-white/5">
            <!-- Card 1 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-3xl bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-2xl bg-[#12111b]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Reverb WebSockets</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Sub-millisecond latency messaging using Laravel's native, high-performance socket server.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-3xl bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-2xl bg-[#12111b]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Private Handshake</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">No unsolicited spam. Establish secure connections via a dedicated request approval handshake.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-3xl bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-2xl bg-[#12111b]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Private Channels</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Authorized broadcasts only. Real-time updates route strictly through private WebSocket channels.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-3xl bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-15 blur transition duration-300"></div>
                <div class="relative rounded-2xl bg-[#12111b]/60 border border-white/5 p-6 backdrop-blur-sm flex flex-col gap-3 min-h-[170px] transition-all group-hover:border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Cross-Platform Ready</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Fully decoupled stateless token APIs designed for both web browser interfaces and Flutter mobile applications.</p>
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
