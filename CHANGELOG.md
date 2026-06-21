# Changelog

Seluruh perubahan penting pada proyek TEAM 13 E-Healthcare dicatat dalam dokumen ini.

## [1.0.0] - 2026-06-21

### Added

- Menambahkan Data Pasien Service.
- Menambahkan Appointment Service.
- Menambahkan Farmasi dan Obat Service.
- Menambahkan API Gateway menggunakan Nginx pada port `8081`.
- Menambahkan Docker Compose untuk menjalankan seluruh service.
- Menambahkan database SQLite terpisah untuk setiap service.
- Menambahkan middleware API Key pada setiap microservice.
- Menambahkan integrasi JWT M2M melalui IAE SSO.
- Menambahkan integrasi SOAP Audit.
- Menambahkan RabbitMQ Publisher.
- Menambahkan komunikasi antar service untuk validasi data pasien dan appointment.
- Menambahkan atribut `patient_id` pada appointment.
- Menambahkan atribut `patient_id` dan `appointment_id` pada data resep.
- Menambahkan dokumentasi kontribusi anggota tim.

### Changed

- API Key seluruh service dipindahkan ke environment Docker.
- Proses pembuatan appointment sekarang melakukan validasi pasien terlebih dahulu.
- Appointment Service otomatis menjalankan integrasi SSO, SOAP, RabbitMQ, dan Farmasi Service.
- Farmasi Service sekarang memvalidasi data pasien dan appointment sebelum menyimpan resep.
- Konfigurasi komunikasi antar service dipusatkan di `docker-compose.yml`.

### Fixed

- Memperbaiki API Key untuk seluruh service.
- Memperbaiki validasi `patient_id` pada Appointment Service.
- Memperbaiki validasi `patient_id` dan `appointment_id` pada Farmasi Service.
- Memperbaiki URL komunikasi antar service.
- Memperbaiki deadlock saat Farmasi Service melakukan validasi appointment.
- Memperbaiki encoding file `farmasi-service/routes/console.php`.
- Merapikan konfigurasi `docker-compose.yml`.

### Tested

- GET dan POST Data Pasien melalui API Gateway.
- GET dan POST Appointment melalui API Gateway.
- GET dan POST Resep melalui API Gateway.
- Validasi API Key berhasil menghasilkan response yang sesuai.
- JWT tidak valid menghasilkan `401 Unauthorized`.
- Data pasien yang tidak ditemukan menghasilkan `404 Not Found`.
- Request tanpa field wajib menghasilkan `422 Unprocessable Entity`.
- Validasi pasien sebelum pembuatan appointment.
- Validasi pasien dan appointment sebelum pembuatan resep.
- Integrasi JWT M2M, SOAP Audit, dan RabbitMQ.
- Integrasi end-to-end antara Data Pasien, Appointment, dan Farmasi Service.
- Pengiriman event ke RabbitMQ berhasil dilakukan.
- SOAP receipt berhasil diterima dari layanan audit.

### Deployment

- Seluruh service berhasil dijalankan menggunakan Docker Compose.
- API Gateway berhasil menghubungkan seluruh microservice.
- Komunikasi antar container berjalan dengan baik.