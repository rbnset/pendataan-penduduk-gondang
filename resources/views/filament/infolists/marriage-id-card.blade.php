@php
$marriage = $record;
$husband = $marriage->husband;
$wife = $marriage->wife;

$isDivorced = filled($marriage->divorce_date);

$statusColor = $isDivorced
? ['bg' => 'bg-rose-50 dark:bg-rose-500/15', 'text' => 'text-rose-700 dark:text-rose-400', 'dot' => 'bg-rose-500 dark:bg-rose-400']
: ['bg' => 'bg-emerald-50 dark:bg-emerald-500/15', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'];

$initialsOf = fn ($name) => collect(explode(' ', trim($name ?? '')))
->filter()
->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
->take(2)
->implode('');

$accent = 'from-rose-500 via-pink-500 to-fuchsia-600';
@endphp

<div class="w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-gray-950/10 dark:ring-white/10 bg-white dark:bg-gray-950 print:shadow-none print:ring-1 print:ring-gray-300 print:bg-white">

    {{-- ============ STRIP AKSEN ATAS ============ --}}
    <div class="h-2 print:h-1.5 w-full bg-gradient-to-r {{ $accent }}"></div>

    <div class="relative p-6 sm:p-8">

        {{-- watermark ikon hati, dekoratif --}}
        <div class="pointer-events-none absolute -right-6 -bottom-6 opacity-[0.04] dark:opacity-[0.05] print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-48 h-48 text-gray-950 dark:text-white">
                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
            </svg>
        </div>

        {{-- ============ HEADER ============ --}}
        <div class="relative flex items-start justify-between gap-4 mb-6">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.2em] text-gray-500 dark:text-gray-400 uppercase">
                    Sistem Informasi Kependudukan
                </p>
                <h2 class="text-lg font-bold text-gray-950 dark:text-white tracking-wide">
                    Buku Nikah
                </h2>
            </div>

            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }}"></span>
                {{ $isDivorced ? 'Bercerai' : 'Aktif' }}
            </span>
        </div>

        {{-- ============ PASANGAN: SUAMI & ISTRI ============ --}}
        <div class="relative grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr] items-center gap-4 mb-6">

            {{-- Suami --}}
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-16 h-16 rounded-xl bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-600 flex items-center justify-center shadow-lg ring-4 ring-gray-950/5 dark:ring-white/5">
                    <span class="text-xl font-bold text-white">{{ $initialsOf($husband?->full_name) ?: '?' }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Suami</p>
                    <p class="text-base font-bold text-gray-950 dark:text-white truncate">
                        {{ $husband?->full_name ?? 'Belum ditentukan' }}
                    </p>
                    <p class="text-[11px] font-mono text-gray-400 dark:text-gray-500">
                        {{ $husband?->nik ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- ikon hati penghubung --}}
            <div class="hidden sm:flex items-center justify-center text-rose-400 dark:text-rose-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                </svg>
            </div>

            {{-- Istri --}}
            <div class="flex items-center gap-4 sm:flex-row-reverse sm:text-right">
                <div class="shrink-0 w-16 h-16 rounded-xl bg-gradient-to-br from-pink-500 via-fuchsia-500 to-purple-600 flex items-center justify-center shadow-lg ring-4 ring-gray-950/5 dark:ring-white/5">
                    <span class="text-xl font-bold text-white">{{ $initialsOf($wife?->full_name) ?: '?' }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Istri</p>
                    <p class="text-base font-bold text-gray-950 dark:text-white truncate">
                        {{ $wife?->full_name ?? 'Belum ditentukan' }}
                    </p>
                    <p class="text-[11px] font-mono text-gray-400 dark:text-gray-500">
                        {{ $wife?->nik ?? '-' }}
                    </p>
                </div>
            </div>

        </div>

        {{-- ============ DATA AKTA NIKAH ============ --}}
        <div class="relative pt-5 border-t border-gray-950/10 dark:border-white/10">

            <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nomor Akta Nikah</p>
            <p class="text-xl font-mono font-bold text-gray-950 dark:text-white tracking-wider mb-4">
                {{ $marriage->marriage_certificate_number ?? '-' }}
            </p>

            <div class="grid grid-cols-2 gap-x-6 gap-y-3">

                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Tanggal Nikah</p>
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ optional($marriage->marriage_date)->translatedFormat('d F Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">KUA Pencatat</p>
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $marriage->kua_name ?? '-' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- ============ INFORMASI PERCERAIAN (jika ada) ============ --}}
        @if($isDivorced)
        <div class="relative mt-5 p-4 rounded-xl bg-rose-50/70 dark:bg-rose-500/10 ring-1 ring-rose-200 dark:ring-rose-500/20">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-rose-600 dark:text-rose-400 mb-3 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                Informasi Perceraian
            </p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nomor Akta Cerai</p>
                    <p class="text-sm font-mono font-semibold text-gray-950 dark:text-white">
                        {{ $marriage->divorce_certificate_number ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Tanggal Cerai</p>
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ optional($marriage->divorce_date)->translatedFormat('d F Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- ============ FOOTER ============ --}}
        <div class="relative mt-6 pt-4 border-t border-gray-950/10 dark:border-white/10 flex items-end justify-between gap-4">

            <div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500">Diterbitkan oleh</p>
                <p class="text-sm font-semibold text-gray-950 dark:text-white">Dukuh Gondang</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                    Dicetak {{ now()->translatedFormat('d M Y, H:i') }}
                </p>
            </div>

            {{-- barcode dekoratif dari nomor akta nikah --}}
            <div class="flex items-end gap-[2px] h-10 opacity-70 print:opacity-100">
                @foreach(str_split(preg_replace('/\D/', '', $marriage->marriage_certificate_number ?? '') ?: '0000000000000000') as $digit)
                <span
                    class="w-[3px] bg-gray-400 dark:bg-gray-300"
                    style="height: {{ 8 + ((int) $digit * 3) }}px"></span>
                @endforeach
            </div>

        </div>

    </div>
</div>
