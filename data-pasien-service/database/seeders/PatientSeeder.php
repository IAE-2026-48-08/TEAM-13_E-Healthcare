<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Seed data pasien dummy untuk keperluan testing dan demo integrasi.
     * Aman dijalankan berkali-kali (idempotent) — cek dulu apakah data
     * dengan NIK yang sama sudah ada sebelum membuat baru.
     */
    public function run(): void
    {
        $patients = [
            [
                'nik'             => '3201234567890001',
                'name'            => 'Budi Santoso',
                'birth_date'      => '1990-05-15',
                'gender'          => 'Laki-laki',
                'address'         => 'Jl. Merdeka No. 1, Bandung',
                'medical_history' => 'Tidak ada riwayat alergi',
            ],
            [
                'nik'             => '3201234567890002',
                'name'            => 'Siti Aminah',
                'birth_date'      => '1985-08-20',
                'gender'          => 'Perempuan',
                'address'         => 'Jl. Asia Afrika No. 10, Bandung',
                'medical_history' => 'Riwayat hipertensi',
            ],
        ];

        foreach ($patients as $patient) {
            Patient::firstOrCreate(
                ['nik' => $patient['nik']],
                $patient
            );
        }
    }
}
