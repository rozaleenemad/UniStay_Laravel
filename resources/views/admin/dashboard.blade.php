<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>StayUni | Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fbf9f6;
        }
    </style>
</head>

<body class="text-amber-950 min-h-screen antialiased flex">

    {{-- ===================== SIDEBAR ===================== --}}



    <aside  id="sidebar" class="w-64 bg-[#1a0f07] text-[#fdfcfb] min-h-screen p-6 flex flex-col justify-between shrink-0 shadow-2xl hidden md:flex">
        <div class="space-y-6">
            {{-- Logo --}}
            <div class="flex items-center gap-3 border-b border-white/10 pb-5">
                <div class="bg-[#b08d72] text-[#211208] w-9 h-9 flex items-center justify-center rounded-xl font-extrabold text-base shadow">
                    <i class="fa-solid fa-user-shield"></i>
                </div>

                <div>
                    <span class="text-lg font-black tracking-tight text-[#b08d72]">Stay<span class="text-[#b08d72]">Uni</span></span>

                    <p class="text-[10px] font-bold text-orange-400 tracking-wider uppercase">Admin Control</p>
                </div>

                <button id="menu-btn" class="md:hidden text-white p-2">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            {{-- Nav Links --}}
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 bg-[#b08d72]/15 text-[#ebdccb] px-3 py-2.5 rounded-xl text-sm font-bold border-l-4 border-[#b08d72]">
                    <i class="fa-solid fa-gauge w-4 text-[#b08d72]"></i> Overview
                </a>
                <a href="#owners-section"
                    class="flex items-center gap-3 text-[#b09a8a] hover:text-white hover:bg-white/[0.04] px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group">
                    <i class="fa-solid fa-users-gear w-4 group-hover:text-[#b08d72]"></i> Manage Owners
                    @if($pendingOwners > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingOwners }}</span>
                    @endif
                </a>
                <a href="#properties-section"
                    class="flex items-center gap-3 text-[#b09a8a] hover:text-white hover:bg-white/[0.04] px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group">
                    <i class="fa-solid fa-building-circle-check w-4 group-hover:text-[#b08d72]"></i> Properties Audit
                    @if($pendingProperties > 0)
                    <span class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingProperties }}</span>
                    @endif
                </a>
                <a href="#rented-section"
                    class="flex items-center gap-3 text-[#b09a8a] hover:text-white hover:bg-white/[0.04] px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group">
                    <i class="fa-solid fa-key w-4 group-hover:text-blue-400"></i> Rented Properties
                    @if(isset($rentedProperties) && $rentedProperties > 0)
                    <span class="ml-auto bg-blue-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $rentedProperties }}</span>
                    @endif
                </a>
            </nav>
        </div>

        {{-- Logout --}}
        <div class="border-t border-white/10 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 border border-white/10 hover:border-red-500/30 text-[#b09a8a] hover:text-red-400 hover:bg-red-500/5 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer bg-transparent">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===================== MAIN CONTENT AREA ===================== --}}
    <div class="flex-1 min-h-screen flex flex-col box-border overflow-x-hidden">

        {{-- Topbar Header --}}
        <header class="bg-white/70 backdrop-blur-md border-b border-[#ebdccb]/40 px-8 py-5 flex items-center justify-between sticky top-0 z-20">
            <h1 class="text-xl font-black text-[#211208] tracking-tight">System Administration</h1>
            <div class="flex items-center gap-3">
                <span class="text-xs text-emerald-800 font-bold bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> System Live
                </span>
                <span class="text-xs text-red-700 font-bold bg-red-50 border border-red-200 px-3 py-1 rounded-full">
                    Super Admin Mode
                </span>
            </div>
        </header>

        <main class="p-8 grow space-y-8 w-full max-w-7xl mx-auto box-border">

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-circle-xmark text-red-500"></i> {{ session('error') }}
            </div>
            @endif

            {{-- ===== KPI CARDS ===== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-white border border-[#ebdccb]/50 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-blue-50 text-blue-600 w-8 h-8 flex items-center justify-center rounded-xl text-xs"><i class="fa-solid fa-graduation-cap"></i></div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Students</p>
                    </div>
                    <h3 class="text-3xl font-black text-[#211208]">{{ $totalStudents }}</h3>
                    <p class="text-[11px] text-[#8c7460] mt-1 font-medium">Registered on platform</p>
                </div>

                <div class="bg-white border border-[#ebdccb]/50 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-amber-50 text-amber-600 w-8 h-8 flex items-center justify-center rounded-xl text-xs"><i class="fa-solid fa-user-tie"></i></div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Owners</p>
                    </div>
                    <h3 class="text-3xl font-black text-[#211208]">{{ $totalOwners }}</h3>
                    <p class="text-[11px] text-amber-600 mt-1 font-bold">{{ $pendingOwners }} pending approval</p>
                </div>

                <div class="bg-white border border-[#ebdccb]/50 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-emerald-50 text-emerald-600 w-8 h-8 flex items-center justify-center rounded-xl text-xs"><i class="fa-solid fa-building"></i></div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Properties</p>
                    </div>
                    <h3 class="text-3xl font-black text-[#211208]">{{ $totalProperties }}</h3>
                    <p class="text-[11px] text-emerald-600 mt-1 font-bold">{{ $approvedProperties }} approved live</p>
                </div>

                <div class="bg-white border border-red-100 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-red-50 text-red-500 w-8 h-8 flex items-center justify-center rounded-xl text-xs"><i class="fa-solid fa-hourglass-half animate-pulse"></i></div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Needs Action</p>
                    </div>
                    <h3 class="text-3xl font-black text-red-500">{{ $pendingTotal }}</h3>
                    <p class="text-[11px] text-[#8c7460] mt-1 font-medium">Awaiting verification</p>
                </div>

                {{-- Rented properties card --}}
                <div class="bg-white border border-blue-100 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-blue-50 text-blue-600 w-8 h-8 flex items-center justify-center rounded-xl text-xs"><i class="fa-solid fa-key"></i></div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Rented</p>
                    </div>
                    <h3 class="text-3xl font-black text-blue-600">{{ $rentedProperties ?? 0 }}</h3>
                    <p class="text-[11px] text-blue-500 mt-1 font-bold">Hidden from listings</p>
                </div>


                <div class="bg-white border border-[#ebdccb]/50 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-orange-50 text-orange-600 w-8 h-8 flex items-center justify-center rounded-xl text-xs">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                        <p class="text-[11px] font-bold text-[#8c7460] uppercase">Maintenance</p>
                    </div>
                    <h3 class="text-3xl font-black text-[#211208]">{{ $totalMaintenance ?? 0 }}</h3>
                    <p class="text-[11px] text-orange-600 mt-1 font-medium">Service providers live</p>
                </div>
            </div>

            {{-- ===== CHARTS ROW (1) ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Monthly registrations chart --}}
                <div class="lg:col-span-2 bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chart-bar text-[#8b6f56]"></i> Monthly Registration Growth
                        </h3>
                        <div class="relative h-64 w-full">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                    <div class="flex gap-5 mt-4 border-t border-[#f5f0ea] pt-3">
                        <span class="flex items-center gap-1.5 text-xs font-bold text-[#4a3728]">
                            <span class="w-3 h-3 rounded bg-[#b08d72] inline-block"></span> Students
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-[#4a3728]">
                            <span class="w-3 h-3 rounded bg-[#1a0f07] inline-block"></span> Owners
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-[#4a3728]">
                            <span class="w-3 h-3 rounded bg-[#f97316] inline-block"></span> Maintenance
                        </span>
                    </div>
                </div>

                {{-- User distribution breakdown doughnut chart --}}
                <div class="bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-[#8b6f56]"></i> User Distribution Share
                        </h3>
                        <div class="w-[240px] h-[240px] mx-auto relative flex items-center justify-center">
                            <canvas id="userDistChart"></canvas>
                        </div>
                    </div>
                    <div class="space-y-2 mt-4 border-t border-[#f5f0ea] pt-3">
                        <div class="flex items-center justify-between text-xs text-[#8c7460] font-medium">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#b08d72] inline-block"></span> Total Students</span>
                            <span class="font-black text-[#211208]">{{ $totalStudents }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[#8c7460] font-medium">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#211208] inline-block"></span> Total Owners</span>
                            <span class="font-black text-[#211208]">{{ $totalOwners }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-[#8c7460] font-medium">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#f97316] inline-block"></span> Total Maintenance</span>
                            <span class="font-black text-[#211208]">{{ $totalMaintenance ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== CHARTS ROW (2) - FIXED GENDER DISPLAY ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Property status metrics --}}
                <div class="lg:col-span-2 bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm space-y-5">
                    <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-[#8b6f56]"></i> Property Status Metrics
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                        @php $propTotal = max($totalProperties, 1); @endphp

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-[#8c7460]">
                                <span>Approved</span><span class="text-emerald-700 font-extrabold">{{ $approvedProperties }} / {{ $totalProperties }}</span>
                            </div>
                            <div class="h-2.5 bg-emerald-50 rounded-full overflow-hidden border border-emerald-100/50">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ round($approvedProperties/$propTotal*100) }}%"></div>
                            </div>
                            <p class="text-[11px] font-bold text-emerald-700">{{ round($approvedProperties/$propTotal*100) }}% efficiency</p>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-[#8c7460]">
                                <span>Pending Queue</span><span class="text-amber-700 font-extrabold">{{ $pendingProperties }} / {{ $totalProperties }}</span>
                            </div>
                            <div class="h-2.5 bg-amber-50 rounded-full overflow-hidden border border-amber-100/50">
                                <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: {{ round($pendingProperties/$propTotal*100) }}%"></div>
                            </div>
                            <p class="text-[11px] font-bold text-amber-700">{{ round($pendingProperties/$propTotal*100) }}% backlogged</p>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-[#8c7460]">
                                <span>Rejected</span><span class="text-red-700 font-extrabold">{{ $rejectedProperties }} / {{ $totalProperties }}</span>
                            </div>
                            <div class="h-2.5 bg-red-50 rounded-full overflow-hidden border border-red-100/50">
                                <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ round($rejectedProperties/$propTotal*100) }}%"></div>
                            </div>
                            <p class="text-[11px] font-bold text-red-700">{{ round($rejectedProperties/$propTotal*100) }}% denied</p>
                        </div>
                    </div>
                </div>

                {{-- Target Demographic Donut Chart (Boys vs Girls) --}}
                <div class="bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-venus-mars text-[#8b6f56]"></i> Target Demographic (Housing Share)
                        </h3>
                        <div class="w-[240px] h-[240px] mx-auto relative flex items-center justify-center">
                            <canvas id="donutChart"></canvas>
                        </div>
                    </div>
                    <div class="space-y-2 mt-4 border-t border-[#f5f0ea] pt-3">
                        <div class="flex items-center justify-between text-xs text-[#8c7460] font-medium">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#211208] inline-block"></span> Boys Housing</span>
                            <span class="font-black text-[#211208]">{{ $malePropertiesCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[#8c7460] font-medium">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#b08d72] inline-block"></span> Girls Housing</span>
                            <span class="font-black text-[#211208]">{{ $femalePropertiesCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== PROPERTIES AUDIT LIST ===== --}}
            @if($pendingPropertiesList->isNotEmpty())
            <div id="properties-section" class="bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-amber-500"></i> Properties Verification & Audit Queue ({{ $pendingPropertiesList->count() }})
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#f5f0ea] text-[#8c7460] text-[11px] uppercase font-bold bg-[#fcfaf7]">
                                <th class="px-4 py-3">Property</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Monthly Rent</th>
                                <th class="px-4 py-3">Target</th>
                                <th class="px-4 py-3">Current Status</th>
                                <th class="px-4 py-3 text-right">Verification Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-[#f5f0ea]">
                            @foreach($pendingPropertiesList as $property)
                            <tr class="hover:bg-[#fcfaf7]/60 transition-colors">
                                <td class="px-4 py-4 font-bold text-sm whitespace-nowrap">
                                    <a href="/owner/properties/{{ $property->id }}/edit" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-500"></i>
                                        {{ Str::limit($property->title ?? $property->location ?? 'Property #'.$property->id, 30) }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-[#4a3728] font-medium">{{ $property->owner->name ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-[#8c7460] font-medium"><i class="fa-solid fa-location-dot text-[#b08d72] mr-0.5"></i> {{ $property->location }}</td>
                                <td class="px-4 py-4 font-black text-[#8b6f56] text-sm">{{ number_format($property->price) }} EGP</td>
                                <td class="px-4 py-4">
                                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider
                                    {{ strtolower($property->gender_type) === 'male' ? 'bg-[#211208] text-white' : 'bg-[#c2410c] text-white' }}">
                                        {{ strtolower($property->gender_type) === 'male' ? 'Male' : 'Female' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @if($property->status == 'pending')
                                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                        <i class="fa-solid fa-hourglass-half mr-0.5 animate-pulse"></i> Pending
                                    </span>
                                    @elseif($property->status == 'approved')
                                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                        Approved
                                    </span>
                                    @elseif($property->status == 'rejected')
                                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">
                                        Rejected
                                    </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 flex-wrap">

                                        @if($property->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.properties.approve', $property->id) }}"
                                            onsubmit="return confirm('Approve this property? It will become live for students.')">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-3 py-2 rounded-xl transition-all">
                                                Approve
                                            </button>
                                        </form>
                                        @endif

                                        @if($property->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.properties.reject', $property->id) }}"
                                            onsubmit="return confirm('Are you sure you want to reject this property?')">
                                            @csrf
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-[11px] font-bold px-3 py-2 rounded-xl cursor-pointer transition-all">
                                                Reject
                                            </button>
                                        </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    <div class="mt-4">
                        {{ $pendingPropertiesList->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>

            @endif

            {{-- ===== RENTED PROPERTIES — MANAGEMENT ===== --}}
            @if(isset($rentedPropertiesList) && $rentedPropertiesList->isNotEmpty())
            <div id="rented-section" class="bg-white border border-blue-200/60 p-6 rounded-[24px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-blue-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-key text-blue-500"></i>
                    Rented Properties ({{ $rentedPropertiesList->count() }}) — Owner Management
                </h3>
                <p class="text-[11px] text-[#8c7460]">These properties are marked rented by their owners and hidden from public listings. Owners can reactivate them anytime, or admins can assist.</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                    </table>
                </div>
            </div>
            @endif


            {{-- ===== OWNER IDENTITY VERIFICATION TABLE ===== --}}
            <div id="owners-section" class="bg-white border border-[#ebdccb]/60 p-6 rounded-[24px] shadow-sm space-y-4">
                <h3 class="text-xs font-black text-[#6e5542] uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-[#8b6f56]"></i> Landlords Verification Requests
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#f5f0ea] text-[#8c7460] text-[11px] uppercase font-bold bg-[#fcfaf7]">
                                <th class="px-4 py-3">Owner Contact</th>
                                <th class="px-4 py-3">National ID</th>
                                <th class="px-4 py-3">Active Listings</th>
                                <th class="px-4 py-3">ID Document</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-[#f5f0ea]">
                            @forelse($owners as $owner)
                            <tr class="hover:bg-[#fcfaf7]/60 transition-colors">
                                <td class="px-4 py-4 space-y-0.5">
                                    <p class="font-bold text-[#211208] text-sm">{{ $owner->name }}</p>
                                    <p class="text-[#8c7460] font-medium">{{ $owner->email }} | {{ $owner->phone ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-4 font-mono text-stone-600 tracking-wider font-semibold">{{ $owner->national_id ?? 'N/A' }}</td>
                                <td class="px-4 py-4 font-bold text-[#4a3728] text-sm">
                                    <span>{{ $owner->properties_count }}</span>
                                    <span class="text-[10px] text-[#8c7460] font-medium"> rooms</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if($owner->id_card_image)
                                    <a href="{{ asset('storage/' . $owner->id_card_image) }}" target="_blank"
                                        class="inline-flex items-center gap-1 font-bold text-[#8b6f56] hover:underline">
                                        <i class="fa-solid fa-image text-sm"></i> View File
                                    </a>
                                    @else
                                    <span class="text-stone-400 font-medium italic">No document</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($owner->status === 'approved')
                                    <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full uppercase tracking-wider">Approved</span>
                                    @elseif($owner->status === 'rejected')
                                    <span class="text-[10px] font-bold bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-full uppercase tracking-wider">Rejected</span>
                                    @else
                                    <span class="text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 px-2.5 py-1 rounded-full uppercase tracking-wider animate-pulse">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($owner->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.approve-owner', $owner) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl border-0 cursor-pointer transition-all shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                        @endif
                                        @if($owner->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.reject-owner', $owner) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-[11px] font-bold px-3 py-1.5 rounded-xl cursor-pointer transition-all">
                                                Reject
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center font-bold text-[#8c7460]">No landlords found on the system database.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $owners->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>


        </main>
    </div>

   {{-- ===================== CHARTS SCRIPT ===================== --}}
    <script>

        document.addEventListener('DOMContentLoaded', function() {

            // 1️. Monthly Registration Growth Chart
            const ctxMonthly = document.getElementById('monthlyChart');
            if (ctxMonthly) {
                new Chart(ctxMonthly, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($months) !!},
                        datasets: [{
                                label: 'Students',
                                data: {!! json_encode($monthlyStudents) !!},
                                backgroundColor: '#b08d72',
                                borderRadius: 8,
                            },
                            {
                                label: 'Owners',
                                data: {!! json_encode($monthlyOwners) !!},
                                backgroundColor: '#1a0f07',
                                borderRadius: 8,
                            },
                            {
                                label: 'Maintenance',
                                data: {!! json_encode($monthlyMaintenance ?? []) !!},
                                backgroundColor: '#f97316',
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        weight: 'bold',
                                        size: 11
                                    },
                                    color: '#4a3728'
                                }
                            },
                            y: {
                                stacked: true,
                                grid: {
                                    color: 'rgba(33, 18, 7, 0.04)'
                                },
                                ticks: {
                                    font: {
                                        weight: 'bold',
                                        size: 11
                                    },
                                    color: '#4a3728',
                                    stepSize: 1
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // 2️. Total Users Base Distribution Share Chart
            const ctxUserDist = document.getElementById('userDistChart');
            if (ctxUserDist) {
                new Chart(ctxUserDist, {
                    type: 'doughnut',
                    data: {
                        labels: ['Students', 'Owners', 'Maintenance'],
                        datasets: [{
                            data: [
                                {{ $totalStudents ?? 0 }},
                                {{ $totalOwners ?? 0 }},
                                {{ $totalMaintenance ?? 0 }}
                            ],
                            backgroundColor: ['#b08d72', '#211208', '#f97316'],
                            borderWidth: 2,
                            borderColor: '#fbf9f6',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // 3️. Target Demographic Share (Boys vs Girls)
            const ctxDonut = document.getElementById('donutChart');
            if (ctxDonut) {
                const maleCount = {{ $malePropertiesCount ?? 0 }};
                const femaleCount = {{ $femalePropertiesCount ?? 0 }};

                new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Boys Housing', 'Girls Housing'],
                        datasets: [{
                            data: [maleCount, femaleCount],
                            backgroundColor: ['#211208', '#b08d72'],
                            borderWidth: 2,
                            borderColor: '#fbf9f6',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

        });


    </script>
</body>

</html>
