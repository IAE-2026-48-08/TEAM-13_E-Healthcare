<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Seed data appointment dummy untuk keperluan testing dan demo integrasi.
     * Aman dijalankan berkali-kali (idempotent) — cek dulu berdasarkan
     * kombinasi pasien + tanggal + jam sebelum membuat baru.
     */
    public function run(): void
    {
        $appointments = [
            [
                'patient_name'     => 'Budi Santoso',
                'doctor_name'      => 'dr. Clara',
                'specialization'   => 'Dokter Umum',
                'appointment_date' => '2026-06-25',
                'appointment_time' => '10:30',
                'status'           => 'scheduled',
            ],
            [
                'patient_name'     => 'Siti Aminah',
                'doctor_name'      => 'dr. Andi',
                'specialization'   => 'Penyakit Dalam',
                'appointment_date' => '2026-06-26',
                'appointment_time' => '14:00',
                'status'           => 'scheduled',
            ],
        ];

        foreach ($appointments as $appointment) {
            Appointment::firstOrCreate(
                [
                    'patient_name'     => $appointment['patient_name'],
                    'appointment_date' => $appointment['appointment_date'],
                    'appointment_time' => $appointment['appointment_time'],
                ],
                $appointment
            );
        }
    }
}
