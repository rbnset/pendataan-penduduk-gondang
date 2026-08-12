<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Marriage;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    protected Generator $faker;

    protected int $nikCounter = 3300000000000001;

    protected int $kkCounter = 3300000000000001;

    /**
     * Sesuaikan daftar pilihan di bawah ini dengan enum/constraint
     * yang benar-benar dipakai di form Filament Anda kalau berbeda.
     */
    protected array $educationLevels = [
        'Tidak/Belum Sekolah',
        'SD',
        'SMP',
        'SMA',
        'D3',
        'S1',
        'S2',
        'S3',
    ];

    protected array $occupations = [
        'Belum/Tidak Bekerja',
        'Pelajar/Mahasiswa',
        'Petani',
        'Buruh Harian Lepas',
        'Wiraswasta',
        'Karyawan Swasta',
        'PNS',
        'TNI/Polri',
        'Pedagang',
        'Ibu Rumah Tangga',
        'Guru',
        'Sopir',
    ];

    protected array $religions = [
        'Islam',
        'Kristen',
        'Katolik',
        'Hindu',
        'Buddha',
        'Konghucu',
    ];

    protected array $bloodTypes = ['A', 'B', 'AB', 'O', null];

    public function run(): void
    {
        $this->faker = FakerFactory::create('id_ID');

        $this->createAdminUser();

        $totalRw = 5;
        $rtPerRw = [4, 8];
        $householdsPerRt = [8, 20];

        $this->command?->getOutput()->progressStart($totalRw);

        for ($i = 0; $i < $totalRw; $i++) {
            $rw = Rw::create([
                'number' => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
            ]);

            $rtCount = rand($rtPerRw[0], $rtPerRw[1]);
            $rwResidentIds = [];

            for ($j = 0; $j < $rtCount; $j++) {
                $rt = Rt::create([
                    'rw_id' => $rw->id,
                    'number' => str_pad((string) ($j + 1), 2, '0', STR_PAD_LEFT),
                ]);

                $householdCount = rand($householdsPerRt[0], $householdsPerRt[1]);
                $rtResidentIds = [];

                for ($k = 0; $k < $householdCount; $k++) {
                    $residentIds = $this->createHousehold($rt);
                    $rtResidentIds = array_merge($rtResidentIds, $residentIds);
                }

                // Jadikan salah satu warga di RT ini sebagai ketua RT
                if (! empty($rtResidentIds)) {
                    $rt->update([
                        'chairman_resident_id' => $this->faker->randomElement($rtResidentIds),
                    ]);
                }

                $rwResidentIds = array_merge($rwResidentIds, $rtResidentIds);
            }

            // Jadikan salah satu warga di RW ini sebagai ketua RW
            if (! empty($rwResidentIds)) {
                $rw->update([
                    'chairman_resident_id' => $this->faker->randomElement($rwResidentIds),
                ]);
            }

            $this->command?->getOutput()->progressAdvance();
        }

        $this->command?->getOutput()->progressFinish();

        $this->command?->info('Seeding selesai: '
            . Rw::count() . ' RW, '
            . Rt::count() . ' RT, '
            . Household::count() . ' KK, '
            . Resident::count() . ' warga, '
            . Marriage::count() . ' data perkawinan.');
    }

    /**
     * Buat akun admin untuk login. Sesuaikan email/password kalau perlu,
     * dan sesuaikan nama role kalau Anda pakai Filament Shield / role lain.
     */
    protected function createAdminUser(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Kalau Anda menggunakan Spatie Permission dengan role 'super_admin' atau 'admin',
        // baris ini akan otomatis assign role tersebut. Kalau role belum ada / belum
        // pakai sistem role sama sekali, ini akan diskip diam-diam (try/catch).
        try {
            if (! $admin->hasRole('super_admin') && ! $admin->hasRole('admin')) {
                $admin->assignRole('super_admin');
            }
        } catch (\Throwable $e) {
            $this->command?->warn('Role belum di-assign otomatis (role belum ada di database): ' . $e->getMessage());
        }

        $this->command?->info("Akun admin dibuat -> email: admin@example.com | password: password");
    }

    /**
     * Buat satu Household lengkap dengan anggota keluarganya.
     * Mengembalikan array id resident yang dibuat (untuk dipakai
     * sebagai kandidat ketua RT/RW).
     */
    protected function createHousehold(Rt $rt): array
    {
        $household = Household::create([
            'rt_id' => $rt->id,
            'no_kk' => $this->nextKk(),
            'address' => $this->faker->streetAddress(),
            'pln_customer_number' => $this->faker->boolean(80)
                ? $this->faker->numerify('############')
                : null,
        ]);

        $residentIds = [];

        // --- Kepala Keluarga ---
        $headGender = $this->faker->randomElement(['Laki-laki', 'Laki-laki', 'Laki-laki', 'Perempuan']);
        $headAge = rand(25, 65);

        $head = $this->createResident($household, [
            'gender' => $headGender,
            'relationship_to_head' => 'Kepala Keluarga',
            'marital_status' => 'Kawin',
            'age' => $headAge,
        ]);
        $residentIds[] = $head->id;

        // --- Pasangan (60% kemungkinan ada) ---
        $spouseId = null;

        if ($this->faker->boolean(60)) {
            $spouseGender = $headGender === 'Laki-laki' ? 'Perempuan' : 'Laki-laki';
            $spouseRelationship = $headGender === 'Laki-laki' ? 'Istri' : 'Suami';
            $spouseAge = max(20, $headAge + rand(-5, 5));

            $spouse = $this->createResident($household, [
                'gender' => $spouseGender,
                'relationship_to_head' => $spouseRelationship,
                'marital_status' => 'Kawin',
                'age' => $spouseAge,
            ]);
            $residentIds[] = $spouse->id;
            $spouseId = $spouse->id;

            $this->createMarriage($headGender === 'Laki-laki' ? $head->id : $spouse->id, $headGender === 'Laki-laki' ? $spouse->id : $head->id, $headAge);
        }

        // --- Anak (0-4 orang) ---
        $childrenCount = $spouseId ? rand(0, 4) : rand(0, 2);
        $maxChildAge = max(0, $headAge - 18);

        for ($c = 0; $c < $childrenCount; $c++) {
            $childAge = $maxChildAge > 0 ? rand(0, $maxChildAge) : 0;

            $child = $this->createResident($household, [
                'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
                'relationship_to_head' => 'Anak',
                'marital_status' => $childAge >= 19 ? $this->faker->randomElement(['Kawin', 'Belum Kawin']) : 'Belum Kawin',
                'age' => $childAge,
            ]);
            $residentIds[] = $child->id;
        }

        // --- Famili lain / anggota tambahan (0-1, 20% kemungkinan) ---
        if ($this->faker->boolean(20)) {
            $extra = $this->createResident($household, [
                'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
                'relationship_to_head' => 'Famili Lain',
                'marital_status' => $this->faker->randomElement(['Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati']),
                'age' => rand(1, 80),
            ]);
            $residentIds[] = $extra->id;
        }

        return $residentIds;
    }

    protected function createResident(Household $household, array $overrides): Resident
    {
        $gender = $overrides['gender'];
        $age = $overrides['age'];
        $birthDate = now()->subYears($age)->subDays(rand(0, 364));

        $isMale = $gender === 'Laki-laki';

        return Resident::create([
            'household_id' => $household->id,
            'user_id' => null,
            'nik' => $this->nextNik(),
            'full_name' => $isMale ? $this->faker->name('male') : $this->faker->name('female'),
            'birth_place' => $this->faker->city(),
            'birth_date' => $birthDate,
            'gender' => $gender,
            'blood_type' => $this->faker->randomElement($this->bloodTypes),
            'religion' => $this->faker->randomElement($this->religions),
            'education' => $this->educationForAge($age),
            'occupation' => $age < 18
                ? ($age >= 6 ? 'Pelajar/Mahasiswa' : 'Belum/Tidak Bekerja')
                : $this->faker->randomElement($this->occupations),
            'marital_status' => $overrides['marital_status'],
            'relationship_to_head' => $overrides['relationship_to_head'],
            'father_name' => $this->faker->name('male'),
            'mother_name' => $this->faker->name('female'),
            'birth_cert_number' => $this->faker->boolean(90)
                ? $this->faker->numerify('##-##-##########')
                : null,
            'birth_cert_issuer' => $this->faker->boolean(90)
                ? 'Dinas Kependudukan dan Catatan Sipil ' . $this->faker->city()
                : null,
            'has_ktp' => $age >= 17,
            'status' => 'Aktif',
            'status_date' => null,
            'status_note' => null,
        ]);
    }

    protected function createMarriage(int $husbandId, int $wifeId, int $headAge): void
    {
        $marriedYearsAgo = min($headAge - 18, rand(1, 30));
        $marriageDate = now()->subYears(max(1, $marriedYearsAgo))->subDays(rand(0, 364));

        $isDivorced = $this->faker->boolean(10);

        Marriage::create([
            'husband_resident_id' => $husbandId,
            'wife_resident_id' => $wifeId,
            'marriage_certificate_number' => $this->faker->numerify('MC-####-####'),
            'marriage_date' => $marriageDate,
            'kua_name' => 'KUA Kecamatan ' . $this->faker->city(),
            'divorce_certificate_number' => $isDivorced ? $this->faker->numerify('DC-####-####') : null,
            'divorce_date' => $isDivorced
                ? $marriageDate->copy()->addYears(rand(1, max(1, $marriedYearsAgo - 1)))
                : null,
        ]);
    }

    protected function educationForAge(int $age): string
    {
        if ($age < 6) {
            return 'Tidak/Belum Sekolah';
        }

        if ($age < 12) {
            return $this->faker->randomElement(['Tidak/Belum Sekolah', 'SD']);
        }

        if ($age < 15) {
            return $this->faker->randomElement(['SD', 'SMP']);
        }

        if ($age < 18) {
            return $this->faker->randomElement(['SMP', 'SMA']);
        }

        // Dewasa: distribusi lebih realistis, makin tinggi makin jarang
        return $this->faker->randomElement([
            'Tidak/Belum Sekolah',
            'SD',
            'SD',
            'SD',
            'SMP',
            'SMP',
            'SMP',
            'SMA',
            'SMA',
            'SMA',
            'SMA',
            'SMA',
            'D3',
            'D3',
            'S1',
            'S1',
            'S1',
            'S2',
            'S3',
        ]);
    }

    protected function nextNik(): string
    {
        return (string) $this->nikCounter++;
    }

    protected function nextKk(): string
    {
        return (string) $this->kkCounter++;
    }
}
