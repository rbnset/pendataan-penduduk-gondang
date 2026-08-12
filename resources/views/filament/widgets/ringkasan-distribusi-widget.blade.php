<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Persebaran Warga
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="text-sm font-medium mb-1 block">Filter RW</label>
                <select wire:model.live="rwId" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua RW</option>
                    @foreach ($this->rwOptions() as $rw)
                        <option value="{{ $rw->id }}">RW {{ $rw->number }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block">Filter RT</label>
                <select wire:model.live="rtId" class="fi-select-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua RT</option>
                    @foreach ($this->rtOptions() as $rt)
                        <option value="{{ $rt->id }}">RT {{ $rt->number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div
            id="ringkasan-distribusi-wrapper"
            wire:ignore
            wire:key="ringkasan-distribusi-wrapper-{{ $rwId }}-{{ $rtId }}"
            x-data
            x-init="
                const genderData = {{ Illuminate\Support\Js::from($this->genderChartData()) }};
                const rtData = {{ Illuminate\Support\Js::from($this->rtBreakdownData()) }};

                const rtLabels = rtData.map(row => row[0]);
                const kkCounts = rtData.map(row => row[1]);
                const wargaCounts = rtData.map(row => row[2]);
                const rataCounts = rtData.map(row => row[3]);

                const genderMap = Object.fromEntries(genderData);
                const totalWarga = (genderMap['Laki-laki'] || 0) + (genderMap['Perempuan'] || 0);

                const el = document.getElementById('ringkasan-combo-chart');

                Highcharts.chart(el, {
                    chart: { backgroundColor: 'transparent' },
                    credits: { enabled: false },
                    dataTable: {
                        columns: {
                            RT: rtLabels,
                            KK: kkCounts,
                            Warga: wargaCounts,
                            Rata: rataCounts
                        }
                    },
                    title: {
                        text: 'Ringkasan Persebaran Warga per RT',
                        style: { color: '#e5e7eb' }
                    },
                    xAxis: {
                        type: 'category',
                        labels: { style: { color: '#e5e7eb' }, rotation: -45 },
                        lineColor: '#374151',
                        tickColor: '#374151'
                    },
                    yAxis: [{
                        title: { text: 'Jumlah', style: { color: '#9ca3af' } },
                        labels: { style: { color: '#9ca3af' } },
                        gridLineColor: '#1f2937',
                        allowDecimals: false
                    }, {
                        title: { text: 'Rata-rata Anggota/KK', style: { color: '#9ca3af' } },
                        labels: { style: { color: '#9ca3af' } },
                        gridLineWidth: 0,
                        opposite: true
                    }],
                    tooltip: {
                        shared: true
                    },
                    legend: {
                        itemStyle: { color: '#e5e7eb' }
                    },
                    plotOptions: {
                        series: {
                            borderRadius: '25%',
                            dataMapping: { name: 'RT' }
                        }
                    },
                    series: [{
                        type: 'column',
                        name: 'Jumlah KK',
                        color: '#3b82f6',
                        dataMapping: { y: 'KK' }
                    }, {
                        type: 'column',
                        name: 'Jumlah Warga',
                        color: '#10b981',
                        dataMapping: { y: 'Warga' }
                    }, {
                        type: 'line',
                        name: 'Rata-rata Anggota/KK',
                        step: 'center',
                        yAxis: 1,
                        color: '#f59e0b',
                        dataMapping: { y: 'Rata' },
                        marker: {
                            lineWidth: 2,
                            lineColor: '#f59e0b',
                            fillColor: '#111827'
                        }
                    }, {
                        type: 'pie',
                        name: 'Sebaran Gender',
                        data: [{
                            name: 'Laki-laki',
                            y: genderMap['Laki-laki'] || 0,
                            color: '#3b82f6',
                            dataLabels: {
                                enabled: true,
                                distance: -25,
                                format: '{point.total}',
                                style: {
                                    fontSize: '13px',
                                    color: '#e5e7eb',
                                    textOutline: 'none'
                                }
                            }
                        }, {
                            name: 'Perempuan',
                            y: genderMap['Perempuan'] || 0,
                            color: '#ec4899'
                        }],
                        center: ['88%', '15%'],
                        size: '28%',
                        innerSize: '65%',
                        showInLegend: false,
                        dataLabels: { enabled: false },
                        tooltip: {
                            pointFormat: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                        }
                    }]
                });
            "
        >
            <div id="ringkasan-combo-chart" style="min-height: 420px;"></div>
        </div>

        <div class="mt-6">
            <h3 class="text-sm font-medium mb-3 text-gray-500">Detail per RT</h3>

            <div class="overflow-x-auto rounded-lg border border-gray-800">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left bg-gray-900 text-gray-400">
                            <th class="py-2 px-4">RT</th>
                            <th class="py-2 px-4">Jumlah KK</th>
                            <th class="py-2 px-4">Jumlah Warga</th>
                            <th class="py-2 px-4">Rata-rata Anggota / KK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->rtBreakdownData() as $row)
                            <tr class="border-t border-gray-800 hover:bg-gray-900/50">
                                <td class="py-2 px-4">{{ $row[0] }}</td>
                                <td class="py-2 px-4">{{ $row[1] }}</td>
                                <td class="py-2 px-4">{{ $row[2] }}</td>
                                <td class="py-2 px-4">{{ $row[3] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                                    Tidak ada data untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
