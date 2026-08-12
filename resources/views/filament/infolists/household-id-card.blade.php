@php
    $household = $record;
    $rt = $household->rt;
    $rw = $rt?->rw;
    $head = $household->head;
    $residents = $household->residents()->orderByRaw("relationship_to_head = 'Kepala Keluarga' desc")->orderBy('full_name')->get();

    $headInitials = collect(explode(' ', trim($head->full_name ?? '')))
        ->filter()
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');

    $memberCount = $residents->count();
    $countColor = match (true) {
        $memberCount === 0 => ['bg' => 'bg-gray-100 dark:bg-gray-500/15', 'text' => 'text-gray-600 dark:text-gray-400', 'dot' => 'bg-gray-400'],
        $memberCount < 3 => ['bg' => 'bg-amber-50 dark:bg-amber-500/15', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        default => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/15', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
    };

    $residentStatusColor = fn ($status) => match ($status) {
        'Aktif' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/15', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Pindah' => ['bg' => 'bg-amber-50 dark:bg-amber-500/15', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Meninggal' => ['bg' => 'bg-rose-50 dark:bg-rose-500/15', 'text' => 'text-rose-700 dark:text-rose-400', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
        default => ['bg' => 'bg-gray-100 dark:bg-gray-500/15', 'text' => 'text-gray-600 dark:text-gray-400', 'dot' => 'bg-gray-400'],
    };

    $relationBadge = fn ($relation) => match ($relation) {
        'Kepala Keluarga' => 'bg-emerald-600 text-white dark:bg-emerald-500',
        'Suami', 'Istri' => 'bg-sky-600 text-white dark:bg-sky-500',
        'Anak' => 'bg-violet-600 text-white dark:bg-violet-500',
        default => 'bg-gray-500 text-white dark:bg-gray-500',
    };

    $accent = 'from-emerald-600 via-teal-600 to-cyan-700';
@endphp

<div class="w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-gray-950/10 dark:ring-white/10 bg-white dark:bg-gray-950 print:shadow-none print:ring-1 print:ring-gray-300 print:bg-white">

    {{-- ============ STRIP AKSEN ATAS ============ --}}
    <div class="h-2 print:h-1.5 w-full bg-gradient-to-r {{ $accent }}"></div>

    <div class="relative p-6 sm:p-8">

        {{-- watermark ikon keluarga, dekoratif --}}
        <div class="pointer-events-none absolute -right-6 -bottom-6 opacity-[0.04] dark:opacity-[0.05] print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-48 h-48 text-gray-950 dark:text-white">
                <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
            </svg>
        </div>

        {{-- ============ HEADER ============ --}}
        <div class="relative flex items-start justify-between gap-4 mb-6">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.2em] text-gray-500 dark:text-gray-400 uppercase">
                    Sistem Informasi Kependudukan
                </p>
                <h2 class="text-lg font-bold text-gray-950 dark:text-white tracking-wide">
                    Kartu Keluarga
                </h2>
            </div>

            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $countColor['bg'] }} {{ $countColor['text'] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $countColor['dot'] }}"></span>
                {{ $memberCount }} Anggota
            </span>
        </div>

        {{-- ============ BODY: AVATAR KEPALA KELUARGA + DATA UTAMA ============ --}}
        <div class="relative flex flex-col sm:flex-row gap-6">

            {{-- Avatar inisial kepala keluarga --}}
            <div class="shrink-0">
                <div class="w-24 h-24 rounded-xl bg-gradient-to-br {{ $accent }} flex items-center justify-center shadow-lg ring-4 ring-gray-950/5 dark:ring-white/5">
                    <span class="text-3xl font-bold text-white">{{ $headInitials ?: '?' }}</span>
                </div>
                <div class="mt-2 flex items-center justify-center gap-1 text-[11px] font-medium {{ $head ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                    @if($head)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                        Kepala Keluarga Terdata
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        Kepala Belum Ditentukan
                    @endif
                </div>
            </div>

            {{-- Data utama --}}
            <div class="flex-1 min-w-0">
                <p class="text-2xl font-bold text-gray-950 dark:text-white leading-tight">
                    {{ $head->full_name ?? 'Kepala Keluarga Belum Ditentukan' }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Kepala Keluarga
                    @if($rt)
                        &middot; RT {{ $rt->number }} / RW {{ $rw?->number }}
                    @endif
                </p>

                <div class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3">

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nomor Kartu Keluarga</p>
                        <p class="text-lg font-mono font-bold text-gray-950 dark:text-white tracking-wider">
                            {{ $household->no_kk ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">RT / RW</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $rt ? "RT {$rt->number} / RW {$rw?->number}" : 'Belum ditentukan' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">ID Pelanggan PLN</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $household->pln_customer_number ?? 'Belum diisi' }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Alamat</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $household->address ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============ ANGGOTA KELUARGA ============ --}}
        <div class="relative mt-6 pt-5 border-t border-gray-950/10 dark:border-white/10">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                Anggota Keluarga ({{ $memberCount }})
            </p>

            @if($memberCount > 0)
                <div class="flex flex-col divide-y divide-gray-950/5 dark:divide-white/5">
                    @foreach($residents as $resident)
                        @php $rStatus = $residentStatusColor($resident->status); @endphp
                        <div class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0">

                            <div class="shrink-0 w-9 h-9 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                                    {{ collect(explode(' ', trim($resident->full_name ?? '')))->filter()->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') ?: '?' }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-950 dark:text-white truncate">
                                    {{ $resident->full_name ?? '-' }}
                                </p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono">
                                    {{ $resident->nik ?? '-' }}
                                </p>
                            </div>

                            <span class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $relationBadge($resident->relationship_to_head) }}">
                                {{ $resident->relationship_to_head ?? '-' }}
                            </span>

                            <span class="shrink-0 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $rStatus['bg'] }} {{ $rStatus['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $rStatus['dot'] }}"></span>
                                {{ $resident->status ?? '-' }}
                            </span>

                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                    Belum ada anggota terdata pada kartu keluarga ini.
                </p>
            @endif
        </div>

        {{-- ============ FOOTER ============ --}}
        <div class="relative mt-6 pt-4 border-t border-gray-950/10 dark:border-white/10 flex items-end justify-between gap-4">

            <div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500">Diterbitkan oleh</p>
                <p class="text-sm font-semibold text-gray-950 dark:text-white">Dukuh Gondang</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                    Dicetak {{ now()->translatedFormat('d M Y, H:i') }}
                </p>
            </div>

            {{-- barcode dekoratif dari No. KK, bukan barcode scan sungguhan --}}
            <div class="flex items-end gap-[2px] h-10 opacity-70 print:opacity-100">
                @foreach(str_split($household->no_kk ?? '0000000000000000') as $digit)
                    <span
                        class="w-[3px] bg-gray-400 dark:bg-gray-300"
                        style="height: {{ 8 + ((int) $digit * 3) }}px"
                    ></span>
                @endforeach
            </div>

        </div>

    </div>
</div>
