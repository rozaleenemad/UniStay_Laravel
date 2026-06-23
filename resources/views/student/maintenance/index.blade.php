<x-app-layout>
    <div class="p-6 bg-slate-950 min-h-screen text-slate-200">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <span>🧰</span> خدمات الصيانة المتوفرة للسكن
            </h1>
            <p class="text-xs text-slate-400 mt-1">ابحث عن فنيين لمساعدتك في إصلاح أعطال السكن الجامعي (فلترة فورية)</p>
        </div>

        <div class="flex flex-wrap gap-4 mb-8 bg-slate-900 p-4 rounded-xl border border-[#4a3325]/30">

            <select id="filter-type" onchange="filterMaintenance()" class="bg-slate-950 text-white rounded-lg p-2 border border-slate-800 text-sm focus:border-[#b08d72] focus:outline-none">
                <option value="all">كل التخصصات</option>
                @foreach($maintenanceTypes as $slug => $label)
                    <option value="{{ $slug }}">{{ $label }}</option>
                @endforeach
            </select>

            <select id="filter-governorate" onchange="filterMaintenance()" class="bg-slate-950 text-white rounded-lg p-2 border border-slate-800 text-sm focus:border-[#b08d72] focus:outline-none">
                <option value="all">كل المحافظات</option>
                @foreach($governorates as $slug => $label)
                    <option value="{{ $slug }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div id="maintenance-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($technicians as $tech)
                @php
                    $typeLabels = $maintenanceTypes;
                    $govLabels = $governorates;
                    $waPhone = preg_replace('/\D/', '', $tech->phone);
                    if (strlen($waPhone) === 11 && substr($waPhone, 0, 1) === '0') {
                        $waPhone = '2' . $waPhone;
                    }
                @endphp
                <div class="provider-card bg-slate-900 p-5 rounded-xl border border-slate-800 hover:border-[#b08d72]/40 transition-all flex flex-col justify-between shadow-lg"
                     data-governorate="{{ strtolower($tech->governorate ?? '') }}"
                     data-type="{{ strtolower($tech->maintenance_type ?? '') }}">

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-white font-bold text-lg">{{ $tech->name }}</h3>
                            <span class="bg-[#4a3325] text-[#e6cfbd] text-xs font-bold px-2.5 py-1 rounded-md border border-[#b08d72]/20">
                                {{ $typeLabels[$tech->maintenance_type] ?? ucfirst($tech->maintenance_type) }}
                            </span>
                        </div>

                        <div class="space-y-1.5 mb-4 text-sm text-slate-400">
                            <p><span class="text-[#b08d72] mr-1">📍</span> المحافظة: {{ $govLabels[$tech->governorate] ?? $tech->governorate }}</p>
                            @if($tech->location)
                                <p><span class="text-[#b08d72] mr-1">🏠</span> العنوان: {{ $tech->location }}</p>
                            @endif
                            <p><span class="text-[#b08d72] mr-1">📞</span> رقم التليفون: {{ $tech->phone }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="tel:{{ $tech->phone }}" class="block text-center bg-slate-950 hover:bg-black text-sm font-bold text-[#e6cfbd] py-2.5 rounded-lg border border-slate-800 hover:border-slate-700 transition-colors shadow-inner">
                            📞 اتصال
                        </a>
                        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('السلام عليكم، عايز أطلب خدمة ' . ($typeLabels[$tech->maintenance_type] ?? $tech->maintenance_type)) }}"
                           target="_blank" rel="noopener"
                           class="block text-center bg-green-700/20 hover:bg-green-700/30 text-sm font-bold text-green-400 py-2.5 rounded-lg border border-green-700/30 hover:border-green-600/50 transition-colors">
                            💬 واتساب
                        </a>
                    </div>
                    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('السلام عليكم، عندي ريكوست صيانة (' . ($typeLabels[$tech->maintenance_type] ?? $tech->maintenance_type) . ') في ' . ($tech->location ?? ($govLabels[$tech->governorate] ?? $tech->governorate)) . '. ممكن نتفق على التفاصيل؟') }}"
                       target="_blank" rel="noopener"
                       class="mt-2 block text-center bg-[#4a3325]/40 hover:bg-[#4a3325]/60 text-sm font-bold text-[#e6cfbd] py-2.5 rounded-lg border border-[#b08d72]/30 hover:border-[#b08d72]/50 transition-colors">
                        🛠️ اطلب صيانة الآن
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-slate-900/30 rounded-xl border border-dashed border-slate-800">
                    <div class="text-slate-600 text-5xl mb-3">📭</div>
                    <h2 class="text-white font-bold text-xl mb-1">لا يوجد مسجلين</h2>
                    <p class="text-slate-400 text-sm">لم يقم أي فني بالتسجيل في الموقع بعد.</p>
                </div>
            @endforelse
        </div>

        <div id="no-maintenance-results" class="hidden text-center py-16 bg-slate-900/30 rounded-xl border border-dashed border-slate-800">
            <div class="text-slate-600 text-5xl mb-3">🔍</div>
            <h2 class="text-white font-bold text-xl mb-1">Not Found</h2>
            <p class="text-slate-400 text-sm max-w-md mx-auto">عذراً، لا يوجد فني متوفر في الخيارات التي اخترتها حالياً.</p>
        </div>
    </div>

    <script>
        function filterMaintenance() {
            const typeFilter = document.getElementById('filter-type').value.toLowerCase();
            const govFilter = document.getElementById('filter-governorate').value.toLowerCase();
            const cards = document.querySelectorAll('#maintenance-container .provider-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardType = card.dataset.type || '';
                const cardGov = card.dataset.governorate || '';

                const typeMatch = (typeFilter === 'all') || (cardType === typeFilter);
                const govMatch = (govFilter === 'all') || (cardGov === govFilter);

                if (typeMatch && govMatch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('no-maintenance-results').classList.toggle('hidden', visibleCount !== 0);
        }
    </script>
</x-app-layout>
