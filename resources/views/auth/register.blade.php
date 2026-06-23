<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StayUni | Create Account</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#fcfaf7] text-[#4a3728] min-h-screen antialiased flex flex-col justify-between selection:bg-[#4a3325] selection:text-white">

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

    <main class="z-10 relative flex grow items-center justify-center px-4 py-6">
        <div class="w-full max-w-md bg-white/85 backdrop-blur-xl border border-[#ebdccb] rounded-[28px] p-8 shadow-xl shadow-[#4a3728]/5">

            <div class="text-center mb-6">
                <h2 class="text-3xl font-extrabold text-[#4a3728] tracking-tight mb-2">Create Account</h2>
                <p class="text-sm text-[#8c7460] font-medium">Join our premium student housing community</p>
            </div>

            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs space-y-1">
                @foreach ($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b08d72]"><i class="fa-regular fa-user"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Username"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b08d72]"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b08d72]"><i class="fa-solid fa-mobile-screen-button"></i></span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="01xxxxxxxxx"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider block">Account Type</label>
                    <div class="grid grid-cols-1 gap-2.5">
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center justify-between bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl p-3 cursor-pointer hover:border-[#4a3325] transition-all relative">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#b08d72]"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <span class="text-sm font-bold text-[#4a3728]">Student</span>
                                </div>
                                <input type="radio" name="role" value="student" id="roleStudent" class="accent-[#4a3325]" checked onclick="handleRoleSwitch()">
                            </label>
                            <label class="flex items-center justify-between bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl p-3 cursor-pointer hover:border-[#4a3325] transition-all relative">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#b08d72]"><i class="fa-solid fa-building-user"></i></span>
                                    <span class="text-sm font-bold text-[#4a3728]">Owner</span>
                                </div>
                                <input type="radio" name="role" value="owner" id="roleOwner" class="accent-[#4a3325]" onclick="handleRoleSwitch()">
                            </label>
                        </div>

                        <label class="flex items-center justify-between bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl p-3 cursor-pointer hover:border-[#4a3325] transition-all relative">
                            <div class="flex items-center gap-2">
                                <span class="text-[#b08d72]"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                                <span class="text-sm font-bold text-[#4a3728]">Maintenance Provider</span>
                            </div>
                            <input type="radio" name="role" value="maintenance" id="roleMaintenance" class="accent-[#4a3325]" onclick="handleRoleSwitch()">
                        </label>
                    </div>
                </div>

                {{-- 🏢 حقول المالك (Owner) --}}
                <div id="ownerFields" class="hidden space-y-4 pt-2 border-t border-[#ebdccb]">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">National ID (الرقم القومي)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b09a8a]"><i class="fa-regular fa-id-card"></i></span>
                            <input type="text" name="national_id" id="nationalIdInput" placeholder="14-digit National ID" maxlength="14"
                                class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Upload ID Photo (صورة البطاقة)</label>
                        <div class="relative flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-24 bg-[#fdfcfb] border border-[#dcd1c4] border-dashed rounded-xl cursor-pointer hover:bg-[#fcfaf7] hover:border-[#4a3325] transition-all">
                                <div class="flex flex-col items-center justify-center pt-2 pb-2 text-[#b09a8a]">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl mb-1 text-[#b08d72]"></i>
                                    <p class="text-xs font-bold text-[#6e5542]" id="fileLabelText">Click to upload National ID Photo</p>
                                </div>
                                <input type="file" name="id_card_image" id="idCardInput" class="hidden" accept="image/*" onchange="updateFileName()">
                            </label>
                        </div>
                    </div>
                </div>

                {{-- (Maintenance) --}}
                <div id="maintenanceFields" class="hidden space-y-4 pt-3 border-t border-[#ebdccb]" dir="rtl">
                    <div class="space-y-1.5 text-right">
                        <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider block">المحافظة</label>
                        <select name="governorate" id="maintenanceGovernorate" class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl px-4 py-3 text-sm text-[#4a3728] bg-white focus:outline-none focus:border-[#4a3325]">
                            <option value="" disabled selected>اختر المحافظة...</option>
                            <option value="أسيوط">أسيوط</option>
                            <option value="أسوان">أسوان</option>
                            <option value="الإسكندرية">الإسكندرية</option>
                            <option value="الإسماعيلية">الإسماعيلية</option>
                            <option value="الأقصر">الأقصر</option>
                            <option value="البحر الأحمر">البحر الأحمر</option>
                            <option value="البحيرة">البحيرة</option>
                            <option value="الجيزة">الجيزة</option>
                            <option value="الدقهلية">الدقهلية</option>
                            <option value="دمياط">دمياط</option>
                            <option value="سوهاج">سوهاج</option>
                            <option value="السويس">السويس</option>
                            <option value="الشرقية">الشرقية</option>
                            <option value="شمال سيناء">شمال سيناء</option>
                            <option value="الغربية">الغربية</option>
                            <option value="الفيوم">الفيوم</option>
                            <option value="القاهرة">القاهرة</option>
                            <option value="القليوبية">القليوبية</option>
                            <option value="قنا">قنا</option>
                            <option value="كفر الشيخ">كفر الشيخ</option>
                            <option value="مطروح">مطروح</option>
                            <option value="المنوفية">المنوفية</option>
                            <option value="المنيا">المنيا</option>
                            <option value="الوادي الجديد">الوادي الجديد</option>
                            <option value="بني سويف">بني سويف</option>
                            <option value="بور سعيد">بور سعيد</option>
                            <option value="جنوب سيناء">جنوب سيناء</option>
                        </select>
                    </div>

                    <div class="space-y-1.5 text-right">
                        <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider block">العنوان بالتفصيل / نطاق العمل</label>
                        <input type="text" name="location" id="maintenanceLocation" placeholder="العنوان بالتفصيل"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl px-4 py-3 text-sm text-[#4a3728] placeholder-[#b09a8a] focus:outline-none focus:border-[#4a3325]">
                    </div>

                    <div class="space-y-1.5 text-right">
                        <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider block">نوع الصيانة (التخصص الأساسي)</label>
                        <select name="maintenance_type" id="maintenanceType" class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl px-4 py-3 text-sm text-[#4a3728] bg-white focus:outline-none focus:border-[#4a3325]">
                            <option value="" disabled selected>اختر نوع الصيانة...</option>
                            <option value="electricity">كهرباء</option>
                            <option value="plumbing">سباكه وغاز </option>
                            <option value="gas">غاز وبوتاجازات</option>
                            <option value="carpentry">نجارة وأثاث</option>
                            <option value="climatization">تكييف وتبريد</option>
                            <option value="internet">شبكات وإنترنت</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b08d72]"><i class="fa-solid fa-lock"></i></span>

                        <input type="password" name="password" id="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="new-password"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-11 py-3 text-sm text-[#4a3728] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">

                        <button type="button" onclick="togglePassword('password', 'password-icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#b08d72] hover:text-[#4a3325] bg-transparent border-0 cursor-pointer outline-none">
                            <i id="password-icon" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#6e5542] uppercase tracking-wider">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#b08d72]"><i class="fa-solid fa-shield-halved"></i></span>

                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="w-full bg-[#fdfcfb] border border-[#dcd1c4] rounded-xl pl-11 pr-11 py-3 text-sm text-[#4a3728] focus:outline-none focus:border-[#4a3325] focus:ring-1 focus:ring-[#4a3325] transition-all">

                        <button type="button" onclick="togglePassword('password_confirmation', 'confirm-password-icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#b08d72] hover:text-[#4a3325] bg-transparent border-0 cursor-pointer outline-none">
                            <i id="confirm-password-icon" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#4a3325] text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md tracking-wide mt-2">
                    Create Account
                </button>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">OR</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('auth.google') }}"
                            class="w-full flex items-center justify-center gap-3 px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-700 hover:bg-gray-50 transition font-medium">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                            Continue with Google For Students
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-t border-[#ebdccb] mt-6 pt-4 text-center">
                <p class="text-sm text-[#8c7460] font-medium">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-bold text-[#4a3325] hover:text-[#38251a] transition-colors ml-1 underline">Sign In</a>
                </p>
            </div>

        </div>
    </main>

    <footer class="w-full text-center py-4 text-xs text-[#a08a75] z-10 relative font-medium">
        &copy; 2026 StayUni Housing Platform. All rights reserved.
    </footer>

    <script>
        function handleRoleSwitch() {
            const ownerRadio = document.querySelector('input[name="role"][value="owner"]');
            const maintenanceRadio = document.querySelector('input[name="role"][value="maintenance"]');

            const ownerFields = document.getElementById('ownerFields');
            const maintenanceFields = document.getElementById('maintenanceFields');

            const nationalIdInput = document.getElementById('nationalIdInput');
            const maintenanceGov = document.getElementById('maintenanceGovernorate');
            const maintenanceLoc = document.getElementById('maintenanceLocation');
            const maintenanceType = document.getElementById('maintenanceType');

            if (ownerRadio && ownerRadio.checked) {
                ownerFields.classList.remove('hidden');
                if (nationalIdInput) nationalIdInput.setAttribute('required', 'required');
            } else {
                ownerFields.classList.add('hidden');
                if (nationalIdInput) nationalIdInput.removeAttribute('required');
            }

            if (maintenanceRadio && maintenanceRadio.checked) {
                maintenanceFields.classList.remove('hidden');
                if (maintenanceGov) maintenanceGov.setAttribute('required', 'required');
                if (maintenanceLoc) maintenanceLoc.setAttribute('required', 'required');
                if (maintenanceType) maintenanceType.setAttribute('required', 'required');
            } else {
                maintenanceFields.classList.add('hidden');
                if (maintenanceGov) maintenanceGov.removeAttribute('required');
                if (maintenanceLoc) maintenanceLoc.removeAttribute('required');
                if (maintenanceType) maintenanceType.removeAttribute('required');
            }
        }

        function updateFileName() {
            const input = document.getElementById('idCardInput');
            const labelText = document.getElementById('fileLabelText');
            if (input.files && input.files.length > 0) {
                labelText.innerText = "Selected: " + input.files[0].name;
                labelText.classList.add('text-[#4a3325]', 'font-bold');
            }
        }

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            handleRoleSwitch();
        });
    </script>
</body>

</html>
