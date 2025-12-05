<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Shaid's Page — Progress Tracker</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ---------- animated neon gradient ---------- */
        .neon-bg {
            position: absolute;
            inset: -20%;
            z-index: -3;
            filter: blur(60px) saturate(120%);
            opacity: 0.9;
            background: linear-gradient(120deg,
                    rgba(14, 165, 233, 0.18),
                    rgba(59, 130, 246, 0.12),
                    rgba(99, 102, 241, 0.18),
                    rgba(236, 72, 153, 0.10));
            background-size: 400% 400%;
            animation: neonMove 12s linear infinite;
            transform: translateZ(0);
        }

        @keyframes neonMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* ---------- subtle center gradient wave ---------- */
        .wave {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -10%;
            width: 120%;
            height: 40vh;
            background: radial-gradient(closest-side,
                    rgba(99, 102, 241, 0.12),
                    rgba(59, 130, 246, 0.08) 30%,
                    rgba(14, 165, 233, 0.03) 60%,
                    transparent 80%);
            z-index: -2;
            filter: blur(36px);
            animation: waveFloat 8s ease-in-out infinite alternate;
        }

        @keyframes waveFloat {
            from {
                transform: translate(-50%, 0px);
            }

            to {
                transform: translate(-50%, -18px);
            }
        }

        /* ---------- particles canvas ---------- */
        #particles {
            position: absolute;
            inset: 0;
            z-index: -4;
            pointer-events: none;
            opacity: 0.55;
        }

        /* ---------- hero text/typing ---------- */
        .typing {
            color: #e6f0ff;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.05;
        }

        .hero-sub {
            color: rgba(226, 240, 255, 0.85);
        }

        /* ---------- parallax elements (depth) ---------- */
        [data-parallax] {
            will-change: transform;
            transition: transform 220ms linear;
        }

        /* ---------- glass card ---------- */
        .glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(8px) saturate(120%);
        }

        /* ---------- fade-in ---------- */
        .fade-up {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeUp 0.7s ease-out forwards;
        }

        .fade-up.delay-1 {
            animation-delay: 0.12s;
        }

        .fade-up.delay-2 {
            animation-delay: 0.24s;
        }

        .fade-up.delay-3 {
            animation-delay: 0.36s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* small helpers */
        .cta {
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.18);
        }

        /* make text easier to read on dark bg */
        .bright {
            color: #eef7ff;
        }
    </style>
</head>

<body class="min-h-screen bg-[#071124] antialiased relative text-white overflow-x-hidden">

    <!-- animated layers -->
    <div class="neon-bg"></div>
    <div class="wave"></div>
    <canvas id="particles"></canvas>

    <!-- NAV (floating glass) -->
    <nav
        class="fixed top-6 left-1/2 -translate-x-1/2 w-[92%] max-w-6xl glass rounded-2xl px-6 py-3 flex items-center justify-between z-40 shadow-xl">
        <div class="hidden md:flex items-center gap-3 text-sm text-white/80">
            <div class="text-xl md:text-2xl font-extrabold text-blue-300 tracking-tight hover:scale-[1.03] transition">
                <a href="{{ route('home') }}"
                    class="hover:text-blue-400 transition-all duration-300 {{ request()->routeIs('login') ? 'text-blue-400 font-semibold' : '' }}">
                    Shaid's Page
                </a>
            </div>
            <a href="#features" class="hover:text-blue-300 transition">Features</a>

            @guest
            <a href="{{ route('register') }}" class="hover:text-blue-300 transition">Get started</a>
            @endguest

            @auth
            <span class="text-blue-300 font-semibold">
                Welcome, {{ Auth::user()->name }}
            </span>

            <a href="{{ url('/dashboard') }}" class="hover:text-blue-300 transition">Dashboard</a>
            <a href="{{ url('/goals') }}" class="hover:text-blue-300 transition">Goals</a>
            @endauth
        </div>


        <div class="flex items-center gap-3">
            @guest
            <a href="{{ route('login') }}"
                class="px-4 py-2 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-lg text-sm font-semibold cta">
                Sign in
            </a>
            <a href="{{ route('register') }}"
                class="px-4 py-2 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-lg text-sm font-semibold cta">
                Create account
            </a>
            @endguest

            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-semibold transition">
                    Logout
                </button>
            </form>
            @endauth
        </div>

    </nav>

    <!-- HERO -->
    <header class="relative pt-28 pb-10">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <!-- left: headline -->
                <div class="md:col-span-7">
                    <div class="max-w-2xl">
                        <h1 class="typing text-4xl md:text-6xl leading-tight fade-up delay-1" id="hero-text">
                            <!-- content filled by typing JS -->
                        </h1>

                        <p class="hero-sub mt-6 text-lg md:text-xl fade-up delay-2">
                            Track your goals, measure progress, and turn tiny wins into serious momentum. Built with
                            Laravel — designed for sharp focus.
                        </p>

                        <div class="mt-8 flex gap-4 fade-up delay-3">
                            <a href="{{ route('register') }}"
                                class="cta px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 font-semibold shadow-lg">
                                Get started — it's free
                            </a>

                            <a href="{{ route('goals.index') }}"
                                class="px-5 py-3 rounded-xl border border-white/10 glass text-white/90">
                                View goals
                            </a>
                        </div>
                    </div>

                    <!-- quick feature badges -->
                    <div class="mt-10 flex flex-wrap gap-3">
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/90">Fast</div>
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/90">Secure</div>
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/90">Offline-friendly</div>
                    </div>
                </div>

                <!-- right: mockup / parallax card -->
                <div class="md:col-span-5 flex justify-center md:justify-end">
                    <div class="relative w-[340px] md:w-[420px]">
                        <div data-parallax="0.06" class="glass rounded-2xl p-5 shadow-2xl"
                            style="transform: translateZ(0);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-blue-300 font-semibold">Monthly Goal</div>
                                    <div class="text-lg font-bold bright">Reach $1,000</div>
                                </div>
                                <div class="text-sm text-white/70">30 days</div>
                            </div>

                            <div class="mt-4">
                                <div class="w-full bg-white/12 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-400 to-indigo-500 h-3 rounded-full"
                                        style="width: 28%"></div>
                                </div>
                                <div class="mt-3 text-sm text-white/80">Progress: $280 / $1,000</div>
                            </div>

                            <div class="mt-5 flex gap-2">
                                <button class="flex-1 px-3 py-2 rounded-md bg-white/6 text-white/90">Add
                                    progress</button>
                                <button class="px-3 py-2 rounded-md bg-white/5 border border-white/6">View</button>
                            </div>
                        </div>

                        <!-- decorative floating chips -->
                        <div data-parallax="0.12"
                            class="absolute -right-8 -top-8 glass rounded-lg px-3 py-2 text-xs shadow-lg">+ $20 today
                        </div>
                        <div data-parallax="-0.12"
                            class="absolute -left-8 -bottom-6 glass rounded-lg px-3 py-2 text-xs shadow-lg">Streak: 3
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- FEATURES -->
    <section id="features" class="py-12">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="glass rounded-2xl p-6 fade-up delay-1">
                    <h4 class="text-lg font-bold bright">Smart progress</h4>
                    <p class="text-white/80 mt-2">Log small wins and the app will calculate your momentum.</p>
                </div>
                <div class="glass rounded-2xl p-6 fade-up delay-2">
                    <h4 class="text-lg font-bold bright">Visual dashboard</h4>
                    <p class="text-white/80 mt-2">At-a-glance stats and charts to keep you honest.</p>
                </div>
                <div class="glass rounded-2xl p-6 fade-up delay-3">
                    <h4 class="text-lg font-bold bright">Mobile-ready</h4>
                    <p class="text-white/80 mt-2">Works across phones and desktop — no fuss.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer class="py-8 mt-12">
        <div class="max-w-6xl mx-auto px-6 text-center text-white/70">
            Built with ❤️ by Shaid — © {{ date('Y') }}
        </div>
    </footer>

    <!-- ---------------- JS (particles + typing + parallax) ---------------- -->
    <script>
        // -------------------- particles --------------------
        (function() {
            const canvas = document.getElementById('particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w = canvas.width = innerWidth;
            let h = canvas.height = innerHeight;
            const PARTICLES = Math.round((w * h) / 90000); // scale with screen
            let parts = [];

            function rand(min, max) {
                return Math.random() * (max - min) + min;
            }
            class P {
                constructor() {
                    this.reset();
                }
                reset() {
                    this.x = rand(0, w);
                    this.y = rand(0, h);
                    this.vx = rand(-0.2, 0.2);
                    this.vy = rand(-0.2, 0.2);
                    this.r = rand(0.6, 1.6);
                    this.alpha = rand(0.05, 0.18);
                    this.hue = 200 + Math.random() * 80;
                }
                step() {
                    this.x += this.vx;
                    this.y += this.vy;
                    if (this.x < -20 || this.x > w + 20 || this.y < -20 || this.y > h + 20) this.reset();
                }
                draw(ctx) {
                    ctx.beginPath();
                    ctx.fillStyle = `hsla(${this.hue},80%,70%,${this.alpha})`;
                    ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            function initParticles() {
                parts = [];
                for (let i = 0; i < PARTICLES; i++) parts.push(new P());
            }

            function render() {
                ctx.clearRect(0, 0, w, h);
                for (let p of parts) {
                    p.step();
                    p.draw(ctx);
                }
                requestAnimationFrame(render);
            }

            function onResize() {
                w = canvas.width = innerWidth;
                h = canvas.height = innerHeight;
                initParticles();
            }

            initParticles();
            render();
            addEventListener('resize', onResize);
        })();

        // -------------------- typing headline --------------------
        (function() {
            const headline = document.getElementById('hero-text');
            const phrases = [
                "Build habits. Track progress. Win consistently.",
                "Turn tiny daily wins into big results.",
                "Focus. Measure. Improve."
            ];
            let pi = 0,
                ci = 0,
                forward = true,
                offset = 0,
                skipCount = 0,
                skipDelay = 20,
                speed = 70;

            function type() {
                const text = phrases[pi];
                if (forward) {
                    if (offset >= text.length) {
                        skipCount++;
                        if (skipCount == skipDelay) {
                            forward = false;
                            skipCount = 0;
                        }
                    }
                } else {
                    if (offset === 0) {
                        forward = true;
                        pi = (pi + 1) % phrases.length;
                    }
                }

                const part = text.substr(0, offset);
                headline.innerHTML = `<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-indigo-300">${part}</span>`;
                if (forward) offset++;
                else offset--;
                setTimeout(type, speed + Math.random() * 40);
            }
            // start after small delay (let hero fade)
            setTimeout(type, 650);
        })();

        // -------------------- parallax (mouse follow) --------------------
        (function() {
            const container = document.querySelector('header');
            const parallaxEls = document.querySelectorAll('[data-parallax]');
            if (!container || !parallaxEls.length) return;
            container.addEventListener('mousemove', (e) => {
                const rect = container.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                const dx = (e.clientX - cx) / rect.width;
                const dy = (e.clientY - cy) / rect.height;
                parallaxEls.forEach(el => {
                    const depth = parseFloat(el.getAttribute('data-parallax') || 0.06);
                    const tx = dx * depth * 40;
                    const ty = dy * depth * 30;
                    el.style.transform = `translate3d(${tx}px, ${ty}px, 0)`;
                });
            });
            container.addEventListener('mouseleave', () => {
                parallaxEls.forEach(el => el.style.transform = 'translate3d(0,0,0)');
            });
        })();

        // -------------------- small accessibility helper: reduce motion --------------------
        (function() {
            try {
                const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
                if (mq && mq.matches) {
                    document.querySelectorAll('.fade-up').forEach(el => {
                        el.style.animation = 'none';
                        el.style.opacity = 1;
                        el.style.transform = 'none';
                    });
                    document.getElementById('particles').style.display = 'none';
                }
            } catch (e) {}
        })();
    </script>
</body>

</html>