<x-app-layout>

    <style>
        /* Page Gradient Background */
        body {
            background: radial-gradient(circle at top left, #0f172a, #020617 60%);
            background-attachment: fixed;
        }

        /* Neon Title Animation */
        .neon-title {
            text-shadow: 0 0 12px #3b82f6, 0 0 24px #60a5fa;
            animation: glowPulse 2.5s infinite ease-in-out;
        }

        @keyframes glowPulse {
            0%, 100% { text-shadow: 0 0 10px #3b82f6; }
            50% { text-shadow: 0 0 25px #60a5fa; }
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .glass-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.35);
        }

        /* Smooth Progress Bar */
        .progress-inner {
            transition: width 0.8s ease-in-out;
        }
    </style>

    <div class="max-w-6xl mx-auto px-6 py-12">

        <!-- TOP BAR BUTTONS -->
        <div class="flex justify-end gap-4 mb-6">
            <a href="/goals"
               class="px-4 py-2 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition">
                Goals
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-500 transition">
                        Logout
                    </button>
                </form>
            @else
                <a href="/login"
                   class="px-4 py-2 text-sm rounded-lg bg-gray-700 text-white hover:bg-gray-600 transition">
                    Login
                </a>
                <a href="/register"
                   class="px-4 py-2 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition">
                    Register
                </a>
            @endauth
        </div>

        <!-- Dashboard Title -->
        <h2 class="text-4xl font-extrabold text-white neon-title mb-10 tracking-wide">
            Dashboard
        </h2>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Total Goals -->
            <div class="glass-card rounded-xl p-6">
                <p class="text-gray-300 text-sm mb-1">Total Goals</p>
                <h3 class="text-4xl font-bold text-white">
                    {{ Auth::user()->goals->count() }}
                </h3>
            </div>

            <!-- Active Goals -->
            <div class="glass-card rounded-xl p-6">
                <p class="text-gray-300 text-sm mb-1">Active Goals</p>
                <h3 class="text-4xl font-bold text-white">
                    {{ Auth::user()->goals->where('current_amount', '<', 'target_amount')->count() }}
                </h3>
            </div>

            <!-- Completed Goals -->
            <div class="glass-card rounded-xl p-6">
                <p class="text-gray-300 text-sm mb-1">Completed</p>
                <h3 class="text-4xl font-bold text-white">
                    {{ Auth::user()->goals->where('current_amount', '>=', 'target_amount')->count() }}
                </h3>
            </div>

        </div>

        <!-- Recent Goals -->
        <div class="mt-14">
            <h3 class="text-2xl font-semibold text-white mb-5">Recent Goals</h3>

            <div class="space-y-5">

                @forelse (Auth::user()->goals->take(3) as $goal)
                    <div class="glass-card p-6 rounded-xl">

                        <div class="flex justify-between">
                            <h4 class="text-lg font-semibold text-white">{{ $goal->title }}</h4>
                            <span class="text-gray-300 text-sm">
                                {{ $goal->deadline ?? 'No deadline' }}
                            </span>
                        </div>

                        <p class="text-gray-300 mt-1">{{ $goal->description }}</p>

                        <p class="text-sm text-blue-300 mt-4 font-medium">
                            Progress: {{ $goal->current_amount }} / {{ $goal->target_amount ?? '-' }}
                        </p>

                        @php
                            $p = $goal->target_amount
                                ? ($goal->current_amount / $goal->target_amount) * 100
                                : 0;
                        @endphp

                        <div class="w-full bg-gray-700 h-2 rounded-full mt-2 overflow-hidden">
                            <div class="progress-inner bg-blue-500 h-2 rounded-full" style="width: {{ $p }}%;"></div>
                        </div>

                    </div>
                @empty
                    <p class="text-gray-400">No goals yet. Start creating your first goal.</p>
                @endforelse

            </div>
        </div>

    </div>
</x-app-layout>
