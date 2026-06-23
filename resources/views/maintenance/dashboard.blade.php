<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f7f4f0; }
    </style>
</head>
<body class="text-[#211208] min-h-screen p-4 md:p-8">

    {{-- MAIN CONTAINER --}}
    <div class="w-full mx-auto space-y-6">

        {{-- HEADER SECTION --}}
        <div class="bg-white border border-[#eaddcf]/70 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#211208] text-[#ebdccb] flex items-center justify-center text-xl shadow-md">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-[#211208]">FixIt Provider Dashboard</h1>
                    <p class="text-xs text-[#8c7460] font-bold uppercase tracking-wider bg-[#fcfaf7] px-2 py-0.5 rounded inline-block">
                        {{ Auth::user()->maintenance_type ?? 'Maintenance' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all font-bold text-xs">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>

        {{-- USER INFO --}}
        <div class="py-2">
            <h2 class="text-2xl font-black text-[#211208]">Welcome back, {{ Auth::user()->name }}!</h2>
            <p class="text-sm text-[#8c7460] font-medium mt-1">Manage your incoming maintenance requests efficiently.</p>
        </div>

        {{-- REQUESTS GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($requests as $requestItem)
                <div class="bg-white border border-[#eaddcf]/70 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="bg-[#ebdccb] text-[#211208] text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $requestItem->title ?? 'Task' }}
                            </span>
                        </div>

                        <div>
                            <h4 class="font-bold text-base text-[#211208]">{{ $requestItem->booking->user->name ?? 'Student Name' }}</h4>
                            <p class="text-xs text-[#8c7460] mt-1 flex items-center gap-2">
                                <i class="fa-solid fa-phone text-[#b08d72]"></i> {{ $requestItem->booking->user->phone ?? 'N/A' }}
                            </p>
                        </div>

                        <p class="text-xs text-[#6e5542] bg-[#fcfaf7] p-4 rounded-2xl border border-[#f5f0ea] leading-relaxed">
                            "{{ Str::limit($requestItem->description, 150) }}"
                        </p>
                    </div>

                    <div class="flex gap-3 mt-6 pt-6 border-t border-[#f5f0ea]">
                        @if(isset($requestItem->booking->user->phone))
                            <a href="https://wa.me/{{ $requestItem->booking->user->phone }}" target="_blank"
                               class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl text-xs font-bold text-center transition-all">
                                WhatsApp
                            </a>
                        @endif
                        <form method="POST" action="#" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-[#211208] hover:bg-[#422613] text-white py-3 rounded-xl text-xs font-bold transition-all">
                                Accept
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-[#eaddcf]">
                    <i class="fa-solid fa-inbox text-5xl text-[#eaddcf] mb-4"></i>
                    <h3 class="text-lg font-black text-[#211208]">Waiting for students' need for the service</h3>
                    <p class="text-sm text-[#8c7460] mt-2">Check back later for new updates.</p>
                </div>
            @endforelse
        </div>

    </div>

</body>
</html>
