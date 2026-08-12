<?php

namespace App\Filament\Resources\Residents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ResidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =========================================================
                // IDENTITAS WARGA
                // =========================================================
                Section::make('Identitas Warga')
                    ->description('Data utama untuk mengidentifikasi warga.')
                    ->icon('heroicon-o-identification')
                    ->schema([

                        TextInput::make('nik')
                            ->label('NIK')
                            ->placeholder('Contoh: 3309051219990001')
                            ->helperText('NIK terdiri dari 16 digit angka.')
                            ->inputMode('numeric')
                            ->length(16)
                            ->maxLength(16)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Budi Santoso')
                            ->required()
                            ->columnSpanFull(),

                        Select::make('household_id')
                            ->relationship('household', 'no_kk')
                            ->label('Kartu Keluarga (No. KK)')
                            ->placeholder('Pilih nomor KK')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Akun Login')
                            ->placeholder('Pilih akun jika warga memiliki akses login')
                            ->helperText('Opsional — dapat dikosongkan.')
                            ->searchable()
                            ->preload()
                            ->default(null)
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // =========================================================
                // DATA KELAHIRAN
                // =========================================================
                Section::make('Data Kelahiran')
                    ->description('Informasi tempat, tanggal, dan karakteristik kelahiran warga.')
                    ->icon('heroicon-o-cake')
                    ->schema([

                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('Contoh: Bantul')
                            ->default(null),

                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->placeholder('Pilih tanggal lahir')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->placeholder('Pilih jenis kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->required(),

                        Select::make('blood_type')
                            ->label('Golongan Darah')
                            ->placeholder('Pilih golongan darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'AB' => 'AB',
                                'O' => 'O',
                                'Tidak Tahu' => 'Tidak Tahu',
                            ])
                            ->default(null),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // =========================================================
                // DATA KELUARGA
                // =========================================================
                Section::make('Data Keluarga')
                    ->description('Informasi hubungan warga dalam keluarga.')
                    ->icon('heroicon-o-user-group')
                    ->schema([

                        Select::make('relationship_to_head')
                            ->label('Status dalam Keluarga')
                            ->placeholder('Pilih status dalam keluarga')
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
                            ->required(),

                        Select::make('marital_status')
                            ->label('Status Perkawinan')
                            ->placeholder('Pilih status perkawinan')
                            ->options([
                                'Belum Kawin' => 'Belum Kawin',
                                'Kawin' => 'Kawin',
                                'Cerai Hidup' => 'Cerai Hidup',
                                'Cerai Mati' => 'Cerai Mati',
                            ])
                            ->default('Belum Kawin')
                            ->required(),

                        TextInput::make('father_name')
                            ->label('Nama Ayah')
                            ->placeholder('Contoh: Bapak Suharto')
                            ->default(null),

                        TextInput::make('mother_name')
                            ->label('Nama Ibu')
                            ->placeholder('Contoh: Ibu Siti')
                            ->default(null),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // =========================================================
                // DATA KEPENDUDUKAN
                // =========================================================
                Section::make('Data Kependudukan')
                    ->description('Status dan dokumen administrasi kependudukan warga.')
                    ->icon('heroicon-o-building-library')
                    ->schema([

                        Select::make('status')
                            ->label('Status Kependudukan')
                            ->placeholder('Pilih status kependudukan')
                            ->options([
                                'Aktif' => 'Aktif',
                                'Pindah' => 'Pindah',
                                'Meninggal' => 'Meninggal',
                            ])
                            ->default('Aktif')
                            ->required()
                            ->live(),

                        Toggle::make('has_ktp')
                            ->label('Sudah Punya KTP')
                            ->helperText('Aktifkan jika warga sudah memiliki KTP.')
                            ->default(false),

                        TextInput::make('birth_cert_number')
                            ->label('Nomor Akta Lahir')
                            ->placeholder('Contoh: 1234-LU-2020')
                            ->default(null),

                        TextInput::make('birth_cert_issuer')
                            ->label('Kabupaten/Kota Penerbit Akta')
                            ->placeholder('Contoh: Kabupaten Bantul')
                            ->default(null),

                        DatePicker::make('status_date')
                            ->label('Tanggal Pindah/Meninggal')
                            ->placeholder('Pilih tanggal')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->disabled(fn(Get $get): bool => $get('status') === 'Aktif')
                            ->required(fn(Get $get): bool => $get('status') !== 'Aktif')
                            ->dehydrated(fn(Get $get): bool => $get('status') !== 'Aktif'),

                        Textarea::make('status_note')
                            ->label('Keterangan Status')
                            ->placeholder('Tambahkan keterangan jika diperlukan...')
                            ->rows(3)
                            ->disabled(fn(Get $get): bool => $get('status') === 'Aktif')
                            ->dehydrated(fn(Get $get): bool => $get('status') !== 'Aktif')
                            ->default(null)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // =========================================================
                // DATA SOSIAL
                // =========================================================
                Section::make('Data Sosial')
                    ->description('Informasi pendidikan, agama, dan pekerjaan warga.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([

                        Select::make('religion')
                            ->label('Agama')
                            ->placeholder('Pilih agama')
                            ->options([
                                'Islam' => 'Islam',
                                'Kristen' => 'Kristen',
                                'Katolik' => 'Katolik',
                                'Hindu' => 'Hindu',
                                'Buddha' => 'Buddha',
                                'Konghucu' => 'Konghucu',
                            ])
                            ->default(null),

                        Select::make('education')
                            ->label('Pendidikan Terakhir')
                            ->placeholder('Pilih pendidikan terakhir')
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
                            ->default(null),

                        TextInput::make('occupation')
                            ->label('Pekerjaan')
                            ->placeholder('Contoh: Petani, Guru, Wiraswasta')
                            ->default(null),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
