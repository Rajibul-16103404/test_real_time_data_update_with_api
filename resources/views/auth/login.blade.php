<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Antigravity Engine</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-[#0a0910] text-[#f4f4f7] flex flex-col justify-between antialiased overflow-x-hidden">
    <!-- Gradient background mesh -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute -top-[40%] -left-[20%] w-[80%] h-[80%] rounded-full bg-radial from-violet-600/30 via-transparent to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[40%] -right-[20%] w-[80%] h-[80%] rounded-full bg-radial from-pink-600/20 via-transparent to-transparent blur-3xl"></div>
    </div>

    <!-- Header -->
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
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md relative group">
            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-violet-600 to-pink-600 opacity-25 blur transition duration-1000 group-hover:opacity-35"></div>

            <div class="relative w-full rounded-2xl bg-[#12111a]/80 border border-white/10 backdrop-blur-xl px-8 py-10 shadow-2xl flex flex-col">
                <h2 class="text-2xl font-bold text-center text-white mb-2">Welcome Back</h2>
                <p class="text-xs text-center text-gray-500 font-medium tracking-wide mb-8">LOGIN TO THE REAL-TIME PLATFORM</p>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <div>• {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/login" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-white/5 border border-white/10 hover:border-white/20 focus:border-violet-500/50 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none transition-all duration-200"
                            placeholder="you@example.com" />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Password</label>
                        <input id="password" type="password" name="password" required
                            class="w-full bg-white/5 border border-white/10 hover:border-white/20 focus:border-violet-500/50 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none transition-all duration-200"
                            placeholder="••••••••" />
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 mt-2 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-sm font-semibold text-white cursor-pointer shadow-lg shadow-violet-600/30">
                        Sign In
                    </button>
                </form>

                <div class="text-center mt-6 text-xs text-gray-500">
                    Don't have an account? 
                    <a href="/register" class="text-violet-400 hover:text-violet-300 font-semibold transition-colors">Register here</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-6 text-center text-xs text-gray-600 border-t border-white/5 bg-black/5">
        © 2026 Antigravity Platform. All rights reserved.
    </footer>
</body>
</html>
