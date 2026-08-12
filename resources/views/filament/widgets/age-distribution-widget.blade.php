<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Sebaran Kelompok Usia
        </x-slot>

        @once
            <style>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
        </div>

        <div class="mb-4 text-sm text-gray-500">
            Total warga sesuai filter: <span class="font-semibold text-gray-900 dark:text-white">{{ $total }}</span>
        </div>

        {{-- Indikator kelompok usia berdasarkan klasifikasi Depkes RI (Kemenkes) 2009. --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach ($lifeStages as $stage)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $stage['color'] }}"></span>
                    <span class="text-xs">
                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $stage['label'] }}</span>
                        <span class="text-gray-400 dark:text-gray-500">· {{ $stage['range'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">— {{ $stage['count'] }} ({{ $stage['percent'] }}%)</span>
                    </span>
                </div>
            @endforeach
        </div>

        <div
            id="age-distribution-wrapper"
            wire:ignore
            wire:key="age-distribution-wrapper-{{ $rwId }}-{{ $rtId }}"
            x-data
            x-init="
                const categories = {{ json_encode($categories) }};
                const maleSeries = {{ json_encode($maleSeries) }};
                const femaleSeries = {{ json_encode($femaleSeries) }};

                const containerEl = document.getElementById('age-distribution-chart');

                Highcharts.chart(containerEl, {
                    chart: { type: 'column', backgroundColor: 'transparent' },
                    title: { text: 'Sebaran Usia per Kelompok', style: { color: '#e5e7eb' } },
                    subtitle: { text: 'Berdasarkan klasifikasi Depkes RI, dipisah jenis kelamin', style: { color: '#9ca3af' } },
                    accessibility: { enabled: true },
                    xAxis: {
                        categories: categories,
                        title: { text: 'Kelompok Usia', style: { color: '#9ca3af' } },
                        labels: { style: { color: '#e5e7eb' }, rotation: -30 },
                        lineColor: '#374151',
                        tickColor: '#374151'
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Jumlah Warga', style: { color: '#9ca3af' } },
                        labels: { style: { color: '#e5e7eb' } },
                        gridLineColor: '#374151',
                        allowDecimals: false
                    },
                    tooltip: {
                        headerFormat: '<span style=\'font-size:11px\'>{point.key}</span><br>',
                        pointFormat: '<span style=\'color:{series.color}\'>{series.name}</span>: <b>{point.y}</b> orang'
                    },
                    legend: { enabled: true, itemStyle: { color: '#e5e7eb' } },
                    plotOptions: {
                        column: {
                            pointPadding: 0.1,
                            groupPadding: 0.1,
                            borderWidth: 0
                        }
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
                        name: 'Laki-laki',
                        data: maleSeries,
                        color: '#3b82f6'
                    }, {
                        name: 'Perempuan',
                        data: femaleSeries,
                        color: '#ec4899'
                    }],
                    responsive: {
                        rules: [{
                            condition: { maxWidth: 500 },
                            chartOptions: {
                                xAxis: { labels: { rotation: -45 } }
                            }
                        }]
                    }
                });
            "
        >
            <div id="age-distribution-chart"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
