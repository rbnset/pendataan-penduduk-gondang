<?php

namespace App\Filament\Resources\Marriages\Schemas;

use App\Models\Marriage;
use App\Models\Resident;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class MarriageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('husband_resident_id')
                    ->label('Suami')
                    ->relationship(
                        name: 'husband',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('gender', 'Laki-laki')
                            ->where('status', 'Aktif'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->different('wife_resident_id')
                    ->rules([
                        function ($record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                $query = Marriage::active()
                                    ->where(function ($query) use ($value) {
                                        $query
                                            ->where('husband_resident_id', $value)
                                            ->orWhere('wife_resident_id', $value);
                                    });

                                if ($record) {
                                    $query->whereKeyNot($record->id);
                                }

                                if ($query->exists()) {
                                    $fail('Resident ini masih memiliki perkawinan aktif.');
                                }
                            };
                        },
                    ]),

                Select::make('wife_resident_id')
                    ->label('Istri')
                    ->relationship(
                        name: 'wife',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('gender', 'Perempuan')
                            ->where('status', 'Aktif'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->different('husband_resident_id')
                    ->rules([
                        function ($record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                $query = Marriage::active()
                                    ->where(function ($query) use ($value) {
                                        $query
                                            ->where('husband_resident_id', $value)
                                            ->orWhere('wife_resident_id', $value);
                                    });

                                if ($record) {
                                    $query->whereKeyNot($record->id);
                                }

                                if ($query->exists()) {
                                    $fail('Resident ini masih memiliki perkawinan aktif.');
                                }
                            };
                        },
                    ]),

                TextInput::make('marriage_certificate_number')
                    ->label('Nomor Akta Nikah')
                    ->maxLength(50)
                    ->default(null),

                DatePicker::make('marriage_date')
                    ->label('Tanggal Nikah')
                    ->required()
                    ->maxDate(now()),

                TextInput::make('kua_name')
                    ->label('KUA')
                    ->maxLength(150)
                    ->default(null),

                TextInput::make('divorce_certificate_number')
                    ->label('Nomor Akta Cerai')
                    ->maxLength(50)
                    ->default(null)
                    ->requiredWith('divorce_date'),

                DatePicker::make('divorce_date')
                    ->label('Tanggal Cerai')
                    ->after('marriage_date')
                    ->maxDate(now())
                    ->requiredWith('divorce_certificate_number'),
            ]);
    }
}