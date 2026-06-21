# Changelog

Seluruh perubahan pada proyek TEAM 13 E-Healthcare dicatat dalam dokumen ini.

- Added

* Tiga microservice Laravel:
1. Data Pasien Service
2. Appointment Service
3. Farmasi dan Obat Service

1. API Gateway menggunakan Nginx pada port `8081`.
2. Docker Compose untuk menjalankan seluruh service.
3. Database SQLite dengan nama volume untuk setiap service.
4. Middleware API Key pada setiap microservice.
5. Integrasi JWT M2M melalui IAE SSO.
6. Integrasi SOAP Audit untuk mencatat transaksi.
7. Integrasi RabbitMQ Publisher untuk mengirim event.
8. Integrasi Appointment Service dengan Data Pasien Service.
9. Integrasi Appointment Service dengan Farmasi Service.
10. Validasi antar pasien dan appointment pada Farmasi Service.
11. Atribut `patient_id` pada data appointment.
12. Atribut `patient_id` dan `appointment_id` pada data resep.
13. Dokumentasi pembagian kontribusi anggota tim.

- Changed

1. API Key seluruh service dipindahkan ke environment Docker.
2. Proses pembuatan appointment sekarang melakukan validasi pasien terlebih dahulu.
3. Appointment Service otomatis menjalankan integrasi SSO, SOAP, RabbitMQ, dan Farmasi Service.
4. Farmasi Service sekarang memvalidasi data pasien dan appointment sebelum menyimpan resep.
5. Konfigurasi komunikasi antar service dipusatkan di `docker-compose.yml`.

- Fixed

1. Memperbaiki API Key untuk seluruh service.
2. Memperbaiki validasi `patient_id` pada Appointment Service.
3. Memperbaiki validasi `patient_id` dan `appointment_id` pada Farmasi Service.
4. Memperbaiki URL komunikasi antar service.
5. Memperbaiki deadlock saat Farmasi Service melakukan validasi appointment.
6. Memperbaiki encoding file `farmasi-service/routes/console.php`.
7. Merapikan konfigurasi `docker-compose.yml`.

- Tested

1. GET dan POST Data Pasien melalui API Gateway.
2. GET dan POST Appointment melalui API Gateway.
3. GET dan POST Resep melalui API Gateway.
4. Validasi API Key berhasil menghasilkan response yang sesuai.
5. JWT tidak valid menghasilkan `401 Unauthorized`.
6. Data pasien yang tidak ditemukan menghasilkan `404 Not Found`.
7. Request tanpa field wajib menghasilkan `422 Unprocessable Entity`.
8. Validasi pasien sebelum pembuatan appointment.
9. Validasi pasien dan appointment sebelum pembuatan resep.
10. Integrasi JWT M2M, SOAP Audit, dan RabbitMQ.
11. Integrasi end-to-end antara Data Pasien, Appointment, dan Farmasi Service.
12. Pengiriman event ke RabbitMQ berhasil dilakukan.
13. SOAP receipt berhasil diterima dari layanan audit.