<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StayUni | Student Housing Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-200 min-h-screen antialiased flex flex-col justify-between selection:bg-[#4a3325] selection:text-white">

    <div class="relative w-full min-h-screen flex flex-col justify-between overflow-hidden">

        <div class="absolute inset-0 z-0">
            <img src="{{ asset('image1.jpeg') }}" alt="StayUni Premium Housing" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/75 via-black/60 to-[#26160c]"></div>
        </div>

        <header class="w-full z-50 bg-black/40 backdrop-blur-md border-b border-[#4a3325]/40 px-6 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3 group">
                <div class="bg-[#4a3325] text-white w-10 h-10 flex items-center justify-center rounded-xl font-extrabold text-lg shadow-md border border-[#b08d72]/30 transition-transform group-hover:scale-105">
                    <i class="fa-solid fa-house-chimney-window"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-[#e6cfbd] leading-none">StayUni</span>
                    <span class="text-[10px] font-bold text-[#b08d72] tracking-wider mt-1 uppercase">Student Housing</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-xs font-extrabold text-[#e6cfbd] tracking-wider uppercase">
                <a href="#services" class="hover:text-white transition-colors py-1">Verified Owners</a>
                <a href="#services" class="hover:text-white transition-colors py-1">Campus Zones</a>
                <a href="#services" class="hover:text-white transition-colors py-1">Secure Booking</a>
            </nav>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                <div class="flex items-center gap-3">
                    @auth
                    @if(Route::has(Auth::user()->role . '.dashboard'))
                    <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="bg-[#4a3325] hover:bg-[#38251a] text-white border border-[#b08d72]/30 px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-all">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ url('/') }}" class="bg-[#4a3325] hover:bg-[#38251a] text-white border border-[#b08d72]/30 px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-all">
                        Dashboard
                    </a>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="text-[#e6cfbd] hover:text-white bg-white/5 hover:bg-white/10 px-4 py-2 rounded-xl text-xs font-bold tracking-wider uppercase border border-white/10 transition-colors">
                        Login
                    </a>

                    <a href="{{ route('register') }}" class="bg-[#4a3325] hover:bg-[#38251a] text-white border border-[#b08d72]/40 px-4 py-2 rounded-xl text-xs font-bold tracking-wider uppercase shadow-lg transition-all">
                        Register
                    </a>
                    @endauth
                </div>
                @endif
            </div>
        </header>

        <main class="w-full max-w-5xl mx-auto px-6 text-center z-10 flex flex-col items-center justify-center grow gap-6 pt-16 pb-24">

            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.2] max-w-4xl text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.5)]">
                Find Your Perfect <br>
                <span class="bg-gradient-to-r from-[#fdfcfb] via-[#e6cfbd] to-[#b08d72] bg-clip-text text-transparent">Student Home</span>
            </h1>

            <p class="text-slate-200 text-base md:text-lg max-w-2xl leading-relaxed font-semibold drop-shadow-[0_2px_10px_rgba(0,0,0,0.9)]">
                Explore the premium network connecting university students with verified owners for a safe, secure, and productive housing experience.
            </p>

            <div class="mt-4">
                @auth
                {{-- 🛠️ تعديل الجزء الأوسط لعرض رسالة مخصصة بناءً على الرول وبشكل مستقر --}}
                <div class="flex flex-col items-center gap-3">
                    <p class="text-xs text-[#e6cfbd] bg-slate-950/90 backdrop-blur-md px-4 py-3 rounded-xl border border-[#4a3325] tracking-wide font-medium shadow-md">
                        <i class="fa-solid fa-circle-check text-[#b08d72] mr-1.5"></i>
                        Core Linkage Established. Welcome back, {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }}).
                    </p>
                    @if(!Route::has(Auth::user()->role . '.dashboard'))
                    <span class="text-[11px] text-[#b08d72] bg-amber-500/10 px-3 py-1 rounded-md border border-amber-500/20 font-bold">
                        🛠️ Maintenance Dashboard Panel is under construction
                    </span>
                    @endif
                </div>
                @else
                <a href="#services" class="bg-[#4a3325] hover:bg-[#38251a] text-white border border-[#b08d72]/40 px-8 py-3.5 rounded-xl text-sm font-extrabold shadow-xl transition-all transform hover:-translate-y-0.5 inline-block">
                    Explore Services <i class="fa-solid fa-arrow-down ml-2 text-xs text-[#e6cfbd]"></i>
                </a>
                @endauth
            </div>
        </main>
    </div>

    <section id="services" class="w-full bg-gradient-to-b from-[#26160c] to-[#1e1109] border-t border-[#4a2e1b] py-24 z-10 relative">
        <div class="max-w-7xl mx-auto px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#fdfcfb] mb-4 tracking-tight">Our Featured Services</h2>
                <div class="h-1 w-12 bg-[#b08d72] mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/[0.02] backdrop-blur-md border border-[#4a3325]/40 p-8 rounded-2xl hover:border-[#b08d72]/50 hover:bg-white/[0.05] hover:shadow-2xl hover:shadow-black/40 transition-all duration-300 group">
                    <div class="text-[#b08d72] text-3xl mb-4 group-hover:scale-110 transition-transform duration-200">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-[#fcfaf7] font-bold text-lg mb-2">Vetted Listings</h3>
                    <p class="text-slate-300 text-sm leading-relaxed font-medium">Every property and owner document goes through manual human administrative vetting.</p>
                </div>

                <div class="bg-white/[0.02] backdrop-blur-md border border-[#4a3325]/40 p-8 rounded-2xl hover:border-[#b08d72]/50 hover:bg-white/[0.05] hover:shadow-2xl hover:shadow-black/40 transition-all duration-300 group">
                    <div class="text-[#b08d72] text-3xl mb-4 group-hover:scale-110 transition-transform duration-200">
                        <i class="fa-solid fa-bolt-lightning"></i>
                    </div>
                    <h3 class="text-[#fcfaf7] font-bold text-lg mb-2">Secure Contacts</h3>
                    <p class="text-slate-300 text-sm leading-relaxed font-medium">Direct, encrypted endpoints connecting certified students with legal property owners.</p>
                </div>

                <div class="bg-white/[0.02] backdrop-blur-md border border-[#4a3325]/40 p-8 rounded-2xl hover:border-[#b08d72]/50 hover:bg-white/[0.05] hover:shadow-2xl hover:shadow-black/40 transition-all duration-300 group">
                    <div class="text-[#b08d72] text-3xl mb-4 group-hover:scale-110 transition-transform duration-200">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="text-[#fcfaf7] font-bold text-lg mb-2">Campus Proximity</h3>
                    <p class="text-slate-300 text-sm leading-relaxed font-medium">Advanced geofencing strictly tracking properties within immediate university walking distance.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="w-full bg-[#fdfcfb] border-t border-[#ebdccb] py-8 z-10 relative">
        <div class="max-w-7xl mx-auto px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#8c7460] gap-4">
            <div class="tracking-wide text-center sm:text-left font-semibold">
                &copy; 2026 <span class="text-[#4a3728] font-bold">StayUni Inc.</span> All rights reserved. Rozaleen Emad Roshdy.
            </div>

            <div class="flex gap-6 font-bold text-[#6e5542]">
                <a href="#" class="hover:text-[#4a3728] hover:underline transition-all">Privacy Shield</a>
                <a href="#" class="hover:text-[#4a3728] hover:underline transition-all">Terms of Operations</a>
            </div>
        </div>
    </footer>

</body>

</html>
