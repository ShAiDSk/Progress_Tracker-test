<x-guest-layout>
    <x-slot name="title">Login to Your Account</x-slot>

    <div class="w-full max-w-md bg-white/10 backdrop-blur-xl p-8 rounded-2xl shadow-2xl border border-white/20 fade-in">

        <h2 class="text-center text-2xl font-extrabold text-blue-400 drop-shadow-md mb-2">
            Welcome Back
        </h2>

        <p class="text-center text-gray-200 text-sm mb-6 font-medium">
            Login to continue your journey.
        </p>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autofocus autocomplete="username"
                    placeholder="you@example.com">
            </div>

            <!-- Password -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Password</label>
                <input type="password" name="password"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autocomplete="current-password"
                    placeholder="********">
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="flex justify-between items-center text-gray-200 text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded bg-white/20 border-white/30 focus:ring-blue-400">
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-blue-300 hover:text-blue-400 font-semibold hover:underline">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button
                class="w-full bg-blue-500 hover:bg-blue-600 transition text-white py-2 rounded-lg font-semibold mt-4 shadow-lg hover:shadow-blue-500/30">
                Log In
            </button>

            <!-- Create Account -->
            <p class="text-center text-gray-200 text-sm mt-3">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-300 hover:text-blue-400 font-semibold hover:underline">
                    Register
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
