<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Sebaran Tingkat Pendidikan
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

            @if ($unmappedCount > 0)
                <span class="ml-2 text-amber-500">
                    · {{ $unmappedCount }} data pendidikan tidak cocok kategori baku, cek konsistensi input
                </span>
            @endif
        </div>

        <div
            id="education-distribution-wrapper"
            wire:ignore
            wire:key="education-distribution-wrapper-{{ $rwId }}-{{ $rtId }}"
            x-data
            x-init="
                const categories = {{ json_encode($categories) }};
                const maleSeries = {{ json_encode($maleSeries) }};
                const femaleSeries = {{ json_encode($femaleSeries) }};

                const containerEl = document.getElementById('education-distribution-chart');

                const maxCount = Math.max(1, ...maleSeries, ...femaleSeries);
                const niceTick = Math.max(1, Math.ceil(maxCount / 5));

                Highcharts.chart(containerEl, {
                    colors: ['#3b82f6', '#ec4899'],
                    chart: {
                        type: 'column',
                        inverted: true,
                        polar: true,
                        backgroundColor: 'transparent'
                    },
                    title: { text: 'Sebaran Tingkat Pendidikan', style: { color: '#e5e7eb' } },
                    subtitle: { text: 'Berdasarkan pendidikan terakhir, dipisah jenis kelamin', style: { color: '#9ca3af' } },
                    accessibility: { enabled: true },
                    tooltip: {
                        outside: true,
                        pointFormat: '<span style=\'color:{series.color}\'>{series.name}</span>: <b>{point.y}</b> orang<br/>'
                    },
                    pane: {
                        size: '85%',
                        innerSize: '20%',
                        endAngle: 270
                    },
                    xAxis: {
                        tickInterval: 1,
                        categories: categories,
                        labels: {
                            align: 'right',
                            allowOverlap: true,
                            step: 1,
                            y: 3,
                            style: { fontSize: '13px', color: '#e5e7eb' }
                        },
                        lineWidth: 0,
                        gridLineWidth: 0
                    },
                    yAxis: {
                        lineWidth: 0,
                        tickInterval: niceTick,
                        reversedStacks: false,
                        endOnTick: true,
                        showLastLabel: true,
                        gridLineWidth: 0,
                        allowDecimals: false,
                        labels: { style: { color: '#9ca3af' } }
                    },
                    legend: { enabled: true, itemStyle: { color: '#e5e7eb' } },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            borderWidth: 0,
                            pointPadding: 0,
                            groupPadding: 0.15,
                            borderRadius: { radius: '50%', where: 'all' }
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
                        data: maleSeries
                    }, {
                        name: 'Perempuan',
                        data: femaleSeries
                    }]
                });
            "
        >
            <div id="education-distribution-chart" style="min-height: 480px;"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
