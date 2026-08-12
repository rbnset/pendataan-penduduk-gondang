<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Status Perkawinan Warga
        </x-slot>

        {{-- Menggunakan tabel data bawaan Highcharts (tombol "View Data" di menu
             export pojok kanan atas chart). Ini butuh modul export-data.js
             ter-load di layout, selain highcharts.js dan exporting.js. --}}
        @once
            <style>
                /* Styling ringan supaya tabel data bawaan Highcharts (yang
                   default putih polos) senada dengan tema gelap dashboard. */
                .highcharts-data-table {
                    margin-top: 16px;
                    font-size: 13px;
                }
                .highcharts-data-table table {
                    width: 100%;
                    border-collapse: collapse;
                    color: #e5e7eb;
                }
                .highcharts-data-table th {
                    background: #1f2937;
                    color: #f9fafb;
                    text-align: left;
                    padding: 8px 12px;
                    border-bottom: 2px solid #374151;
                }
                .highcharts-data-table td {
                    padding: 8px 12px;
                    border-bottom: 1px solid #374151;
                }
                .highcharts-data-table tr:hover td {
                    background: #111827;
                }
                .highcharts-data-table caption {
                    caption-side: top;
                    text-align: left;
                    font-weight: 600;
                    color: #f9fafb;
                    padding-bottom: 8px;
                }
            </style>
        @endonce

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="text-sm font-medium mb-1 block">Filter RW</label>
                <select wire:model.live="rwId" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua RW</option>
                    @foreach ($rws as $rw)
                        <option value="{{ $rw->id }}">RW {{ $rw->number }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">Filter RT</label>
                <select wire:model.live="rtId" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}">RT {{ $rt->number }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">Status Perkawinan</label>
                <select wire:model.live="statusFilter" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">Tahun</label>
                <select wire:model.live="year" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4 text-sm text-gray-500">
            Total warga sesuai filter: <span class="font-semibold text-gray-900 dark:text-white">{{ $total }}</span>
        </div>

        <div
            id="marital-status-wrapper"
            wire:ignore
            wire:key="marital-status-wrapper-{{ $rwId }}-{{ $rtId }}-{{ $statusFilter }}-{{ $year }}"
            x-data
            x-init="
                const statusData = {{ json_encode($statusData) }};
                const detailData = {{ json_encode($detailData) }}.map(function (d) {
                    return {
                        name: d.name,
                        y: d.y,
                        color: Highcharts.color(d.color).brighten(d.brightness).get()
                    };
                });

                const containerEl = document.getElementById('marital-status-chart');

                Highcharts.chart(containerEl, {
                    chart: { type: 'pie', backgroundColor: 'transparent' },
                    title: { text: 'Status Perkawinan per RW/RT', style: { color: '#e5e7eb' } },
                    subtitle: { text: 'Klik segmen dalam untuk lihat detail wilayah', style: { color: '#9ca3af' } },
                    accessibility: { enabled: true },
                    tooltip: {
                        headerFormat: '<span style=\'font-size:11px\'>{series.name}</span><br>',
                        pointFormat: '<span style=\'color:{point.color}\'>{point.name}</span>: <b>{point.y}</b> orang ({point.percentage:.1f}%)'
                    },
                    legend: { enabled: true, itemStyle: { color: '#e5e7eb' } },
                    plotOptions: {
                        pie: { shadow: false, center: ['50%', '50%'], allowPointSelect: true, cursor: 'pointer' }
                    },
                    exporting: {
                        enabled: true,
                        buttons: {
                            contextButton: {
                                menuItems: [
                                    'viewFullscreen', 'printChart', 'separator',
                                    'downloadPNG', 'downloadJPEG', 'downloadSVG', 'separator',
                                    'downloadCSV', 'downloadXLS', 'viewData'
                                ]
                            }
                        }
                    },
                    series: [{
                        name: 'Status',
                        data: statusData,
                        size: '45%',
                        dataLabels: { color: '#ffffff', distance: '-50%', format: '{point.name}' }
                    }, {
                        name: 'Wilayah',
                        data: detailData,
                        size: '80%',
                        innerSize: '60%',
                        dataLabels: {
                            format: '<b>{point.name}:</b> <span style=\'opacity:0.6\'>{point.y}</span>',
                            filter: { property: 'y', operator: '>', value: 0 },
                            style: { fontWeight: 'normal', color: '#e5e7eb' }
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: { maxWidth: 500 },
                            chartOptions: { series: [{}, { dataLabels: { distance: 10 } }] }
                        }]
                    }
                });
            "
        >
            <div id="marital-status-chart"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
