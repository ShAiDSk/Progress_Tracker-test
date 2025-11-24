<x-guest-layout>
    <x-slot name="title">Create Your Account</x-slot>

    <div class="w-full max-w-md bg-white/10 backdrop-blur-xl p-8 rounded-2xl shadow-2xl border border-white/20 fade-in">

        <h2 class="text-center text-2xl font-extrabold text-blue-400 drop-shadow-md mb-2">
            Create Your Account
        </h2>

        <p class="text-center text-gray-200 text-sm mb-6 font-medium">
            Join Shaid's Page and start your journey.
        </p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autofocus autocomplete="name" placeholder="Your full name">
            </div>

            <!-- Email -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autocomplete="username" placeholder="you@example.com">
            </div>

            <!-- Password -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Password</label>
                <input type="password" name="password"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autocomplete="new-password" placeholder="********">
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="text-gray-100 text-sm font-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-1 rounded-lg bg-white/30 text-white placeholder-gray-200
                    border border-white/20 px-3 py-2 shadow-sm
                    focus:border-blue-400 focus:ring focus:ring-blue-500/40 outline-none"
                    required autocomplete="new-password" placeholder="********">
            </div>

            <!-- Register Button -->
            <button
                class="w-full bg-blue-500 hover:bg-blue-600 transition text-white py-2 rounded-lg font-semibold mt-4 shadow-lg hover:shadow-blue-500/30">
                Register
            </button>

            <!-- Already Registered -->
            <p class="text-center text-gray-200 text-sm mt-3">
                Already registered?
                <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-400 font-semibold hover:underline">
                    Log in
                </a>
            </p>

        </form>
    </div>
</x-guest-layout>
