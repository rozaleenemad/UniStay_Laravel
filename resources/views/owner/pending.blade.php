<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StayUni | Account Review</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fcfaf7] min-h-screen flex flex-col items-center justify-center px-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <div class="w-full max-w-md bg-white border border-[#ebdccb] rounded-[24px] p-8 shadow-sm text-center space-y-6">

        <div class="flex items-center justify-center gap-2.5">
            <div class="bg-[#4a3325] text-white w-8 h-8 flex items-center justify-center rounded-lg font-extrabold text-sm">
                <i class="fa-solid fa-house-chimney-window"></i>
            </div>
            <span class="text-base font-extrabold text-[#4a3728] tracking-tight">Stay<span class="text-[#b08d72]">Uni</span></span>
        </div>

        <div class="text-[#b08d72] text-3xl py-2">
            <i class="fa-solid fa-hourglass-half fa-spin-by-need" style="animation: fa-spin 3s linear infinite;"></i>
        </div>

        <div class="space-y-2">
            <h1 class="text-lg font-extrabold text-[#4a3728] tracking-tight">Account Under Review</h1>
            <p class="text-xs text-[#8c7460] font-medium">Welcome, {{ Auth::user()->name ?? 'Owner' }}</p>
        </div>

        <div class="border-t border-[#f5f0ea] pt-4 space-y-2" dir="rtl">
            <p class="text-sm font-bold text-[#4a3728]">تم استلام بياناتك وجاري مراجعة الحساب ⏳</p>
            <p class="text-xs text-[#8c7460] leading-relaxed">
                يقوم فريق الإدارة بمراجعة الأوراق الآن لتأمين الحساب. سيتم تفعيل حسابك خلال ساعات قليلة لتتمكن من إضافة شققك.
            </p>
        </div>

        <div class="pt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-[#fdfcfb] border border-[#ebdccb] hover:border-red-200 text-[#8c7460] hover:text-red-500 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i>
                    Sign Out / تسجيل الخروج
                </button>
            </form>
        </div>

    </div>

    <p class="text-center text-[10px] text-[#b09a8a] font-medium mt-4">&copy; 2026 StayUni Platform</p>

</body>
</html>
