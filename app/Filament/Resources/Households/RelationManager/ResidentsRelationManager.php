<?php

namespace App\Filament\Resources\Households\RelationManagers;

use App\Filament\Resources\Residents\ResidentResource;
use App\Models\Resident;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResidentsRelationManager extends RelationManager
{
    protected static string $relationship = 'residents';

    protected static ?string $title = 'Anggota Keluarga';

    protected static ?string $modelLabel = 'Warga';

    // =============================================================
    // Form ini sengaja hanya berisi field inti untuk "tambah cepat"
    // lewat wizard 3 langkah. household_id tidak ditampilkan karena
    // otomatis terisi sesuai KK ini (RelationManager yang mengurus).
    // Detail lengkap (alamat kelahiran, agama, pekerjaan, dokumen,
    // dll) dilengkapi setelah disimpan — otomatis diarahkan ke
    // halaman Edit Resident yang sebenarnya.
    // =============================================================

    private static function wizardSteps(): array
    {
        return [

            Step::make('Identitas')
                ->icon('heroicon-o-identification')
                ->description('NIK & nama sesuai KTP/KK')
                ->schema([

                    TextInput::make('nik')
                        ->label('NIK')
                        ->prefixIcon('heroicon-o-identification')
                        ->placeholder('Contoh: 3372010101010001')
                        ->helperText('Wajib 16 digit sesuai KTP/KK.')
                        ->inputMode('numeric')
                        ->extraInputAttributes([
                            'maxlength' => 16,
                            'pattern' => '[0-9]*',
                        ])
                        ->minLength(16)
                        ->maxLength(16)
                        ->rule('digits:16')
                        ->live(onBlur: true)
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'digits' => 'NIK harus tepat 16 digit.',
                            'unique' => 'NIK ini sudah terdaftar.',
                        ])
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('full_name')
                        ->label('Nama Lengkap')
                        ->prefixIcon('heroicon-o-user')
                        ->placeholder('Sesuai KTP, tanpa singkatan')
                        ->autocapitalize('words')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),

                ]),

            Step::make('Data Kelahiran')
                ->icon('heroicon-o-cake')
                ->description('Jenis kelamin & tanggal lahir')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->placeholder('Pilih jenis kelamin')
                                ->prefixIcon('heroicon-o-user')
                                ->native(false)
                                ->options([
                                    'Laki-laki' => 'Laki-laki',
                                    'Perempuan' => 'Perempuan',
                                ])
                                ->required(),

                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->prefixIcon('heroicon-o-calendar')
                                ->placeholder('Pilih tanggal lahir')
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->maxDate(now())
                                ->closeOnDateSelection()
                                ->required(),

                        ]),

                ]),

            Step::make('Status Keluarga')
                ->icon('heroicon-o-user-group')
                ->description('Kedudukan dalam Kartu Keluarga')
                ->schema([

                    Select::make('relationship_to_head')
                        ->label('Status dalam Keluarga')
                        ->placeholder('Pilih status dalam keluarga')
                        ->prefixIcon('heroicon-o-user-group')
                        ->native(false)
                        ->searchable()
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
                        ->helperText('Hanya boleh ada satu "Kepala Keluarga" per KK.')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('marital_status')
                        ->label('Status Perkawinan')
                        ->prefixIcon('heroicon-o-heart')
                        ->native(false)
                        ->options([
                            'Belum Kawin' => 'Belum Kawin',
                            'Kawin' => 'Kawin',
                            'Cerai Hidup' => 'Cerai Hidup',
                            'Cerai Mati' => 'Cerai Mati',
                        ])
                        ->default('Belum Kawin')
                        ->required()
                        ->columnSpanFull(),

                ]),

        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make(self::wizardSteps())
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')

            ->columns([

                TextColumn::make('identity')
                    ->label('Nama Warga')
                    ->state(fn(Resident $record) => $record->full_name)
                    ->description(fn(Resident $record) => self::maskNumber($record->nik))
                    ->weight('semibold')
                    ->searchable(
                        query: fn(Builder $query, string $search): Builder =>
                        $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%")
                    ),

                TextColumn::make('relationship_to_head')
                    ->label('Status Keluarga')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Kepala Keluarga' => 'primary',
                        'Suami', 'Istri' => 'info',
                        'Anak' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Laki-laki' => 'info',
                        'Perempuan' => 'pink',
                        default => 'gray',
                    }),

                TextColumn::make('age')
                    ->label('Usia')
                    ->formatStateUsing(
                        fn($state) => $state !== null ? "{$state} tahun" : '-'
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Pindah' => 'warning',
                        'Meninggal' => 'danger',
                        default => 'gray',
                    }),

            ])

            // Kepala Keluarga selalu tampil paling atas
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->orderByRaw("relationship_to_head = 'Kepala Keluarga' DESC")
                    ->orderBy('full_name')
            )

            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Anggota Keluarga')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Tambah Anggota Keluarga')
                    ->modalDescription('Isi data inti dulu, detail lainnya bisa dilengkapi setelah disimpan.')
                    ->modalWidth('2xl')
                    ->steps(self::wizardSteps())
                    ->createAnother(false)
                    ->successNotificationTitle(
                        'Anggota keluarga ditambahkan. Lengkapi datanya di halaman berikut.'
                    )
                    ->successRedirectUrl(
                        fn(Resident $record): string =>
                        ResidentResource::getUrl('edit', ['record' => $record])
                    ),
            ])

            ->recordActions([

                ViewAction::make()
                    ->url(
                        fn(Resident $record): string =>
                        ResidentResource::getUrl('view', ['record' => $record])
                    ),

                EditAction::make()
                    ->label('Lengkapi Data')
                    ->url(
                        fn(Resident $record): string =>
                        ResidentResource::getUrl('edit', ['record' => $record])
                    ),

                DeleteAction::make(),

            ])

            ->emptyStateHeading('Belum ada anggota keluarga')
            ->emptyStateDescription('Tambahkan anggota keluarga pertama, mulai dari Kepala Keluarga.')
            ->emptyStateIcon('heroicon-o-user-group');
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
