<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayUni | Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-[#fcfaf7] text-[#211208] antialiased">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-64 bg-[#211208] text-[#ebdccb] p-6 flex flex-col justify-between shrink-0 h-screen sticky top-0">
            <div class="space-y-8">
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-[#8b6f56] p-2 rounded-xl text-white shadow-md">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <span class="text-xl font-black text-white tracking-tight">StayUni</span>
                </div>

                <nav class="space-y-1">
                    <button onclick="switchTab('explore')" id="nav-explore" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all bg-[#8b6f56] text-white shadow-sm border-0 cursor-pointer text-left">
                        <i class="fa-solid fa-chart-pie text-sm"></i> Explore Properties
                    </button>
                    <button onclick="switchTab('favorites')" id="nav-favorites" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all text-[#ebdccb]/70 hover:bg-white/5 hover:text-white border-0 cursor-pointer text-left">
                        <i class="fa-solid fa-heart text-sm"></i> Favorites
                    </button>

                    <button onclick="switchTab('maintenance')" id="nav-maintenance" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all text-[#ebdccb]/70 hover:bg-white/5 hover:text-white border-0 cursor-pointer text-left">
                        <i class="fa-solid fa-screwdriver-wrench text-sm"></i> Maintenance Services
                    </button>
                </nav>
            </div>

            <div class="border-t border-white/10 pt-4 flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#8b6f56] text-white flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                        {{ substr(Auth::user()->name ?? 'S', 0, 2) }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-white line-clamp-1">{{ Auth::user()->name ?? 'Student' }}</p>
                        <p class="text-[10px] text-[#8c7460] font-medium">Student Account</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#8c7460] hover:text-red-400 transition-colors p-1 bg-transparent border-0 cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto w-full space-y-8 overflow-y-auto">

            {{-- 1️ SECTION: EXPLORE PROPERTIES --}}
            <div id="tab-explore" class="space-y-8 tab-content">
                <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#ebdccb]/40">
                    <div>
                        <h1 class="text-2xl font-black text-[#211208] tracking-tight">Available Accommodations</h1>
                        <p class="text-xs text-[#8c7460] font-medium mt-0.5">Find the perfect student housing near your university.</p>
                    </div>
                </header>


                {{-- 1. SECTION: FILTERING CONTROLS --}}
                <div class="bg-white p-4 rounded-2xl border border-[#eaddcf]/60 shadow-sm mb-8 max-w-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- فلتر المحافظة --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-extrabold text-[#4a3728] uppercase tracking-wider ml-1">Filter by Governorate</label>
                            <select id="filter_governorate" onchange="applyFilters()" class="w-full bg-[#fdfcfb] border border-[#ebdccb] rounded-xl px-4 py-2.5 text-xs font-bold text-[#4a3728] outline-none focus:ring-1 focus:ring-[#8b6f56]">
                                <option value="all">All Governorates</option>
                                <option value="cairo">Cairo</option>
                                <option value="giza">Giza</option>
                                <option value="alexandria">Alexandria</option>
                                <option value="asyut">Asyut</option>
                                <option value="sohag">Sohag</option>
                                <option value="qena">Qena</option>
                                <option value="luxor">Luxor</option>
                                <option value="aswan">Aswan</option>
                                <option value="minya">Minya</option>
                                <option value="beni_suef">Beni Suef</option>
                                <option value="fayoum">Fayoum</option>
                                <option value="dakahlia">Dakahlia</option>
                                <option value="gharbia">Gharbia</option>
                                <option value="sharkia">Sharkia</option>
                                <option value="beheira">Beheira</option>
                                <option value="kafr_el_sheikh">Kafr El Sheikh</option>
                                <option value="monufia">Monufia</option>
                                <option value="damietta">Damietta</option>
                                <option value="port_said">Port Said</option>
                                <option value="ismailia">Ismailia</option>
                                <option value="suez">Suez</option>
                                <option value="matrouh">Matrouh</option>
                                <option value="red_sea">Red Sea</option>
                                <option value="new_valley">New Valley</option>
                                <option value="north_sinai">North Sinai</option>
                                <option value="south_sinai">South Sinai</option>
                                <option value="qalyubia">Qalyubia</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-extrabold text-[#4a3728] uppercase tracking-wider ml-1">Target Renter</label>
                            <div class="flex gap-2 p-1 bg-[#f7f4f0] rounded-2xl border border-[#eaddcf]/50">
                                <button onclick="filterGender('all', this)" class="gender-btn flex-1 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 bg-[#8b6f56] text-white shadow-sm">
                                    All
                                </button>
                                <button onclick="filterGender('male', this)" class="gender-btn flex-1 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 text-[#8b6f56] hover:bg-[#eaddcf]">
                                    Males Only
                                </button>
                                <button onclick="filterGender('female', this)" class="gender-btn flex-1 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 text-[#8b6f56] hover:bg-[#eaddcf]">
                                    Females Only
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                @if(isset($properties) && $properties->count() > 0)
                <div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" id="properties-container">
                        @foreach($properties as $property)
                        @php
                        $imgs = $property->images ?? $property->property_images ?? [];
                        if (is_string($imgs)) $imgs = json_decode($imgs, true) ?? [];
                        $imgCount = count($imgs);
                        @endphp

                        <div class="property-card bg-white border border-[#eaddcf]/70 rounded-[24px] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                            data-id="{{ $property->id }}"
                            data-gender="{{ $property->gender_type }}"
                            data-gov="{{ strtolower(trim($property->governorate)) }}">

                            <div class="p-6 space-y-4">
                                <div class="relative w-full h-60 bg-[#f7f4f0] rounded-2xl overflow-hidden group shadow-inner border border-[#f0eae1]" id="student-slider-{{ $property->id }}">
                                    @if($imgCount > 0)
                                    @foreach($imgs as $i => $img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="Property Image"
                                        class="absolute inset-0 w-full h-full object-cover transition-all duration-500 transform {{ $i == 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-95 hidden' }}"
                                        data-student-index="{{ $i }}">
                                    @endforeach

                                    @if($imgCount > 1)
                                    <button type="button" onclick="studentSlideImg({{ $property->id }}, -1)" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-[#4a3728] w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-sm transition-all duration-300 shadow-md border-0 cursor-pointer z-10">
                                        <i class="fa-solid fa-chevron-left text-xs"></i>
                                    </button>
                                    <button type="button" onclick="studentSlideImg({{ $property->id }}, 1)" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-[#4a3728] w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-sm transition-all duration-300 shadow-md border-0 cursor-pointer z-10">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </button>
                                    @endif
                                    @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-[#b08d72] gap-2">
                                        <i class="fa-solid fa-images text-4xl opacity-30"></i>
                                        <p class="text-xs font-medium text-[#b09a8a]">No images uploaded</p>
                                    </div>
                                    @endif

                                    <div class="absolute top-3 inset-x-3 flex justify-between items-center z-10">
                                        <span class="text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm {{ $property->gender_type === 'male' ? 'bg-[#211208] text-white' : 'bg-[#c2410c] text-white' }}">
                                            {{ $property->gender_type === 'male' ? '♂ Males Only' : '♀ Females Only' }}
                                        </span>
                                        <button type="button" onclick="toggleFavorite({{ $property->id }})" id="fav-btn-{{ $property->id }}" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center text-stone-400 hover:text-red-500 shadow-md border-0 cursor-pointer transition-colors">
                                            <i class="fa-solid fa-heart text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex justify-between items-start gap-4 pt-1">
                                    <div class="space-y-1">
                                        <h3 class="font-extrabold text-[#211208] text-base tracking-tight leading-snug">{{ $property->title }}</h3>
                                        <p class="text-xs text-[#8c7460] flex items-center gap-1 font-medium">
                                            <i class="fa-solid fa-location-dot text-[#b08d72] text-xs"></i> {{ ucfirst($property->governorate) }} - {{ $property->location ?? $property->address }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 bg-[#fdfcfb] border border-[#eaddcf]/50 px-3 py-2 rounded-xl shrink-0 text-[11px] font-bold text-[#6e5542]">
                                        <span class="flex items-center gap-1"><i class="fa-solid fa-bed text-[#b08d72]"></i>{{ $property->bedrooms ?? $property->rooms }}</span>
                                        <span class="flex items-center gap-1"><i class="fa-solid fa-bath text-[#b08d72]"></i>{{ $property->bathrooms }}</span>
                                        <span class="flex items-center gap-1"><i class="fa-solid fa-person-walking text-[#b08d72]"></i>{{ $property->proximity }}m</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1 border-t border-[#f5f0ea]">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($property->is_furnished)
                                        <span class="bg-stone-50 text-stone-600 border border-stone-200/60 text-[10px] font-bold px-2.5 py-0.5 rounded-md">✓ Furnished</span>
                                        @endif
                                        @if($property->utilities_included)
                                        <span class="bg-stone-50 text-stone-600 border border-stone-200/60 text-[10px] font-bold px-2.5 py-0.5 rounded-md">✓ Bills Included</span>
                                        @endif
                                        <span class="bg-stone-50 text-stone-600 border border-stone-200/60 text-[10px] font-bold px-2.5 py-0.5 rounded-md">Floor {{ $property->floor ?? 0 }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-base font-black text-[#8b6f56] tracking-tight">{{ number_format($property->price) }}</span>
                                        <span class="text-[10px] font-bold text-[#8c7460] block -mt-1">EGP/month</span>
                                    </div>
                                </div>

                                @if($property->description)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-extrabold text-[#8c7460] uppercase tracking-wider">Owner's Description:</span>
                                    <p class="text-xs text-[#6e5542]/90 leading-relaxed bg-[#fdfcfb] p-3 rounded-xl border border-[#f5f0ea]">
                                        {{ $property->description }}
                                    </p>
                                </div>
                                @endif

                                {{-- ===== OWNER INFO SECTION ===== --}}
                                <div class="mt-4 pt-3 border-t border-[#f5f0ea] space-y-1.5">
                                    {{-- اسم المالك --}}
                                    <div class="flex items-center gap-2 text-xs text-[#4a3728] font-medium">
                                        <i class="fa-solid fa-user text-[#b08d72] w-4 text-center"></i>
                                        <span>المالك: <strong class="text-[#211208]">{{ $property->owner->name ?? 'غير معروف' }}</strong></span>
                                    </div>

                                    {{-- رقم الهاتف --}}
                                    <div class="flex items-center gap-2 text-xs text-[#4a3728] font-medium">
                                        <i class="fa-solid fa-phone text-[#b08d72] w-4 text-center"></i>
                                        @if($property->owner && $property->owner->phone)
                                        <a href="tel:{{ $property->owner->phone }}" class="text-blue-600 hover:underline font-mono font-bold tracking-wide">
                                            {{ $property->owner->phone }}
                                        </a>
                                        @else
                                        <span class="text-stone-400 italic">غير متوفر</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- زر الـ WhatsApp --}}
                            <div class="px-6 pb-6 pt-2">
                                @php
                                $ownerPhone = $property->owner->phone ?? $property->user->phone ?? $property->phone ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $ownerPhone);
                                if (strpos($cleanPhone, '0') === 0 && strpos($cleanPhone, '2') !== 0) {
                                $cleanPhone = '2' . $cleanPhone;
                                }
                                $propertyLabel = $property->location ?? ('Property #' . $property->id);
                                $whatsappMessage = urlencode("Hello! I am interested in your property listed on StayUni: " . $propertyLabel);
                                @endphp

                                @if(!empty($cleanPhone))
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $whatsappMessage }}" target="_blank" onclick="logRequest('{{ addslashes($propertyLabel) }}', '{{ addslashes($property->location ?? '') }}')"
                                    class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba5a] text-white py-3 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-lg text-center cursor-pointer border-0">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> Contact via WhatsApp
                                </a>
                                @else
                                <button disabled class="w-full flex items-center justify-center gap-2 bg-stone-200 text-stone-400 py-3 rounded-xl text-xs font-bold border-0 cursor-not-allowed">
                                    <i class="fa-solid fa-phone-slash text-xs"></i> Owner contact unavailable
                                </button>
                                @endif
                            </div>

                        </div>
                        @endforeach


                    </div>
                            <div class="mt-8">

                        {{ $properties->links() }}

                    </div>



                    <div id="no-filter-results" class="hidden bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5 mt-6">
                        <div class="bg-[#fcfaf7] w-16 h-16 flex items-center justify-center text-[#b08d72] text-3xl rounded-2xl mx-auto border border-[#ebdccb]">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-[#4a3728]">No matching properties</h3>
                            <p class="text-sm text-[#8c7460] leading-relaxed mt-1">Try changing your gender filter settings.</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5 mt-6">
                    <div class="bg-[#fcfaf7] w-16 h-16 flex items-center justify-center text-[#b08d72] text-3xl rounded-2xl mx-auto border border-[#ebdccb]"><i class="fa-solid fa-building"></i></div>
                    <div>
                        <h3 class="text-lg font-extrabold text-[#4a3728]">No properties available</h3>
                        <p class="text-sm text-[#8c7460] leading-relaxed mt-1">Check back later for new student accommodations.</p>
                    </div>
                </div>
                @endif
            </div>


            {{-- 2️ SECTION: FAVORITES TAB --}}
            <div id="tab-favorites" class="space-y-8 tab-content hidden">
                <header class="pb-4 border-b border-[#ebdccb]/40">
                    <h1 class="text-2xl font-black text-[#211208] tracking-tight">My Saved Favorites</h1>
                    <p class="text-xs text-[#8c7460] font-medium mt-0.5">Your shortlisted properties saved for quick access.</p>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" id="favorites-container">
                </div>

                <div id="empty-favorites" class="bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5">
                    <div>
                        <h3 class="text-lg font-extrabold text-[#4a3728]">Your Favorites list is empty</h3>
                        <p class="text-sm text-[#8c7460] leading-relaxed mt-1">Tap the heart icon on any accommodation card to save it here.</p>
                    </div>
                </div>
            </div>




            {{-- 4️ SECTION: MAINTENANCE TAB --}}
            <div id="tab-maintenance" class="space-y-8 tab-content hidden">
                <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#ebdccb]/40">
                    <div>
                        <h1 class="text-2xl font-black text-[#211208] tracking-tight">Available Maintenance Providers</h1>
                        <p class="text-xs text-[#8c7460] font-medium mt-0.5">Find trusted maintenance professionals near you.</p>
                    </div>
                </header>

                {{-- الفلاتر: المحافظة ونوع الصيانة --}}
                <div class="flex flex-wrap gap-4 bg-white p-4 rounded-2xl border border-[#eaddcf]/60 shadow-sm">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-extrabold text-[#8c7460] uppercase tracking-wider mb-1">Governorate</label>
                        <select id="filter-governorate" onchange="filterMaintenance()" class="w-full bg-[#fcfaf7] border border-[#eaddcf] rounded-xl px-3 py-2 text-xs font-bold text-[#211208] outline-none">
                            <option value="all">All Governorates</option>
                            @foreach([
                            'cairo' => 'Cairo / القاهرة',
                            'giza' => 'Giza / الجيزة',
                            'alexandria' => 'Alexandria / الإسكندرية',
                            'qalyubia' => 'Qalyubia / القليوبية',
                            'gharbia' => 'Gharbia / الغربية',
                            'dakahlia' => 'Dakahlia / الدقهلية',
                            'menofia' => 'Menofia / المنوفية',
                            'sharqia' => 'Sharqia / الشرقية',
                            'beheira' => 'Beheira / البحيرة',
                            'damietta' => 'Damietta / دمياط',
                            'matrouh' => 'Matrouh / مطروح',
                            'shakshia' => 'Kafr El-Sheikh / كفر الشيخ',
                            'fayoum' => 'Fayoum / الفيوم',
                            'beni_suef' => 'Beni Suef / بني سويف',
                            'minya' => 'Minya / المنيا',
                            'assiut' => 'Assiut / أسيوط',
                            'sohag' => 'Sohag / سوهاج',
                            'qena' => 'Qena / قنا',
                            'luxor' => 'Luxor / الأقصر',
                            'aswan' => 'Aswan / أسوان',
                            'red_sea' => 'Red Sea / البحر الأحمر',
                            'new_valley' => 'New Valley / الوادي الجديد',
                            'north_sinai' => 'North Sinai / شمال سيناء',
                            'south_sinai' => 'South Sinai / جنوب سيناء',
                            'port_said' => 'Port Said / بورسعيد',
                            'ismailia' => 'Ismailia / الإسماعيلية',
                            'suez' => 'Suez / السويس'
                            ] as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-extrabold text-[#8c7460] uppercase tracking-wider mb-1">Maintenance Type</label>
                        <select id="filter-type" onchange="filterMaintenance()" class="w-full bg-[#fcfaf7] border border-[#eaddcf] rounded-xl px-3 py-2 text-xs font-bold text-[#211208] outline-none">
                            <option value="all">All Types</option>
                            <option value="plumbing">Plumbing / سباكة</option>
                            <option value="electricity">Electricity / كهرباء</option>
                            <option value="carpentry">Carpentry / نجارة</option>
                            <option value="air_conditioning">Air Conditioning / تكييفات</option>
                            <option value="painting">Painting / نقاشة</option>
                        </select>
                    </div>
                </div>

                @if(isset($maintenanceProviders) && $maintenanceProviders->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" id="maintenance-container">
                    @foreach($maintenanceProviders as $provider)
                    @php
                    $typeLabels = [
                    'plumbing' => 'سباكة',
                    'electricity' => 'كهرباء',
                    'carpentry' => 'نجارة',
                    'air_conditioning' => 'تكييفات',
                    'painting' => 'نقاشة',
                    ];
                    $govLabels = [
                    'cairo' => 'القاهرة',
                    'giza' => 'الجيزة',
                    'alexandria' => 'الإسكندرية',
                    'qalyubia' => 'القليوبية',
                    'gharbia' => 'الغربية',
                    'dakahlia' => 'الدقهلية',
                    'menofia' => 'المنوفية',
                    'sharqia' => 'الشرقية',
                    'beheira' => 'البحيرة',
                    'damietta' => 'دمياط',
                    'matrouh' => 'مطروح',
                    'shakshia' => 'كفر الشيخ',
                    'fayoum' => 'الفيوم',
                    'beni_suef' => 'بني سويف',
                    'minya' => 'المنيا',
                    'assiut' => 'أسيوط',
                    'sohag' => 'سوهاج',
                    'qena' => 'قنا',
                    'luxor' => 'الأقصر',
                    'aswan' => 'أسوان',
                    'red_sea' => 'البحر الأحمر',
                    'new_valley' => 'الوادي الجديد',
                    'north_sinai' => 'شمال سيناء',
                    'south_sinai' => 'جنوب سيناء',
                    'port_said' => 'بورسعيد',
                    'ismailia' => 'الإسماعيلية',
                    'suez' => 'السويس'
                    ];
                    $providerType = strtolower($provider->maintenance_type ?? '');
                    $providerGov = strtolower($provider->governorate ?? '');

                    $waPhone = preg_replace('/\D/', '', $provider->phone ?? '');
                    if (strlen($waPhone) === 11 && substr($waPhone, 0, 1) === '0') {
                    $waPhone = '2' . $waPhone;
                    }
                    $waMessage = urlencode('السلام عليكم، عايز أطلب خدمة ' . ($typeLabels[$providerType] ?? $provider->maintenance_type));
                    @endphp
                    <div class="provider-card bg-white border border-[#eaddcf]/70 rounded-[24px] p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                        data-governorate="{{ $providerGov }}"
                        data-type="{{ $providerType }}">

                        <div class="space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-[#4a3325] text-[#ebdccb] flex items-center justify-center font-bold text-base shadow-md">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-[#211208] text-base tracking-tight">{{ $provider->name }}</h3>
                                        <span class="inline-block bg-[#8b6f56]/10 text-[#8b6f56] text-[10px] font-extrabold px-2.5 py-0.5 rounded-md uppercase mt-1">
                                            {{ $typeLabels[$providerType] ?? ($provider->maintenance_type ?? 'Maintenance') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-[#f5f0ea]">

                            <div class="space-y-2 text-xs text-[#6e5542] font-medium">
                                <p class="flex items-start gap-2">
                                    <i class="fa-solid fa-location-dot text-[#b08d72] mt-0.5 shrink-0"></i>
                                    <span><strong>المحافظة:</strong> {{ $govLabels[$providerGov] ?? $provider->governorate }}</span>
                                </p>
                                @if($provider->location)
                                <p class="flex items-start gap-2">
                                    <i class="fa-solid fa-map-location-dot text-[#b08d72] mt-0.5 shrink-0"></i>
                                    <span><strong>العنوان بالتفصيل:</strong> {{ $provider->location }}</span>
                                </p>
                                @endif
                                <p class="flex items-start gap-2">
                                    <i class="fa-solid fa-phone text-[#b08d72] mt-0.5 shrink-0"></i>
                                    <span><strong>رقم التليفون:</strong> {{ $provider->phone }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-[#f5f0ea] grid grid-cols-2 gap-2">
                            <a href="tel:{{ $provider->phone }}" class="flex items-center justify-center gap-2 bg-[#211208] hover:bg-[#4a3325] text-white py-3 rounded-xl text-xs font-bold transition-all shadow-sm text-center">
                                <i class="fa-solid fa-phone text-xs"></i> اتصال
                            </a>
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba5a] text-white py-3 rounded-xl text-xs font-bold transition-all shadow-sm text-center">
                                <i class="fa-brands fa-whatsapp text-xs"></i> واتساب
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="no-maintenance-results" class="hidden bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5">
                    <div class="bg-[#fcfaf7] w-16 h-16 flex items-center justify-center text-[#b08d72] text-3xl rounded-2xl mx-auto border border-[#ebdccb]">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-[#4a3728]">No providers found</h3>
                        <p class="text-sm text-[#8c7460] leading-relaxed mt-1">Try resetting or changing your search filters.</p>
                    </div>
                </div>
                @else
                <div class="bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5">
                    <div class="bg-[#fcfaf7] w-16 h-16 flex items-center justify-center text-[#b08d72] text-3xl rounded-2xl mx-auto border border-[#ebdccb]">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-[#4a3728]">No providers registered</h3>
                        <p class="text-sm text-[#8c7460] leading-relaxed mt-1">There are no maintenance providers registered on the platform at the moment.</p>
                    </div>
                </div>
                @endif
            </div>

        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            updateHeartIconsUI();
            loadRequestsTable();
        });

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            document.getElementById(`tab-${tabName}`).classList.remove('hidden');

            const tabs = ['explore', 'favorites', 'requests', 'maintenance'];
            tabs.forEach(t => {
                const btn = document.getElementById(`nav-${t}`);
                if (t === tabName) {
                    btn.className = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all bg-[#8b6f56] text-white shadow-sm border-0 cursor-pointer text-left";
                } else {
                    btn.className = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all text-[#ebdccb]/70 hover:bg-white/5 hover:text-white border-0 cursor-pointer text-left";
                }
            });

            if (tabName === 'favorites') {
                renderFavorites();
            }
        }

        function getFavorites() {
            return JSON.parse(localStorage.getItem('stayuni_favs')) || [];
        }

        function toggleFavorite(id) {
            let favs = getFavorites();
            const index = favs.indexOf(id);

            if (index === -1) {
                favs.push(id);
            } else {
                favs.splice(index, 1);
            }

            localStorage.setItem('stayuni_favs', JSON.stringify(favs));
            updateHeartIconsUI();

            if (!document.getElementById('tab-favorites').classList.contains('hidden')) {
                renderFavorites();
            }
        }

        function updateHeartIconsUI() {
            const favs = getFavorites();
            document.querySelectorAll('[id^="fav-btn-"]').forEach(btn => {
                const id = parseInt(btn.id.replace('fav-btn-', ''));
                const icon = btn.querySelector('i');
                if (favs.includes(id)) {
                    icon.className = "fa-solid fa-heart text-sm text-red-500";
                    btn.classList.add('text-red-500');
                } else {
                    icon.className = "fa-solid fa-heart text-sm text-stone-400";
                    btn.classList.remove('text-red-500');
                }
            });
        }

        function renderFavorites() {
            const favs = getFavorites();
            const container = document.getElementById('favorites-container');
            const emptyMsg = document.getElementById('empty-favorites');

            container.innerHTML = '';

            let matchedCount = 0;

            document.querySelectorAll('#properties-container .property-card').forEach(card => {
                const id = parseInt(card.getAttribute('data-id'));
                if (favs.includes(id)) {
                    const clone = card.cloneNode(true);

                    const oldSliderId = clone.querySelector('[id^="student-slider-"]').id;
                    clone.querySelector('[id^="student-slider-"]').id = oldSliderId + "-fav";

                    const sliderButtons = clone.querySelectorAll('#' + oldSliderId + "-fav button");
                    if (sliderButtons.length > 0) {
                        sliderButtons[0].setAttribute('onclick', `studentSlideImgFav(${id}, -1)`);
                        sliderButtons[1].setAttribute('onclick', `studentSlideImgFav(${id}, 1)`);
                    }

                    const favBtn = clone.querySelector('[id^="fav-btn-"]');
                    favBtn.id = `fav-btn-${id}-fav`;
                    favBtn.setAttribute('onclick', `toggleFavorite(${id})`);

                    container.appendChild(clone);
                    matchedCount++;
                }
            });

            if (matchedCount === 0) {
                emptyMsg.classList.remove('hidden');
                container.classList.add('hidden');
            } else {
                emptyMsg.classList.add('hidden');
                container.classList.remove('hidden');
            }
        }

        function studentSlideImgFav(propertyId, direction) {
            const container = document.getElementById(`student-slider-${propertyId}-fav`);
            if (!container) return;
            const images = container.querySelectorAll('img[data-student-index]');
            if (images.length <= 1) return;
            let currentIndex = -1;
            images.forEach((img, index) => {
                if (img.classList.contains('opacity-100')) currentIndex = index;
            });
            if (currentIndex === -1) return;
            images[currentIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-0 scale-95 hidden";
            let newIndex = currentIndex + direction;
            if (newIndex >= images.length) newIndex = 0;
            if (newIndex < 0) newIndex = images.length - 1;
            images[newIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-100 scale-100";
        }

        function logRequest(title, location) {
            let requests = JSON.parse(localStorage.getItem('stayuni_requests')) || [];
            const newReq = {
                title: title,
                location: location,
                date: new Date().toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                }),
                status: 'Opened via WhatsApp'
            };
            requests.unshift(newReq);
            localStorage.setItem('stayuni_requests', JSON.stringify(requests));
            loadRequestsTable();
        }



        let currentGender = 'all';

        function filterGender(gender, element) {
            currentGender = gender;

            document.querySelectorAll('.gender-btn').forEach(btn => {
                btn.classList.remove('bg-[#8b6f56]', 'text-white', 'shadow-sm');
                btn.classList.add('text-[#8b6f56]', 'hover:bg-[#eaddcf]');
            });

            element.classList.add('bg-[#8b6f56]', 'text-white', 'shadow-sm');
            element.classList.remove('text-[#8b6f56]', 'hover:bg-[#eaddcf]');

            applyFilters();
        }

        document.addEventListener("DOMContentLoaded", () => {
            applyFilters();
        });

        function applyFilters() {
            const selectedGov = document.getElementById('filter_governorate').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.property-card');
            let hasVisible = false;

            cards.forEach(card => {
                const cardGov = (card.getAttribute('data-gov') || '').toLowerCase().trim();
                const cardGender = card.getAttribute('data-gender');

                const matchesGov = (selectedGov === 'all' || cardGov === selectedGov);
                const matchesGender = (currentGender === 'all' || cardGender === currentGender);

                if (matchesGov && matchesGender) {
                    card.style.setProperty('display', 'flex', 'important');
                    hasVisible = true;
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });

            const noResults = document.getElementById('no-filter-results');
            if (noResults) {
                noResults.style.display = hasVisible ? 'none' : 'block';
            }
        }


        function studentSlideImg(propertyId, direction) {
            const container = document.getElementById(`student-slider-${propertyId}`);
            if (!container) return;
            const images = container.querySelectorAll('img[data-student-index]');
            if (images.length <= 1) return;
            let currentIndex = -1;
            images.forEach((img, index) => {
                if (img.classList.contains('opacity-100')) currentIndex = index;
            });
            if (currentIndex === -1) return;
            images[currentIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-0 scale-95 hidden";
            let newIndex = currentIndex + direction;
            if (newIndex >= images.length) newIndex = 0;
            if (newIndex < 0) newIndex = images.length - 1;
            images[newIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-100 scale-100";
        }


        function filterMaintenance() {
            const selectedGov = document.getElementById('filter-governorate').value.toLowerCase().trim();
            const selectedType = document.getElementById('filter-type').value.toLowerCase().trim();

            const cards = document.querySelectorAll('.provider-card');
            let hasResults = false;

            cards.forEach(card => {
                const cardGov = (card.getAttribute('data-governorate') || '').toLowerCase().trim();
                const cardType = (card.getAttribute('data-type') || '').toLowerCase().trim();

                const matchesGov = (selectedGov === 'all') ||
                    cardGov.includes(selectedGov) ||
                    (selectedGov === 'assiut' && (cardGov.includes('أسيوط') || cardGov.includes('assiut'))) ||
                    (selectedGov === 'cairo' && (cardGov.includes('قاهرة') || cardGov.includes('القاهرة') || cardGov.includes('cairo'))) ||
                    (selectedGov === 'giza' && (cardGov.includes('جيزة') || cardGov.includes('الجيزة') || cardGov.includes('giza'))) ||
                    (selectedGov === 'alexandria' && (cardGov.includes('إسكندرية') || cardGov.includes('الاسكندرية') || cardGov.includes('alexandria')));

                const matchesType = (selectedType === 'all') ||
                    cardType.includes(selectedType) ||
                    (selectedType === 'plumbing' && (cardType.includes('سباك') || cardType.includes('سباكة'))) ||
                    (selectedType === 'electricity' && (cardType.includes('كهربا') || cardType.includes('كهربائي') || cardType.includes('كهرباء'))) ||
                    (selectedType === 'carpentry' && (cardType.includes('نجار') || cardType.includes('نجارة'))) ||
                    (selectedType === 'air conditioning' && (cardType.includes('تكييف') || cardType.includes('تكييفات'))) ||
                    (selectedType === 'painting' && (cardType.includes('نقاش') || cardType.includes('نقاشة')));

                if (matchesGov && matchesType) {
                    card.style.setProperty('display', 'flex', 'important'); // يظهر كـ flex لأن تنسيق الكارت يعتمد عليه
                    hasResults = true;
                } else {
                    card.style.setProperty('display', 'none', 'important'); // يخفيه تماماً
                }
            });

            const noResultsMsg = document.getElementById('no-maintenance-results');
            const container = document.getElementById('maintenance-container');

            if (noResultsMsg) {
                if (hasResults) {
                    noResultsMsg.classList.add('hidden');
                    if (container) container.style.setProperty('display', 'grid', 'important');
                } else {
                    noResultsMsg.classList.remove('hidden');
                    if (container) container.style.setProperty('display', 'none', 'important');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            filterMaintenance();
        });


        function studentSlideImgFav(propertyId, direction) {
            const container = document.getElementById(`student-slider-${propertyId}-fav`);
            if (!container) return;
            const images = container.querySelectorAll('img[data-student-index]');
            if (images.length <= 1) return;
            let currentIndex = -1;
            images.forEach((img, index) => {
                if (img.classList.contains('opacity-100')) currentIndex = index;
            });
            if (currentIndex === -1) return;
            images[currentIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-0 scale-95 hidden";
            let newIndex = currentIndex + direction;
            if (newIndex >= images.length) newIndex = 0;
            if (newIndex < 0) newIndex = images.length - 1;
            images[newIndex].className = "absolute inset-0 w-full h-full object-cover transition-all duration-500 transform opacity-100 scale-100";
        }
    </script>
</body>

</html>
