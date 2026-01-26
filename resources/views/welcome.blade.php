<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Diary') }} - Capture Your Moments</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary: #4a3427; /* Rich Mahogany */
                --accent: #d4af37;  /* Soft Gold */
                --bg-light: #fdfcf9; /* Creamy White */
                --bg-dark: #0f0f0f;
            }
            body { 
                font-family: 'Outfit', sans-serif;
            }
            .font-serif {
                font-family: 'Playfair Display', serif;
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .dark .glass {
                background: rgba(15, 15, 15, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </head>
    <body class="antialiased bg-[#fdfcf9] dark:bg-[#0f0f0f] text-[#2d241e] dark:text-[#e5e1da] selection:bg-[#4a3427] selection:text-white">
        
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 glass shadow-sm">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2 group">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="w-10 h-10 group-hover:scale-110 transition-transform">
                    <span class="text-xl font-bold font-serif tracking-tight">{{ config('app.name', 'Diary') }}</span>
                </a>

                <div class="flex items-center gap-4 md:gap-8 text-sm font-medium">
                    <a href="#features" class="hidden sm:block hover:text-[#4a3427] dark:hover:text-[#d4af37] transition-colors">Features</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-[#4a3427] text-white rounded-full hover:bg-[#3d2b20] transition-all shadow-md active:scale-95">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-[#4a3427] dark:hover:text-[#d4af37] transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hidden xs:block px-6 py-2.5 bg-[#4a3427] text-white rounded-full hover:bg-[#3d2b20] transition-all shadow-md active:scale-95">Start Writing</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-screen pt-32 pb-20 overflow-hidden flex items-center">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 w-1/3 h-full bg-[#f8f5f0] dark:bg-[#1a1a1a] -z-10 transform skew-x-6 translate-x-12"></div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#4a3427]/5 rounded-full blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative z-10 space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1a1a1a] border border-[#4a3427]/10 rounded-full shadow-sm mx-auto lg:mx-0">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#d4af37] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#d4af37]"></span>
                        </span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#4a3427] dark:text-[#d4af37]">Your personal space</span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-serif leading-[1.1] text-[#2d241e] dark:text-white">
                        Every story <span class="text-[#d4af37] italic">deserves</span> a home.
                    </h1>

                    <p class="text-lg text-[#5a4e46] dark:text-[#a09a95] max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Capture your thoughts, dreams, and memories in a sanctuary built for reflection. 
                        A beautiful, intuitive, and personal digital journal designed for your daily life.
                    </p>

                    <div class="flex flex-wrap justify-center lg:justify-start gap-4 pt-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-[#4a3427] text-white rounded-xl font-semibold text-lg hover:shadow-2xl hover:bg-[#3d2b20] transition-all flex items-center gap-2 group">
                                Start Your Journey
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        @endif
                        <a href="#features" class="px-8 py-4 bg-white dark:bg-[#1a1a1a] text-[#4a3427] dark:text-white border border-[#4a3427]/10 rounded-xl font-semibold text-lg hover:bg-gray-50 dark:hover:bg-[#252525] transition-all">
                            Learn More
                        </a>
                    </div>
                </div>

                <div class="relative lg:block">
                    <div class="relative animate-float max-w-lg mx-auto lg:max-w-none">
                        <!-- Hero Image Frame -->
                        <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border-8 border-white dark:border-[#1a1a1a]">
                            <img src="{{ asset('images/diary_hero.png') }}" alt="Beautiful Diary" class="w-full h-auto">
                        </div>
                        
                        <!-- Decorative Accents -->
                        <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-[#d4af37]/20 rounded-full blur-2xl -z-10"></div>
                        <div class="absolute -top-10 -left-10 w-32 h-32 bg-[#4a3427]/10 rounded-full blur-xl -z-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-white dark:bg-[#151515] relative overflow-hidden text-left">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                    <h2 class="text-4xl md:text-5xl font-serif">Designed for reflection</h2>
                    <p class="text-lg text-[#5a4e46] dark:text-[#a09a95]">Thoughtfully crafted features to help you capture the essence of your life, day by day.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 rounded-3xl bg-[#fdfcf9] dark:bg-[#1a1a1a] border border-[#4a3427]/5 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center mb-6 text-amber-600 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Intuitive Writing</h3>
                        <p class="text-[#5a4e46] dark:text-[#8a837e]">A distraction-free interface that lets your thoughts flow onto the digital page effortlessly.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-8 rounded-3xl bg-[#fdfcf9] dark:bg-[#1a1a1a] border border-[#4a3427]/5 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mb-6 text-blue-600 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Immich Photos</h3>
                        <p class="text-[#5a4e46] dark:text-[#8a837e]">Full integration with your Immich library. Easily attach photos and memories to your entries.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-8 rounded-3xl bg-[#fdfcf9] dark:bg-[#1a1a1a] border border-[#4a3427]/5 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-purple-50 dark:bg-purple-900/20 rounded-2xl flex items-center justify-center mb-6 text-purple-600 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Person Tracking</h3>
                        <p class="text-[#5a4e46] dark:text-[#8a837e]">Keep track of interactions with children and other important people in your life.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-8 rounded-3xl bg-[#fdfcf9] dark:bg-[#1a1a1a] border border-[#4a3427]/5 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center mb-6 text-green-600 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 00-2-2m11 12a9 9 0 01-9-9m9 9a9 9 0 009-9m-9 9Hc1.252 0 2.455-.134 3.608-.388m-9.108-.592a8.962 8.962 0 011.374-2.47m9.447-15.448a9.96 9.96 0 010 15.914m-1.374-2.47a8.962 8.962 0 01-1.374 2.47M15 9a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">AI Insights</h3>
                        <p class="text-[#5a4e46] dark:text-[#8a837e]">Hybrid extraction logic to automatically understand and organize the context of your notes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 text-center">
            <div class="max-w-5xl mx-auto px-6">
                <div class="rounded-3xl bg-[#4a3427] p-12 md:p-20 relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/notebook.png')] opacity-10"></div>
                    <div class="relative z-10 space-y-8">
                        <h2 class="text-4xl md:text-6xl font-serif text-white leading-tight">Begin your chapter <br><span class="text-[#d4af37]">today.</span></h2>
                        <p class="text-lg text-white/70 max-w-2xl mx-auto italic">"Memory is the diary we all carry about with us."</p>
                        <div class="pt-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-[#d4af37] text-[#4a3427] rounded-xl font-bold text-lg hover:bg-white transition-all shadow-xl active:scale-95">Create Your Free Diary</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-[#4a3427]/5 dark:border-white/5">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="w-8 h-8 opacity-80">
                    <span class="text-lg font-bold font-serif">{{ config('app.name', 'Diary') }}</span>
                    <span class="text-xs text-[#8a837e] ml-2">© {{ date('Y') }} All rights reserved.</span>
                </div>
                <div class="flex gap-8 text-sm text-[#8a837e]">
                    <a href="#" class="hover:text-[#4a3427] dark:hover:text-white transition-colors font-medium">Privacy Policy</a>
                    <a href="#" class="hover:text-[#4a3427] dark:hover:text-white transition-colors font-medium">Terms</a>
                    <a href="#" class="hover:text-[#4a3427] dark:hover:text-white transition-colors font-medium">Support</a>
                </div>
            </div>
        </footer>

    </body>
</html>
