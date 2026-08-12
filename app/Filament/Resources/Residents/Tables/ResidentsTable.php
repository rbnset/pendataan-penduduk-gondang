<?php

namespace App\Filament\Resources\Residents\Tables;

use App\Filament\Resources\Residents\Pages\ViewResident;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                // =====================================================
                // IDENTITAS
                // =====================================================

                TextColumn::make('identity')
                    ->label('Identitas Warga')
                    ->state(fn(Resident $record) => $record->full_name)
                    ->description(
                        fn(Resident $record) => self::maskNumber($record->nik)
                    )
                    ->weight('semibold')
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->where(function (Builder $query) use ($search) {
                                $query
                                    ->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('nik', 'like', "%{$search}%")
                                    ->orWhereHas(
                                        'household',
                                        fn(Builder $q) =>
                                        $q->where('no_kk', 'like', "%{$search}%")
                                    );
                            });
                        }
                    )
                    ->sortable(
                        query: fn(Builder $query, string $direction) =>
                        $query->orderBy('full_name', $direction)
                    ),

                // =====================================================
                // DATA UTAMA
                // =====================================================

                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'Laki-laki' => 'info',
                            'Perempuan' => 'pink',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('age')
                    ->label('Usia')
                    ->formatStateUsing(
                        fn($state) =>
                        $state !== null
                            ? $state . ' tahun'
                            : '-'
                    )
                    ->sortable(),

                TextColumn::make('relationship_to_head')
                    ->label('Status Keluarga')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'Kepala Keluarga' => 'primary',
                            'Suami', 'Istri' => 'info',
                            'Anak' => 'success',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'Aktif' => 'success',
                            'Pindah' => 'warning',
                            'Meninggal' => 'danger',
                            default => 'gray',
                        }
                    ),

                // =====================================================
                // DATA TAMBAHAN
                // =====================================================

                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('birth_place')
                    ->label('Tempat Lahir')
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('blood_type')
                    ->label('Gol. Darah')
                    ->placeholder('Belum diketahui')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('marital_status')
                    ->label('Status Kawin')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('religion')
                    ->label('Agama')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('education')
                    ->label('Pendidikan')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('occupation')
                    ->label('Pekerjaan')
                    ->placeholder('Tidak/belum bekerja')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('father_name')
                    ->label('Nama Ayah')
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('mother_name')
                    ->label('Nama Ibu')
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make('has_ktp')
                    ->label('KTP')
                    ->boolean()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('birth_cert_number')
                    ->label('No. Akta Lahir')
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('birth_cert_issuer')
                    ->label('Penerbit Akta')
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('status_date')
                    ->label('Tanggal Status')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('status_note')
                    ->label('Keterangan Status')
                    ->limit(30)
                    ->placeholder('Belum diisi')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])

            // =========================================================
            // SEARCH
            // =========================================================

            ->searchPlaceholder(
                'Cari nama, NIK, atau nomor KK...'
            )

            ->searchDebounce('500ms')

            ->striped()

            // =========================================================
            // URUTAN DATA
            // =========================================================

            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->orderBy('household_id')
                    ->orderByRaw(
                        "relationship_to_head = 'Kepala Keluarga' DESC"
                    )
                    ->orderBy('full_name')
            )

            // =========================================================
            // PAGINATION
            // =========================================================

            ->paginated([
                10,
                25,
                50,
                'all',
            ])

            ->defaultPaginationPageOption(25)

            // =========================================================
            // FILTER
            // =========================================================

            ->filters([

                // -----------------------------------------------------
                // WILAYAH
                // -----------------------------------------------------

                SelectFilter::make('rw')
                    ->label('RW')
                    ->options(
                        fn() => Rw::pluck('number', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {

                            if (blank($data['value'] ?? null)) {
                                return $query;
                            }

                            return $query->whereHas(
                                'household.rt',
                                fn(Builder $q) =>
                                $q->where(
                                    'rw_id',
                                    $data['value']
                                )
                            );
                        }
                    ),

                SelectFilter::make('rt')
                    ->label('RT')
                    ->options(
                        fn() => Rt::with('rw')
                            ->get()
                            ->mapWithKeys(
                                fn(Rt $rt) => [
                                    $rt->id =>
                                    "RT {$rt->number} / RW {$rt->rw->number}",
                                ]
                            )
                    )
                    ->searchable()
                    ->preload()
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {

                            if (blank($data['value'] ?? null)) {
                                return $query;
                            }

                            return $query->whereHas(
                                'household',
                                fn(Builder $q) =>
                                $q->where(
                                    'rt_id',
                                    $data['value']
                                )
                            );
                        }
                    ),

                // -----------------------------------------------------
                // KEPENDUDUKAN
                // -----------------------------------------------------

                SelectFilter::make('status')
                    ->label('Status Kependudukan')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Pindah' => 'Pindah',
                        'Meninggal' => 'Meninggal',
                    ]),

                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),

                SelectFilter::make('relationship_to_head')
                    ->label('Status Keluarga')
                    ->options([
                        'Kepala Keluarga' => 'Kepala Keluarga',
                        'Suami' => 'Suami',
                        'Istri' => 'Istri',
                        'Anak' => 'Anak',
                        'Menantu' => 'Menantu',
                        'Cucu' => 'Cucu',
                        'Orang Tua' => 'Orang Tua',
                        'Famili Lain' => 'Famili Lain',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->searchable(),

                SelectFilter::make('marital_status')
                    ->label('Status Kawin')
                    ->options([
                        'Belum Kawin' => 'Belum Kawin',
                        'Kawin' => 'Kawin',
                        'Cerai Hidup' => 'Cerai Hidup',
                        'Cerai Mati' => 'Cerai Mati',
                    ]),

                // -----------------------------------------------------
                // SOSIAL
                // -----------------------------------------------------

                SelectFilter::make('religion')
                    ->label('Agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen' => 'Kristen',
                        'Katolik' => 'Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ])
                    ->searchable(),

                SelectFilter::make('education')
                    ->label('Pendidikan')
                    ->options([
                        'Tidak/Belum Sekolah' => 'Tidak/Belum Sekolah',
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA' => 'SMA',
                        'D3' => 'D3',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->searchable(),

                // -----------------------------------------------------
                // USIA
                // -----------------------------------------------------

                Filter::make('age_range')
                    ->label('Rentang Usia')
                    ->schema([

                        TextInput::make('age_from')
                            ->label('Usia minimal')
                            ->placeholder('Contoh: 17')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('age_to')
                            ->label('Usia maksimal')
                            ->placeholder('Contoh: 60')
                            ->numeric()
                            ->minValue(0),

                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {

                            return $query
                                ->when(
                                    filled(
                                        $data['age_from'] ?? null
                                    ),
                                    fn(Builder $q) =>
                                    $q->whereDate(
                                        'birth_date',
                                        '<=',
                                        now()
                                            ->subYears(
                                                (int) $data['age_from']
                                            )
                                            ->format('Y-m-d')
                                    )
                                )
                                ->when(
                                    filled(
                                        $data['age_to'] ?? null
                                    ),
                                    fn(Builder $q) =>
                                    $q->whereDate(
                                        'birth_date',
                                        '>=',
                                        now()
                                            ->subYears(
                                                (int) $data['age_to'] + 1
                                            )
                                            ->format('Y-m-d')
                                    )
                                );
                        }
                    ),

                SelectFilter::make('birth_year')
                    ->label('Tahun Lahir')
                    ->options(
                        fn() => Resident::query()
                            ->whereNotNull('birth_date')
                            ->selectRaw(
                                'YEAR(birth_date) as year'
                            )
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year', 'year')
                    )
                    ->searchable(),
            ])

            // =========================================================
            // ACTION
            // =========================================================

            ->recordUrl(
                fn(Resident $record): string =>
                ViewResident::getUrl(['record' => $record])
            )

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function maskNumber(?string $number): string
    {
        if (blank($number)) {
            return '-';
        }

        return substr($number, 0, 4)
            . '••••••••'
            . substr($number, -4);
    }
}
