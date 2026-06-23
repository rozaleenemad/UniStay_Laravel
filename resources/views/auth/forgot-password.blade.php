<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StayUni | Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fcfaf7] text-amber-950 min-h-screen antialiased flex flex-col justify-between" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <div class="fixed inset-0 z-0">
        <img src="{{ asset('hero.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-25 blur-[1px]">
        <div class="absolute inset-0 bg-gradient-to-b from-[#fdfbf9]/90 via-[#f5f0ea]/85 to-[#ebdccb]/90"></div>
    </div>

    <header class="w-full max-w-7xl mx-auto px-6 py-6 z-10 relative flex justify-between items-center">
        <a href="/" class="flex items-center gap-3">
            <div class="bg-[#8b6f56] text-[#fcfaf7] w-10 h-10 flex items-center justify-center rounded-xl font-extrabold text-lg shadow-md shadow-[#8b6f56]/20">
                <i class="fa-solid fa-house-chimney-window text-sm"></i>
            </div>
            <span class="text-xl font-bold text-[#5c4738] tracking-tight">Stay<span class="text-[#b08d72]">Uni</span></span>
        </a>
    </header>

    <main class="z-10 relative flex grow items-center justify-center px-4 py-8">
        <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-[#ebdccb] rounded-[28px] p-8 shadow-xl shadow-[#5c4738]/5">

            <div class="text-center mb-6">
                <div class="text-[#8b6f56] text-4xl mb-3">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-[#4a3728] tracking-tight mb-2">Forgot Password?</h2>
                <p class="text-xs text-[#8c7460] leading-relaxed">
                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-[#6e5542] uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b09a8a]"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@university.edu"
                               class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#8b6f56] hover:bg-[#765e49] text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md shadow-[#8b6f56]/20 tracking-wide">
                    Email Password Reset Link
                </button>
            </form>

            <div class="border-t border-[#ebdccb] mt-6 pt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm font-bold text-[#8b6f56] hover:text-[#765e49] transition-colors inline-flex items-center gap-2 underline">
                    <i class="fa-solid fa-arrow-left-long text-xs"></i> Back to Sign In
                </a>
            </div>

        </div>
    </main>

    <footer class="w-full text-center py-4 text-xs text-[#a08a75] z-10 relative">
        &copy; 2026 StayUni Housing Platform.
    </footer>
</body>
</html>
