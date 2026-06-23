<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayUni | Owner Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

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
        <aside class="w-64 bg-[#211208] text-[#ebdccb] p-6 flex flex-col justify-between shrink-0 hidden md:flex">
            <div class="space-y-8">
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-[#8b6f56] p-2 rounded-xl text-white shadow-md">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <span class="text-xl font-black text-white tracking-tight">StayUni</span>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all bg-[#8b6f56] text-white shadow-sm">
                        <i class="fa-solid fa-chart-pie text-sm"></i> Dashboard
                    </a>
                </nav>
            </div>

            <div class="space-y-4">
                @if(method_exists(auth()->user(), 'isImpersonating') && auth()->user()->isImpersonating())
                <div class="bg-amber-500 text-white text-center py-2 font-bold text-xs flex flex-col justify-center items-center gap-2 rounded-xl p-2">
                    <span>(Admin Mode)</span>
                    <a href="{{ route('impersonate.leave') }}" class="bg-white text-amber-700 px-3 py-1 rounded-lg hover:bg-amber-50 transition-all font-black w-full text-center">
                        Return to Admin
                    </a>
                </div>
                @endif

                <div class="border-t border-white/10 pt-4 flex items-center justify-between px-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#8b6f56] text-white flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                            {{ substr(Auth::user()->name ?? 'O', 0, 2) }}
                        </div>
                        <div>
                            <p class="text-xs font-black text-white line-clamp-1">{{ Auth::user()->name ?? 'Owner' }}</p>
                            <p class="text-[10px] text-[#8c7460] font-medium">Host Account</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[#8c7460] hover:text-red-400 transition-colors p-1 bg-transparent border-0 cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto w-full space-y-8 overflow-y-auto">

            {{-- Header --}}
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#ebdccb]/40">
                <div>
                    <h1 class="text-2xl font-black text-[#211208] tracking-tight">Owner Dashboard</h1>
                    <p class="text-xs text-[#8c7460] font-medium mt-0.5">Manage your listings and student requests.</p>
                </div>
                <button type="button" onclick="openAddModal()"
                    class="bg-[#8b6f56] hover:bg-[#765e49] text-white font-bold py-3 px-5 rounded-xl text-xs transition-all shadow-md hover:shadow-lg border-0 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Add New Property
                </button>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl text-green-700 text-xs font-bold shadow-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-xs font-bold shadow-sm">
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-xs font-bold space-y-1 shadow-sm">
                <p class="font-black">Please fix the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Properties Grid --}}
            @if(isset($properties) && $properties->count() > 0)
            <div>
                <h2 class="text-xs font-black text-[#6e5542] uppercase tracking-wider mb-5">My Listed Properties</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($properties as $property)
                    @php
                    $imgs = $property->images ?? [];
                    if (is_string($imgs)) $imgs = json_decode($imgs, true) ?? [];
                    $imgCount = count($imgs);

                    $statusConfig = [
                    'approved' => ['bg-green-50 text-green-700 border border-green-200', 'fa-circle-check', 'Approved'],
                    'pending' => ['bg-yellow-50 text-yellow-700 border border-yellow-200','fa-clock', 'Pending Review'],
                    'rejected' => ['bg-red-50 text-red-700 border border-red-200', 'fa-circle-xmark', 'Rejected'],
                    'rented' => ['bg-blue-50 text-blue-700 border border-blue-200', 'fa-key', 'Rented'],
                    ];
                    $sc = $statusConfig[$property->status] ?? ['bg-gray-50 text-gray-600 border border-gray-200', 'fa-circle-question', ucfirst($property->status)];

                    $overlayConfig = [
                    'approved' => 'bg-green-500 text-white',
                    'pending' => 'bg-amber-400 text-amber-900',
                    'rejected' => 'bg-red-500 text-white',
                    'rented' => 'bg-blue-600 text-white',
                    ];
                    $oc = $overlayConfig[$property->status] ?? 'bg-gray-400 text-white';

                    $overlayLabel = [
                    'approved' => '✓ Approved',
                    'pending' => '⏳ Pending Review',
                    'rejected' => '✗ Rejected',
                    'rented' => '🔑 Rented',
                    ];
                    $ol = $overlayLabel[$property->status] ?? ucfirst($property->status);
                    @endphp

                    <div class="bg-white border border-[#eaddcf]/70 rounded-[24px] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between
                    {{ $property->status === 'rented' ? 'opacity-75 ring-2 ring-blue-200' : '' }}">

                        <div class="p-6 space-y-4">

                            {{-- Rented banner / Activation --}}
                            @if($property->status === 'rented')
                            <form method="POST" action="{{ route('owner.properties.activate', $property->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all cursor-pointer">
                                    <i class="fa-solid fa-eye mr-1"></i> Make Available
                                </button>
                            </form>
                            @endif

                            {{-- Image Slider --}}
                            <div class="relative w-full h-60 bg-[#f7f4f0] rounded-2xl overflow-hidden group shadow-inner border border-[#f0eae1]" id="slider-{{ $property->id }}">
                                @if($imgCount > 0)
                                @foreach($imgs as $i => $img)
                                <img src="{{ asset('storage/' . $img) }}" alt="Property image"
                                    class="absolute inset-0 w-full h-full object-cover transition-all duration-500 transform {{ $i == 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-95' }}"
                                    data-slide-index="{{ $i }}">
                                @endforeach
                                @if($imgCount > 1)
                                <button type="button" onclick="slideImg({{ $property->id }}, -1)"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-[#4a3728] w-9 h-9 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-md border-0 cursor-pointer">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                                <button type="button" onclick="slideImg({{ $property->id }}, 1)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-[#4a3728] w-9 h-9 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-md border-0 cursor-pointer">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                                @endif
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-[#b08d72] gap-2">
                                    <i class="fa-solid fa-images text-4xl opacity-30"></i>
                                    <p class="text-xs font-medium text-[#b09a8a]">No images uploaded</p>
                                </div>
                                @endif

                                {{-- Gender badge --}}
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm {{ $property->gender_type === 'male' ? 'bg-[#211208] text-white' : 'bg-[#c2410c] text-white' }}">
                                        {{ $property->gender_type === 'male' ? '♂ Males Only' : '♀ Females Only' }}
                                    </span>
                                </div>

                                {{-- Status overlay badge --}}
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm {{ $oc }}">
                                        {{ $ol }}
                                    </span>
                                </div>
                            </div>

                            {{-- Title & specs --}}
                            <div class="flex justify-between items-start gap-4 pt-1">
                                <div class="space-y-1">
                                    <p class="text-xs text-[#8c7460] flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-location-dot text-[#b08d72] text-xs"></i>
                                        {{ $property->governorate ? ucfirst($property->governorate) . ' — ' : '' }}{{ $property->location }}
                                    </p>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md {{ $sc[0] }}">
                                        <i class="fa-solid {{ $sc[1] }} text-[9px]"></i> {{ $sc[2] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 bg-[#fdfcfb] border border-[#eaddcf]/50 px-3 py-2 rounded-xl shrink-0 text-[11px] font-bold text-[#6e5542]">
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-bed text-[#b08d72]"></i>{{ $property->bedrooms }}</span>
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-bath text-[#b08d72]"></i>{{ $property->bathrooms }}</span>
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-person-walking text-[#b08d72]"></i>{{ $property->proximity }}m</span>
                                </div>
                            </div>

                            {{-- Badges & Price --}}
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
                            <p class="text-xs text-[#6e5542]/90 leading-relaxed bg-[#fdfcfb] p-3 rounded-xl border border-[#f5f0ea] line-clamp-2">
                                {{ $property->description }}
                            </p>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="px-6 pb-6 pt-2 flex items-center gap-2">

                            @if($property->status === 'approved')
                            <form method="POST" action="{{ route('owner.properties.mark-rented', $property->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all cursor-pointer">
                                    <i class="fa-solid fa-key text-xs"></i> Mark as Rented
                                </button>
                            </form>
                            @endif

                            @if($property->status !== 'rented')
                            <button type="button" onclick='openEditModal({!! json_encode($property) !!})'
                                class="flex-1 flex items-center justify-center gap-2 bg-[#fdfcfb] border border-[#ebdccb] hover:border-[#8b6f56] text-[#6e5542] hover:text-[#8b6f56] py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer">
                                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                            </button>

                            <form method="POST" action="{{ route('owner.properties.destroy', $property->id) }}"
                                onsubmit="return confirm('Delete this property permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50/50 border border-red-100 hover:border-red-200 hover:bg-red-50 text-red-400 hover:text-red-600 p-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            @endif

                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white border border-[#ebdccb] rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm space-y-5 mt-6">
                <div class="bg-[#fcfaf7] w-16 h-16 flex items-center justify-center text-[#b08d72] text-3xl rounded-2xl mx-auto border border-[#ebdccb]">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#4a3728]">No properties listed yet</h3>
                    <p class="text-sm text-[#8c7460] leading-relaxed mt-1">Add your first apartment to start connecting with students.</p>
                </div>
                <button type="button" onclick="openAddModal()"
                    class="bg-[#8b6f56] hover:bg-[#765e49] text-white font-bold py-3 px-6 rounded-xl text-xs transition-all shadow-md border-0 cursor-pointer inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Your First Property
                </button>
            </div>
            @endif

        </main>
    </div>

    {{-- Add Property Modal --}}
    <div id="addPropertyModal" class="fixed inset-0 z-50 bg-[#211208]/40 backdrop-blur-sm flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white w-full max-w-xl rounded-[28px] shadow-2xl border border-[#ebdccb]/50 overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh] flex flex-col">

            <div class="px-6 py-5 border-b border-[#f5f0ea] flex items-center justify-between bg-[#fdfcfb]">
                <div>
                    <h2 class="text-lg font-black text-[#211208]">Add New Property</h2>
                    <p class="text-[11px] text-[#8c7460] font-medium">Fill in the details to list your property on StayUni.</p>
                </div>
                <button type="button" onclick="closeAddModal()"
                    class="w-8 h-8 rounded-full bg-[#f5f0ea] hover:bg-red-50 text-[#6e5542] hover:text-red-500 flex items-center justify-center transition-colors border-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('owner.properties.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Governorate</label>
                    <select name="governorate" required
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                        <option value="">Select governorate</option>
                        @foreach(['cairo'=>'Cairo','giza'=>'Giza','alexandria'=>'Alexandria','asyut'=>'Asyut','sohag'=>'Sohag','qena'=>'Qena','luxor'=>'Luxor','aswan'=>'Aswan','minya'=>'Minya','beni_suef'=>'Beni Suef','fayoum'=>'Fayoum','dakahlia'=>'Dakahlia','gharbia'=>'Gharbia','sharkia'=>'Sharkia','beheira'=>'Beheira','kafr_el_sheikh'=>'Kafr El Sheikh','monufia'=>'Monufia','damietta'=>'Damietta','porsaid'=>'PorSaid','ismailia'=>'Ismailia','suez'=>'Suez'] as $val => $label)
                        <option value="{{ $val }}" {{ old('governorate') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Location / Address</label>
                    <input type="text" name="location" required value="{{ old('location') }}" placeholder="Street, building, area..."
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Bedrooms</label>
                        <input type="number" name="bedrooms" required min="1" max="20" value="{{ old('bedrooms', 3) }}"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Bathrooms</label>
                        <input type="number" name="bathrooms" required min="1" max="20" value="{{ old('bathrooms', 1) }}"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Floor</label>
                        <input type="number" name="floor" min="0" max="200" value="{{ old('floor', 0) }}"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Proximity (m)</label>
                        <input type="number" name="proximity" required min="0" value="{{ old('proximity', 0) }}" placeholder="e.g. 300"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Monthly Rent (EGP)</label>
                        <input type="number" name="price" required min="0" value="{{ old('price') }}" placeholder="2500"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Target Renter</label>
                        <select name="gender_type"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-bold text-[#4a3728] outline-none transition-all">
                            <option value="female" {{ old('gender_type') == 'female' ? 'selected' : '' }}>♀ Females Only</option>
                            <option value="male" {{ old('gender_type') == 'male'   ? 'selected' : '' }}>♂ Males Only</option>
                        </select>
                    </div>
                </div>

                <div class="bg-[#fcfaf7] border border-[#ebdccb]/60 rounded-xl p-4 flex flex-row items-center justify-around gap-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-[#4a3728]">
                        <input type="checkbox" name="is_furnished" value="1" {{ old('is_furnished', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-[#ebdccb] text-[#8b6f56] focus:ring-[#8b6f56]">
                        Furnished
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-[#4a3728]">
                        <input type="checkbox" name="utilities_included" value="1" {{ old('utilities_included', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-[#ebdccb] text-[#8b6f56] focus:ring-[#8b6f56]">
                        Bills Included
                    </label>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Property Images (1–10 photos)</label>
                    <div class="border-2 border-dashed border-[#ebdccb] hover:border-[#8b6f56] rounded-xl p-4 bg-[#fdfcfb] text-center transition-all relative">
                        <input type="file" name="property_images[]" multiple accept="image/jpeg,image/png,image/jpg" required
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="space-y-1 pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-[#b08d72]"></i>
                            <p class="text-xs font-bold text-[#4a3728]">Click or drag pictures to upload</p>
                            <p class="text-[10px] text-[#8c7460]">PNG, JPG up to 4MB each · max 10 images</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Description / Notes</label>
                    <textarea name="description" rows="3" placeholder="Amenities, rules, contact times..."
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="pt-3 flex items-center gap-3 border-t border-[#f5f0ea]">
                    <button type="button" onclick="closeAddModal()"
                        class="flex-1 bg-stone-100 hover:bg-stone-200 text-[#6e5542] py-3 rounded-xl text-xs font-bold transition-all border-0 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-[#8b6f56] hover:bg-[#765e49] text-white py-3 rounded-xl text-xs font-bold transition-all shadow-md border-0 cursor-pointer">
                        Save Listing
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Edit Property Modal --}}
    <div id="editPropertyModal" class="fixed inset-0 z-50 bg-[#211208]/40 backdrop-blur-sm flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white w-full max-w-xl rounded-[28px] shadow-2xl border border-[#ebdccb]/50 overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh] flex flex-col">

            <div class="px-6 py-5 border-b border-[#f5f0ea] flex items-center justify-between bg-[#fdfcfb]">
                <div>
                    <h2 class="text-lg font-black text-[#211208]">Edit Property Details</h2>
                    <p class="text-[11px] text-[#8c7460] font-medium">Update the details of your property listing.</p>
                </div>
                <button type="button" onclick="closeEditModal()"
                    class="w-8 h-8 rounded-full bg-[#f5f0ea] hover:bg-red-50 text-[#6e5542] hover:text-red-500 flex items-center justify-center transition-colors border-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="editPropertyForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Governorate</label>
                    <select name="governorate" id="edit_governorate" required
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                        <option value="">Select governorate</option>
                        @foreach(['cairo'=>'Cairo','giza'=>'Giza','alexandria'=>'Alexandria','asyut'=>'Asyut','sohag'=>'Sohag','qena'=>'Qena','luxor'=>'Luxor','aswan'=>'Aswan','minya'=>'Minya','beni_suef'=>'Beni Suef','fayoum'=>'Fayoum','dakahlia'=>'Dakahlia','gharbia'=>'Gharbia','sharkia'=>'Sharkia','beheira'=>'Beheira','kafr_el_sheikh'=>'Kafr El Sheikh','monufia'=>'Monufia','damietta'=>'Damietta','porssaid'=>'PorsSaid','ismailia'=>'Ismailia','suez'=>'Suez'] as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Location / Address</label>
                    <input type="text" name="location" id="edit_location" required placeholder="Street, building, area..."
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Bedrooms</label>
                        <input type="number" name="bedrooms" id="edit_bedrooms" required min="1" max="20"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Bathrooms</label>
                        <input type="number" name="bathrooms" id="edit_bathrooms" required min="1" max="20"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Floor</label>
                        <input type="number" name="floor" id="edit_floor" min="0" max="200"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Proximity (m)</label>
                        <input type="number" name="proximity" id="edit_proximity" required min="0"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-2 py-2.5 text-xs font-medium text-center outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Monthly Rent (EGP)</label>
                        <input type="number" name="price" id="edit_price" required min="0" placeholder="2500"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-[#4a3728] block">Target Renter</label>
                        <select name="gender_type" id="edit_gender_type"
                            class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-bold text-[#4a3728] outline-none transition-all">
                            <option value="female">♀ Females Only</option>
                            <option value="male">♂ Males Only</option>
                        </select>
                    </div>
                </div>

                <div class="bg-[#fcfaf7] border border-[#ebdccb]/60 rounded-xl p-4 flex flex-row items-center justify-around gap-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-[#4a3728]">
                        <input type="checkbox" name="is_furnished" id="edit_is_furnished" value="1"
                            class="w-4 h-4 rounded border-[#ebdccb] text-[#8b6f56] focus:ring-[#8b6f56]">
                        Furnished
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-[#4a3728]">
                        <input type="checkbox" name="utilities_included" id="edit_utilities_included" value="1"
                            class="w-4 h-4 rounded border-[#ebdccb] text-[#8b6f56] focus:ring-[#8b6f56]">
                        Bills Included
                    </label>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Property Images (Leave empty to keep existing)</label>
                    <div class="border-2 border-dashed border-[#ebdccb] hover:border-[#8b6f56] rounded-xl p-4 bg-[#fdfcfb] text-center transition-all relative">
                        <input type="file" name="property_images[]" multiple accept="image/jpeg,image/png,image/jpg"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="space-y-1 pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-[#b08d72]"></i>
                            <p class="text-xs font-bold text-[#4a3728]">Upload new pictures to replace old ones</p>
                            <p class="text-[10px] text-[#8c7460]">PNG, JPG up to 4MB each · max 10 images</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-[#4a3728] block">Description / Notes</label>
                    <textarea name="description" id="edit_description" rows="3" placeholder="Amenities, rules, contact times..."
                        class="w-full bg-[#fdfcfb] border border-[#ebdccb] focus:border-[#8b6f56] focus:ring-1 focus:ring-[#8b6f56] rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all resize-none"></textarea>
                </div>

                <div class="pt-3 flex items-center gap-3 border-t border-[#f5f0ea]">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-stone-100 hover:bg-stone-200 text-[#6e5542] py-3 rounded-xl text-xs font-bold transition-all border-0 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-[#8b6f56] hover:bg-[#765e49] text-white py-3 rounded-xl text-xs font-bold transition-all shadow-md border-0 cursor-pointer">
                        Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function slideImg(propertyId, direction) {
            const container = document.getElementById(`slider-${propertyId}`);
            if (!container) return;
            const images = container.querySelectorAll('img[data-slide-index]');
            if (images.length <= 1) return;
            let currentIndex = [...images].findIndex(img => img.classList.contains('opacity-100'));
            if (currentIndex === -1) return;
            images[currentIndex].classList.replace('opacity-100', 'opacity-0');
            images[currentIndex].classList.replace('scale-100', 'scale-95');
            let newIndex = (currentIndex + direction + images.length) % images.length;
            images[newIndex].classList.replace('opacity-0', 'opacity-100');
            images[newIndex].classList.replace('scale-95', 'scale-100');
        }

        function openAddModal() {
            const modal = document.getElementById('addPropertyModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
            }, 20);
        }

        // تم ربط تعبئة حقل القرب هنا (edit_proximity)
        function openEditModal(property) {
            const modal = document.getElementById('editPropertyModal');
            const form = document.getElementById('editPropertyForm');

            form.action = `/owner/properties/${property.id}`;

            document.getElementById('edit_governorate').value = property.governorate || '';
            document.getElementById('edit_location').value = property.location || '';
            document.getElementById('edit_bedrooms').value = property.bedrooms || 3;
            document.getElementById('edit_bathrooms').value = property.bathrooms || 1;
            document.getElementById('edit_floor').value = property.floor || 0;
            document.getElementById('edit_proximity').value = property.proximity || 0;
            document.getElementById('edit_price').value = property.price || '';
            document.getElementById('edit_gender_type').value = property.gender_type || 'female';
            document.getElementById('edit_description').value = property.description || '';

            document.getElementById('edit_is_furnished').checked = !!property.is_furnished;
            document.getElementById('edit_utilities_included').checked = !!property.utilities_included;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
            }, 20);
        }

        function closeAddModal() {
            const modal = document.getElementById('addPropertyModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.bg-white').classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function closeEditModal() {
            const modal = document.getElementById('editPropertyModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.bg-white').classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => openAddModal());
        @endif
    </script>
</body>

</html>
