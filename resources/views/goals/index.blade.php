<x-app-layout>

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
            0%, 100% { text-shadow: 0 0 10px #3b82f6; }
            50% { text-shadow: 0 0 25px #60a5fa; }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
            transition: transform .22s ease, box-shadow .22s ease;
        }
        .glass-card:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(0,0,0,0.45); }
        .add-btn {
            background: linear-gradient(90deg,#2563eb,#1d4ed8);
            transition: all .25s ease;
        }
        .add-btn:hover { transform: scale(1.03); }
        .progress-inner { transition: width 0.7s ease-out; }
        .status-badge { font-weight: 600; padding: 4px 8px; border-radius: 999px; font-size: .8rem; }
        .status-not { background: rgba(255,255,255,0.06); color: #e6eefc; }
        .status-progress { background: rgba(37,99,235,0.16); color: #cfe4ff; }
        .status-done { background: rgba(34,197,94,0.16); color: #dff7e5; }
        .muted { color: rgba(255,255,255,0.65); }
        .small-input { padding: .375rem .6rem; border-radius: .5rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); color: #fff; }
    </style>

    <div class="max-w-5xl mx-auto px-4 py-10">

        <!-- header -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-4xl font-extrabold neon-title">Your Goals</h2>

            <a href="{{ route('goals.create') }}" class="add-btn text-white px-5 py-2.5 rounded-lg shadow-lg font-semibold">
                + Add Goal
            </a>
        </div>

        <div class="space-y-6">

            @forelse ($goals as $goal)
                @php
                    $percent = ($goal->target_amount && $goal->target_amount > 0)
                                ? ($goal->current_amount / $goal->target_amount) * 100
                                : ($goal->current_amount > 0 ? 100 : 0);

                    $status = 'not-started';
                    if ($percent <= 0) $status = 'not-started';
                    elseif ($percent >= 100) $status = 'done';
                    else $status = 'in-progress';
                @endphp

                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-semibold text-white">{{ $goal->title }}</h3>
                            @if ($goal->description)
                                <p class="muted mt-1">{{ $goal->description }}</p>
                            @endif
                        </div>

                        <div class="text-right">
                            <div class="mb-2">
                                <span class="status-badge
                                    {{ $status === 'not-started' ? 'status-not' : '' }}
                                    {{ $status === 'in-progress' ? 'status-progress' : '' }}
                                    {{ $status === 'done' ? 'status-done' : '' }}">
                                    {{ $status === 'not-started' ? 'Not started' : ($status === 'in-progress' ? 'In progress' : 'Completed') }}
                                </span>
                            </div>
                            <div class="text-sm muted">
                                {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('F d, Y') : 'No deadline' }}
                            </div>
                        </div>
                    </div>

                    @if($goal->target_amount)
                        <div class="mt-5">
                            <div class="flex justify-between text-sm font-medium text-blue-200 mb-1">
                                <span>Progress</span>
                                <span>{{ number_format($percent, 0) }}%</span>
                            </div>

                            <div class="w-full bg-white/8 rounded-full h-3 overflow-hidden">
                                <div class="progress-inner bg-gradient-to-r from-blue-400 to-indigo-500 h-3 rounded-full"
                                     style="width: {{ $percent }}%"></div>
                            </div>

                            <p class="mt-2 text-sm muted">
                                {{ $goal->current_amount }} / {{ $goal->target_amount }}
                            </p>
                        </div>
                    @endif

                    <!-- buttons -->
                    <div class="mt-5 flex items-center gap-3">
                        {{-- Edit Progress toggle --}}
                        <button type="button" class="px-3 py-1.5 rounded-md bg-white/6 text-white hover:bg-white/8 transition" onclick="toggleEdit({{ $goal->id }})">
                            Edit Progress
                        </button>

                        @if($status === 'not-started')
                            <form method="POST" action="{{ route('goals.updateProgress', $goal) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="current_amount" value="{{ min( max(1, $goal->current_amount), $goal->target_amount ?? 1) }}">
                                <button class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">Start Progress</button>
                            </form>

                            {{-- Also allow mark done immediately --}}
                            <form method="POST" action="{{ route('goals.done', $goal) }}" class="inline-block">
                                @csrf
                                <button class="px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700 transition">Mark as Done</button>
                            </form>
                        @elseif($status === 'in-progress')
                            <form method="POST" action="{{ route('goals.done', $goal) }}" class="inline-block">
                                @csrf
                                <button class="px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700 transition">Mark as Done</button>
                            </form>
                        @elseif($status === 'done')
                            <form method="POST" action="{{ route('goals.reopen', $goal) }}" class="inline-block">
                                @csrf
                                <button class="px-3 py-1.5 rounded-md bg-yellow-600 text-white hover:bg-yellow-700 transition">Reopen</button>
                            </form>
                        @endif

                        {{-- small help text --}}
                        <span class="muted text-sm ml-auto">Status: <strong class="text-white">{{ ucfirst(str_replace('-', ' ', $status)) }}</strong></span>
                    </div>

                    {{-- inline edit area (hidden by default) --}}
                    <div id="edit-{{ $goal->id }}" style="display:none" class="mt-4">
                        <form method="POST" action="{{ route('goals.updateProgress', $goal) }}" class="flex items-center gap-3">
                            @csrf
                            @method('PATCH')

                            <input type="number"
                                   name="current_amount"
                                   min="0"
                                   step="0.01"
                                   value="{{ $goal->current_amount }}"
                                   @if($goal->target_amount) max="{{ $goal->target_amount }}" @endif
                                   class="small-input">

                            <button class="px-3 py-1.5 rounded-md bg-blue-500 text-white hover:bg-blue-600 transition">Save</button>
                            <button type="button" onclick="toggleEdit({{ $goal->id }})" class="px-3 py-1.5 rounded-md bg-white/6 text-white hover:bg-white/8 transition">Cancel</button>
                        </form>
                    </div>

                </div>

            @empty
                <p class="text-gray-300 text-center py-16 text-lg">No goals yet. Create one to get started.</p>
            @endforelse

        </div>

    </div>

    <script>
        // show/hide per-goal edit UI
        function toggleEdit(id){
            const el = document.getElementById('edit-' + id);
            if(!el) return;
            el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
            // smooth scroll to edit area
            if(el.style.display === 'block'){
                setTimeout(()=> el.scrollIntoView({behavior:'smooth', block:'center'}), 50);
            }
        }

        // animate progress bars (on load)
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.progress-inner').forEach(el=>{
                const w = el.style.width;
                el.style.width = '0%';
                setTimeout(()=> el.style.width = w, 60);
            });
        });
    </script>

</x-app-layout>
