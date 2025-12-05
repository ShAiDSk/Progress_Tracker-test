<x-app-layout>
    {{-- Upgraded premium Goals index - includes SweetAlert2 confirmations, validation + flash handling, improved accessibility and animations --}}

    <style>
        body {
            background: radial-gradient(circle at top left, #0f172a, #020617 60%);
            background-attachment: fixed;
        }

        .neon-title {
            color: #fff;
            text-shadow: 0 0 12px #3b82f6, 0 0 25px #60a5fa;
            animation: glow 2.5s infinite ease-in-out;
        }

        @keyframes glow {

            0%,
            100% {
                text-shadow: 0 0 10px #3b82f6
            }

            50% {
                text-shadow: 0 0 25px #60a5fa
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.04);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .glass-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.45);
        }

        .add-btn {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            transition: all .25s ease;
        }

        .add-btn:hover {
            transform: scale(1.03);
        }

        .progress-inner {
            transition: width .7s ease-out;
        }

        .status-badge {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: .8rem;
        }

        .status-not {
            background: rgba(255, 255, 255, 0.06);
            color: #e6eefc;
        }

        .status-progress {
            background: rgba(37, 99, 235, 0.12);
            color: #cfe4ff;
        }

        .status-done {
            background: rgba(34, 197, 94, 0.12);
            color: #dff7e5;
        }

        .muted {
            color: rgba(255, 255, 255, 0.65);
        }

        .small-input {
            padding: .375rem .6rem;
            border-radius: .5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.04);
            color: #fff;
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(8px);
            animation: fadeUp .45s ease-out forwards;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: none
            }
        }
    </style>

    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-4xl font-extrabold neon-title">Your Goals</h2>
            <a href="{{ route('goals.create') }}" class="add-btn text-white px-5 py-2.5 rounded-lg shadow-lg font-semibold">+ Add Goal</a>
        </div>

        @if(session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" style="display:none"></div>
        @endif

        @if($errors->any())
        <div id="flash-errors" data-count="{{ $errors->count() }}" style="display:none"></div>
        @endif

        <div class="space-y-6">

            @forelse ($goals as $goal)
            @php
            $percent = ($goal->target_value && $goal->target_value > 0)
            ? ($goal->current_value / $goal->target_value) * 100
            : ($goal->current_value > 0 ? 100 : 0);

            $status = 'not-started';
            if ($percent <= 0) $status='not-started' ;
                elseif ($percent>= 100) $status = 'done';
                else $status = 'in-progress';
                @endphp

                <article class="glass-card rounded-xl p-6 fade-in-up">

                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-semibold text-white">{{ $goal->title }}</h3>
                            @if($goal->description)
                            <p class="muted mt-1">{{ $goal->description }}</p>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="status-badge {{ $status === 'not-started' ? 'status-not' : '' }} {{ $status === 'in-progress' ? 'status-progress' : '' }} {{ $status === 'done' ? 'status-done' : '' }}">
                                {{ ucfirst(str_replace('-', ' ', $status)) }}
                            </span>
                            <div class="text-sm muted mt-1">
                                {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('F d, Y') : 'No deadline' }}
                            </div>
                        </div>
                    </div>

                    @if($goal->target_value)
                    <div class="mt-5">
                        <div class="flex justify-between text-sm font-medium text-blue-200 mb-1">
                            <span>Progress</span>
                            <span>{{ number_format($percent, 0) }}%</span>
                        </div>

                        <div class="w-full bg-white/8 rounded-full h-3">
                            <div class="progress-inner bg-gradient-to-r from-blue-400 to-indigo-500 h-3 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>

                        <p class="mt-2 text-sm muted">{{ $goal->current_value }} / {{ $goal->target_value }}</p>
                    </div>
                    @endif

                    <div class="mt-5 flex items-center gap-3">

                        <!-- HIDE / ARCHIVE -->
                        <form action="{{ route('goals.archive', $goal) }}" method="POST">
                            @csrf
                            <button type="submit" data-hide class="px-3 py-1.5 rounded-md bg-purple-600 text-white">Hide</button>
                        </form>

                        <!-- DELETE -->
                        <form action="{{ route('goals.destroy', $goal) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" data-delete class="px-3 py-1.5 rounded-md bg-red-600 text-white">Remove</button>
                        </form>

                        <!-- INCREMENT -->
                        <form method="POST" action="{{ route('goals.increment', $goal) }}">
                            @csrf
                            <input type="hidden" name="current_amount" value="1">
                            <button class="px-3 py-1.5 rounded-md bg-blue-600 text-white">Start / Add +1</button>
                        </form>

                        @if($status !== 'done')
                        <form method="POST" action="{{ route('goals.done', $goal) }}">
                            @csrf
                            <button class="px-3 py-1.5 rounded-md bg-green-600 text-white">Mark as Done</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('goals.reopen', $goal->id) }}" class="inline-block">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 rounded-md bg-yellow-600 text-white hover:bg-yellow-700 transition">
                                Reopen
                            </button>
                        </form>

                        @endif

                        <span class="muted ml-auto">Status: <strong class="text-white">{{ ucfirst(str_replace('-', ' ', $status)) }}</strong></span>
                    </div>

                    <!-- inline edit area -->
                    <div id="edit-{{ $goal->id }}" style="display:none" class="mt-4">
                        <form method="POST" action="{{ route('goals.increment', $goal) }}" class="flex items-center gap-3">
                            @csrf
                            <input type="number" name="amount" min="0" step="0.01"
                                value="{{ $goal->current_value }}"
                                class="small-input">

                            <button class="px-3 py-1.5 rounded-md bg-blue-500 text-white">Save</button>
                            <button type="button" onclick="toggleEdit({{ $goal->id }})" class="px-3 py-1.5 rounded-md bg-white/6 text-white">Cancel</button>
                        </form>
                    </div>

                </article>

                @empty
                <p class="text-gray-300 text-center py-16 text-lg">No goals yet. Create one to get started.</p>
                @endforelse

        </div>

    </div>

</x-app-layout>