<?php

namespace App\Filament\Resources\Residents\Schemas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Resident;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ResidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nik')
                    ->label('NIK')
                    ->formatStateUsing(function (?string $state, Resident $record): string {
                        if (blank($state)) {
                            return '-';
                        }

                        $revealedUntil = session("resident_nik_revealed.{$record->getKey()}");

                        if ($revealedUntil && now()->lessThan($revealedUntil)) {
                            return $state;
                        }

                        return substr($state, 0, 4) . '••••••••' . substr($state, -4);
                    })
                    ->suffixAction(
                        Action::make('revealNik')
                            ->label('Tampilkan NIK')
                            ->icon(Heroicon::Eye)
                            ->color('gray')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password akun Anda')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->autocomplete('current-password')
                                    ->rules([
                                        function (): \Closure {
                                            return function (
                                                string $attribute,
                                                mixed $value,
                                                \Closure $fail
                                            ): void {
                                                $user = Auth::user();

                                                if (
                                                    ! $user ||
                                                    ! Hash::check($value, $user->getAuthPassword())
                                                ) {
                                                    $fail('Password yang dimasukkan tidak benar.');
                                                }
                                            };
                                        },
                                    ]),
                            ])
                            ->modalHeading('Verifikasi untuk Melihat NIK')
                            ->modalDescription(
                                'Masukkan password akun Anda untuk melihat NIK lengkap. NIK akan ditampilkan sementara.'
                            )
                            ->modalSubmitActionLabel('Tampilkan NIK')
                            ->modalCancelActionLabel('Batal')
                            ->action(function (Resident $record): void {
                                session([
                                    "resident_nik_revealed.{$record->getKey()}" => now()->addMinutes(5),
                                ]);
                            }),
                    ),
                TextEntry::make('full_name')
                    ->label('Nama Lengkap'),
                TextEntry::make('household.no_kk')
                    ->label('No. KK'),
                TextEntry::make('user.name')
                    ->label('Akun Login')
                    ->placeholder('-'),
                TextEntry::make('birth_place')
                    ->label('Tempat Lahir')
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d M Y'),
                TextEntry::make('age')
                    ->label('Usia')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . ' tahun' : '-'),
                TextEntry::make('gender')
                    ->label('Jenis Kelamin'),
                TextEntry::make('blood_type')
                    ->label('Golongan Darah')
                    ->placeholder('-'),
                TextEntry::make('religion')
                    ->label('Agama')
                    ->placeholder('-'),
                TextEntry::make('education')
                    ->label('Pendidikan')
                    ->placeholder('-'),
                TextEntry::make('occupation')
                    ->label('Pekerjaan')
                    ->placeholder('-'),
                TextEntry::make('marital_status')
                    ->label('Status Perkawinan'),
                TextEntry::make('relationship_to_head')
                    ->label('Status dalam Keluarga'),
                TextEntry::make('father_name')
                    ->label('Nama Ayah')
                    ->placeholder('-'),
                TextEntry::make('mother_name')
                    ->label('Nama Ibu')
                    ->placeholder('-'),
                TextEntry::make('birth_cert_number')
                    ->label('Nomor Akta Lahir')
                    ->placeholder('-'),
                TextEntry::make('birth_cert_issuer')
                    ->label('Kabupaten/Kota Penerbit Akta')
                    ->placeholder('-'),
                IconEntry::make('has_ktp')
                    ->label('Sudah Punya KTP')
                    ->boolean(),
                TextEntry::make('status')
                    ->label('Status Kependudukan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Pindah' => 'warning',
                        'Meninggal' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('status_date')
                    ->label('Tanggal Pindah/Meninggal')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextEntry::make('status_note')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}