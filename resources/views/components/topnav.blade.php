<nav class="w-full bg-[#0f172a]/80 backdrop-blur-lg border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo -->
        <a href="/" class="text-2xl font-extrabold text-blue-400 hover:text-blue-300 transition">
            itz-shaidsk
        </a>

        <!-- Links -->
        <div class="flex items-center gap-6 text-white">
            <a href="/" class="hover:text-blue-300 transition">Home</a>
            <a href="/dashboard" class="hover:text-blue-300 transition">Dashboard</a>
            <a href="/goals" class="hover:text-blue-300 transition">Goals</a>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-4">
            @auth
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-600 text-white font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-white">{{ Auth::user()->name }}</span>
                </div>
            @else
                <a href="/login" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition">
                    Login
                </a>
                <a href="/register" class="px-4 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-600 transition">
                    Register
                </a>
            @endauth
        </div>

    </div>
</nav>
