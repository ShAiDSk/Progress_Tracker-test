<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shaid's Page</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Smooth fade-in animation -->
    <style>
        .fade-in {
            animation: fadeIn 0.8s ease-in-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
            from { opacity: 0; transform: translateY(10px); }
        }

        ::selection {
            background: #2563eb;
            color: white;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-800 text-white">

    <!-- ⭐ Modern Floating Navbar -->
    <nav class="backdrop-blur-xl bg-white/5 border-b border-white/10 px-10 py-4 flex justify-between items-center fixed top-0 left-0 w-full z-50">
        <h1 class="text-3xl font-extrabold tracking-wide text-blue-400 hover:text-blue-300 transition">
            <div class="text-xl md:text-2xl font-extrabold text-blue-300 tracking-tight hover:scale-[1.03] transition">
                <a href="{{ route('home') }}"
                    class="hover:text-blue-400 transition-all duration-300 {{ request()->routeIs('home') ? 'text-blue-400 font-semibold' : '' }}">
                    Shaid's Page
                </a>
            </div>
        </h1>

        <div class="flex items-center gap-6 text-lg">
            <a href="{{ route('login') }}"
               class="hover:text-blue-400 transition-all duration-300 {{ request()->routeIs('login') ? 'text-blue-400 font-semibold' : '' }}">
               Login
            </a>

            <a href="{{ route('register') }}"
               class="hover:text-blue-400 transition-all duration-300 {{ request()->routeIs('register') ? 'text-blue-400 font-semibold' : '' }}">
               Register
            </a>
        </div>
    </nav>

    <!-- ⭐ Main Content with Animation -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-24 pb-10 fade-in">

        <div class="w-full max-w-md bg-white/10 backdrop-blur-xl shadow-2xl border border-white/20 rounded-2xl p-8">

            <h2 class="text-center text-3xl font-bold mb-6 text-blue-300 drop-shadow-sm">
                {{ $title ?? 'Welcome' }}
            </h2>

            {{-- Form content from each page --}}
            <div class="text-white">
                {{ $slot }}
            </div>

        </div>

    </div>

</body>
</html>
