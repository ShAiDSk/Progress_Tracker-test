<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', "Shaid's Page") }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* global visual system for the app layout */
        :root{--glass-bg: rgba(255,255,255,0.04);--glass-border: rgba(255,255,255,0.06);} 

        body { background: linear-gradient(180deg,#041226 0%, #05122b 100%); }

        /* floating glass navbar */
        .site-nav { backdrop-filter: blur(8px) saturate(120%); background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border-bottom: 1px solid rgba(255,255,255,0.04);} 

        .brand { color: #60a5fa; font-weight:800; letter-spacing:-0.5px; }

        /* avatar */
        .avatar { width:36px; height:36px; border-radius:9999px; background:linear-gradient(135deg,#3b82f6,#7c3aed); display:inline-flex; align-items:center; justify-content:center; font-weight:700; color:white; }

        /* dropdown */
        .dropdown { min-width:180px; background: rgba(6,10,15,0.7); border: 1px solid rgba(255,255,255,0.04); backdrop-filter: blur(8px); }

        /* nice small helpers */
        .glass-card { background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.04); }

        /* mobile nav */
        .menu-hidden{ display:none; }

        /* subtle glow for headings */
        .glow { text-shadow: 0 6px 24px rgba(59,130,246,0.12); }

        /* small responsive tweaks */
        @media (max-width: 768px){ .desktop-links{ display:none; } .menu-hidden{ display:block; } }
    </style>
</head>
<body class="min-h-screen text-white antialiased">

    <!-- NAVBAR -->
    <header class="fixed top-4 left-1/2 -translate-x-1/2 w-[94%] max-w-7xl z-50 rounded-2xl site-nav shadow-lg px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="brand text-xl md:text-2xl">{{ config('app.name', "Shaid's Page") }}</a>

            <nav class="hidden md:flex items-center gap-4 desktop-links text-sm text-white/80">
                <a href="{{ url('/') }}" class="hover:text-blue-300 transition">Home</a>
                <a href="{{ url('/dashboard') }}" class="hover:text-blue-300 transition">Dashboard</a>
                <a href="{{ url('/goals') }}" class="hover:text-blue-300 transition">Goals</a>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            @guest
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-sm hover:text-blue-300 transition">Sign in</a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-md text-sm font-semibold shadow">Create account</a>
                </div>

                <!-- mobile: show small buttons -->
                <div class="md:hidden flex gap-2">
                    <a href="{{ route('login') }}" class="text-sm px-2 py-1 rounded-md border border-white/6">Login</a>
                </div>
            @endguest

            @auth
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('goals.index') }}" class="px-3 py-1.5 bg-blue-600 rounded-md text-sm font-medium">Goals</a>

                    <!-- avatar dropdown -->
                    <div class="relative" x-data="{}">
                        <button id="userToggle" class="flex items-center gap-3 px-2 py-1 rounded-md hover:bg-white/3 transition">
                            <div class="avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                            <div class="text-sm text-white/90">{{ Auth::user()->name }}</div>
                        </button>

                        <div id="userDropdown" class="absolute right-0 mt-2 dropdown rounded-lg p-3 hidden">
                            <div class="text-sm text-white/90 mb-2">Signed in as <strong class="block">{{ Auth::user()->name }}</strong></div>
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded hover:bg-white/4">Profile</a>
                            <a href="{{ route('goals.index') }}" class="block px-3 py-2 rounded hover:bg-white/4">My Goals</a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button class="w-full text-left px-3 py-2 rounded bg-red-600 hover:bg-red-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- mobile quick actions -->
                <div class="md:hidden flex items-center gap-2">
                    <button id="mobileToggle" class="p-2 rounded-md border border-white/6">☰</button>
                </div>
            @endauth
        </div>
    </header>

    <!-- MOBILE MENU (slide down) -->
    <div id="mobileMenu" class="fixed inset-x-4 top-[72px] z-40 bg-[#041226] border border-white/6 rounded-xl p-4 shadow-lg hidden md:hidden">
        <div class="flex flex-col gap-2">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded hover:bg-white/4">Home</a>
            <a href="{{ url('/dashboard') }}" class="block px-3 py-2 rounded hover:bg-white/4">Dashboard</a>
            <a href="{{ url('/goals') }}" class="block px-3 py-2 rounded hover:bg-white/4">Goals</a>
            @auth
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded hover:bg-white/4">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-3 py-2 rounded bg-red-600 hover:bg-red-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded hover:bg-white/4">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded bg-gradient-to-tr from-blue-500 to-indigo-500 text-center">Create account</a>
            @endauth
        </div>
    </div>

    <!-- PAGE CONTENT (push under nav) -->
    <main class="pt-28 min-h-[70vh]">
        <div class="max-w-6xl mx-auto px-6">
            {{ $slot }}
        </div>
    </main>

    <footer class="py-8 text-center text-white/60">
        © {{ date('Y') }} {{ config('app.name', "Shaid's Page") }}
    </footer>

    <!-- small scripts: dropdown + mobile menu -->
    <script>
        // user dropdown
        document.addEventListener('click', function(e){
            const toggle = document.getElementById('userToggle');
            const menu = document.getElementById('userDropdown');
            if(!toggle || !menu) return;
            if(toggle.contains(e.target)){
                menu.classList.toggle('hidden');
            } else if(!menu.contains(e.target)){
                menu.classList.add('hidden');
            }
        });

        // mobile menu toggle
        (function(){
            const btn = document.getElementById('mobileToggle');
            const mobile = document.getElementById('mobileMenu');
            if(!btn || !mobile) return;
            btn.addEventListener('click', ()=>{
                mobile.classList.toggle('hidden');
            });
        })();
    </script>
</body>
</html>
