@php
    $resident = $record;
    $household = $resident->household;
    $rt = $household?->rt;
    $rw = $rt?->rw;

    $initials = collect(explode(' ', trim($resident->full_name ?? '')))
        ->filter()
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');

    $statusColor = match ($resident->status) {
        'Aktif' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-500/15',
            'text' => 'text-emerald-700 dark:text-emerald-400',
            'dot' => 'bg-emerald-500 dark:bg-emerald-400',
        ],
        'Pindah' => [
            'bg' => 'bg-amber-50 dark:bg-amber-500/15',
            'text' => 'text-amber-700 dark:text-amber-400',
            'dot' => 'bg-amber-500 dark:bg-amber-400',
        ],
        'Meninggal' => [
            'bg' => 'bg-rose-50 dark:bg-rose-500/15',
            'text' => 'text-rose-700 dark:text-rose-400',
            'dot' => 'bg-rose-500 dark:bg-rose-400',
        ],
        default => [
            'bg' => 'bg-gray-100 dark:bg-gray-500/15',
            'text' => 'text-gray-600 dark:text-gray-400',
            'dot' => 'bg-gray-400',
        ],
    };

    $accent = $resident->gender === 'Perempuan'
        ? 'from-pink-500 via-fuchsia-500 to-purple-600'
        : 'from-sky-500 via-blue-500 to-indigo-600';

    $akteTerdaftar = filled($resident->birth_cert_number);
@endphp

<div
    x-data="{ tab: 'padukuhan' }"
    class="w-full print:!block"
>
    {{-- ============ TAB NAVIGATION (disembunyikan saat cetak) ============ --}}
    <div class="flex flex-wrap items-center gap-2 mb-4 print:hidden">

        <button
            type="button"
            @click="tab = 'padukuhan'"
            :class="tab === 'padukuhan'
                ? 'bg-gray-950 text-white dark:bg-white dark:text-gray-950 shadow-sm'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10'"
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
            </svg>
            Kartu Padukuhan
        </button>

        <button
            type="button"
            @click="tab = 'akte'"
            :class="tab === 'akte'
                ? 'bg-gray-950 text-white dark:bg-white dark:text-gray-950 shadow-sm'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10'"
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5z" clip-rule="evenodd" />
                <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
            </svg>
            Akta Kelahiran
        </button>

        <button
            type="button"
            @click="tab = 'keluarga'"
            :class="tab === 'keluarga'
                ? 'bg-gray-950 text-white dark:bg-white dark:text-gray-950 shadow-sm'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10'"
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
            </svg>
            Kartu Keluarga
        </button>

        <span class="ml-auto text-[11px] text-gray-400 dark:text-gray-500">
            {{ $resident->full_name }}
        </span>
    </div>

    {{-- =====================================================================
         KARTU 1 — ID CARD PADUKUHAN (utama)
    ===================================================================== --}}
    <div x-show="tab === 'padukuhan'" x-cloak class="w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-gray-950/10 dark:ring-white/10 bg-white dark:bg-gray-950 print:shadow-none print:ring-1 print:ring-gray-300 print:bg-white">

        <div class="h-2 print:h-1.5 w-full bg-gradient-to-r {{ $accent }}"></div>

        <div class="relative p-6 sm:p-8">

            <div class="pointer-events-none absolute -right-6 -bottom-6 opacity-[0.04] dark:opacity-[0.05] print:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-48 h-48 text-gray-950 dark:text-white">
                    <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                    <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                </svg>
            </div>

            <div class="relative flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.2em] text-gray-500 dark:text-gray-400 uppercase">
                        Sistem Informasi Kependudukan
                    </p>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white tracking-wide">
                        Kartu Identitas Warga
                    </h2>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }}"></span>
                    {{ $resident->status ?? '-' }}
                </span>
            </div>

            <div class="relative flex flex-col sm:flex-row gap-6">

                <div class="shrink-0">
                    <div class="w-24 h-24 rounded-xl bg-gradient-to-br {{ $accent }} flex items-center justify-center shadow-lg ring-4 ring-gray-950/5 dark:ring-white/5">
                        <span class="text-3xl font-bold text-white">{{ $initials ?: '?' }}</span>
                    </div>
                    @if($resident->has_ktp)
                        <div class="mt-2 flex items-center justify-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            KTP Terverifikasi
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-2xl font-bold text-gray-950 dark:text-white leading-tight">
                        {{ $resident->full_name ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $resident->relationship_to_head ?? '-' }}
                        @if($household?->no_kk)
                            &middot; KK {{ $household->no_kk }}
                        @endif
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3">

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">NIK</p>
                            <p class="text-sm font-mono font-semibold text-gray-950 dark:text-white tracking-wider">
                                {{ $resident->nik ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Jenis Kelamin</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->gender ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Tempat, Tanggal Lahir</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->birth_place ?? '-' }}, {{ optional($resident->birth_date)->translatedFormat('d M Y') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Golongan Darah</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->blood_type ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Agama</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->religion ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Pendidikan Terakhir</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->education ?? 'Tidak/belum bekerja' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Pekerjaan</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $resident->occupation ?? 'Tidak/belum bekerja' }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Alamat</p>
                            <p class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $household?->address ?? '-' }}
                                @if($rt)
                                    &middot; RT {{ $rt->number }} / RW {{ $rw?->number }}
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="relative mt-6 pt-4 border-t border-gray-950/10 dark:border-white/10 flex items-end justify-between gap-4">

                <div>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Diterbitkan oleh</p>
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">Dukuh Gondang</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                        Dicetak {{ now()->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>

                <div class="flex items-end gap-[2px] h-10 opacity-70 print:opacity-100">
                    @foreach(str_split($resident->nik ?? '0000000000000000') as $digit)
                        <span
                            class="w-[3px] bg-gray-400 dark:bg-gray-300"
                            style="height: {{ 8 + ((int) $digit * 3) }}px"
                        ></span>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    {{-- =====================================================================
         KARTU 2 — ID CARD AKTA KELAHIRAN
    ===================================================================== --}}
    <div x-show="tab === 'akte'" x-cloak class="w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-amber-900/10 dark:ring-amber-300/10 bg-amber-50/60 dark:bg-gray-950 print:shadow-none print:ring-1 print:ring-gray-300 print:bg-white">

        <div class="h-2 print:h-1.5 w-full bg-gradient-to-r from-amber-600 via-yellow-600 to-amber-700"></div>

        <div class="relative p-6 sm:p-8">

            {{-- bingkai dekoratif ala dokumen resmi --}}
            <div class="pointer-events-none absolute inset-3 rounded-xl border border-dashed border-amber-900/15 dark:border-amber-300/10 print:border-gray-300"></div>

            <div class="pointer-events-none absolute -right-8 -bottom-8 opacity-[0.05] print:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-48 h-48 text-amber-900 dark:text-amber-200">
                    <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5z" clip-rule="evenodd" />
                    <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                </svg>
            </div>

            <div class="relative flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.2em] text-amber-700 dark:text-amber-400 uppercase">
                        Pencatatan Sipil &middot; Kutipan Akta Kelahiran
                    </p>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white tracking-wide">
                        Kartu Akta Kelahiran
                    </h2>
                </div>

                {{-- "materai" / cap status terdaftar --}}
                <div class="shrink-0 flex flex-col items-center">
                    <div class="w-14 h-14 rounded-full border-2 border-dashed flex items-center justify-center
                        {{ $akteTerdaftar ? 'border-amber-600 text-amber-700 dark:text-amber-400' : 'border-gray-300 text-gray-400' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            @if($akteTerdaftar)
                                <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12 6a.75.75 0 01.75.75v5.25a.75.75 0 01-1.5 0V6.75A.75.75 0 0112 6zm0 10.5a.9.9 0 100-1.8.9.9 0 000 1.8z" clip-rule="evenodd" />
                            @endif
                        </svg>
                    </div>
                    <span class="mt-1 text-[10px] font-semibold {{ $akteTerdaftar ? 'text-amber-700 dark:text-amber-400' : 'text-gray-400' }}">
                        {{ $akteTerdaftar ? 'Terdaftar' : 'Belum Terdaftar' }}
                    </span>
                </div>
            </div>

            <div class="relative">
                <p class="text-[11px] uppercase tracking-wide text-amber-700/70 dark:text-amber-400/70">Nomor Akta Kelahiran</p>
                <p class="text-xl font-mono font-bold text-gray-950 dark:text-white tracking-wider mb-6">
                    {{ $resident->birth_cert_number ?? '-' }}
                </p>

                <div class="grid grid-cols-2 gap-x-6 gap-y-4">

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Lengkap Anak</p>
                        <p class="text-base font-bold text-gray-950 dark:text-white">
                            {{ $resident->full_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">NIK</p>
                        <p class="text-sm font-mono font-semibold text-gray-950 dark:text-white">
                            {{ $resident->nik ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Jenis Kelamin</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->gender ?? '-' }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Tempat, Tanggal Lahir</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->birth_place ?? '-' }}, {{ optional($resident->birth_date)->translatedFormat('d F Y') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Ayah</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->father_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Ibu</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->mother_name ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>

            <div class="relative mt-6 pt-4 border-t border-dashed border-amber-900/15 dark:border-amber-300/10 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Diterbitkan oleh</p>
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $resident->birth_cert_issuer ?? 'Dinas Kependudukan dan Pencatatan Sipil' }}
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                        Dicetak {{ now()->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-10 h-10 text-amber-700/40 dark:text-amber-400/30">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
            </div>

        </div>
    </div>

    {{-- =====================================================================
         KARTU 3 — ID CARD KARTU KELUARGA
    ===================================================================== --}}
    <div x-show="tab === 'keluarga'" x-cloak class="w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-gray-950/10 dark:ring-white/10 bg-white dark:bg-gray-950 print:shadow-none print:ring-1 print:ring-gray-300 print:bg-white">

        <div class="h-2 print:h-1.5 w-full bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-700"></div>

        <div class="relative p-6 sm:p-8">

            <div class="pointer-events-none absolute -right-6 -bottom-6 opacity-[0.04] dark:opacity-[0.05] print:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-48 h-48 text-gray-950 dark:text-white">
                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122z" />
                </svg>
            </div>

            <div class="relative flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.2em] text-teal-700 dark:text-teal-400 uppercase">
                        Kartu Keluarga &middot; Anggota
                    </p>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white tracking-wide">
                        Kartu Keluarga
                    </h2>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-teal-50 text-teal-700 dark:bg-teal-500/15 dark:text-teal-400">
                    {{ $resident->relationship_to_head ?? '-' }}
                </span>
            </div>

            <div class="relative">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nomor Kartu Keluarga</p>
                <p class="text-xl font-mono font-bold text-gray-950 dark:text-white tracking-wider mb-6">
                    {{ $household?->no_kk ?? '-' }}
                </p>

                <div class="grid grid-cols-2 gap-x-6 gap-y-4">

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Anggota</p>
                        <p class="text-base font-bold text-gray-950 dark:text-white">
                            {{ $resident->full_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Status dalam Keluarga</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->relationship_to_head ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Status Perkawinan</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->marital_status ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Ayah</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->father_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nama Ibu</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $resident->mother_name ?? '-' }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Alamat</p>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $household?->address ?? '-' }}
                            @if($rt)
                                &middot; RT {{ $rt->number }} / RW {{ $rw?->number }}
                            @endif
                        </p>
                    </div>

                </div>
            </div>

            <div class="relative mt-6 pt-4 border-t border-gray-950/10 dark:border-white/10 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Diterbitkan oleh</p>
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">Dukuh Gondang</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                        Dicetak {{ now()->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
