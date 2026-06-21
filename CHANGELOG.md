# Changelog

Seluruh perubahan penting pada proyek TEAM 13 E-Healthcare dicatat dalam dokumen ini.

## [1.0.0] — 2026-06-21

### Added

* Tiga microservice Laravel:

  * Data Pasien Service
  * Appointment Service
  * Farmasi dan Obat Service
* API Gateway menggunakan Nginx pada port `8081`.
* Docker Compose untuk menjalankan seluruh service.
* Database SQLite dengan named volume untuk setiap service.
* Middleware API Key pada setiap microservice.
* Integrasi JWT M2M melalui IAE SSO.
* Integrasi SOAP Audit untuk mencatat transaksi.
* Integrasi RabbitMQ Publisher untuk mengirim event.
* Integrasi Appointment Service dengan Data Pasien Service.
* Integrasi Appointment Service dengan Farmasi Service.
* Validasi silang pasien dan appointment pada Farmasi Service.
* Atribut `patient_id` pada data appointment.
* Atribut `patient_id` dan `appointment_id` pada data resep.
* Dokumentasi pembagian kontribusi anggota tim.

### Changed

* API Key setiap service dibaca dari environment Docker.
* Appointment Service menggunakan beberapa PHP CLI server worker.
* Pembuatan appointment memvalidasi pasien sebelum menyimpan data.
* Pembuatan appointment otomatis menjalankan integrasi SSO, SOAP, RabbitMQ, dan Farmasi.
* Farmasi Service memvalidasi pasien dan appointment sebelum menyimpan resep.
* Konfigurasi komunikasi antarmicroservice dipusatkan pada `docker-compose.yml`.

### Fixed

* Memperbaiki API Key Data Pasien menjadi `KEY-MHS-279`.
* Memperbaiki API Key Appointment menjadi `KEY-MHS-390`.
* Memperbaiki API Key Farmasi menjadi `KEY-MHS-157`.
* Memperbaiki validasi `patient_id` dan `appointment_id` pada Farmasi Service.
* Memperbaiki encoding `farmasi-service/routes/console.php`.
* Memperbaiki URL internal antarmicroservice.
* Memperbaiki deadlock ketika Farmasi Service memvalidasi Appointment Service.
* Memperbaiki struktur dan indentasi `docker-compose.yml`.

### Tested

* GET dan POST Data Pasien melalui API Gateway.
* GET dan POST Appointment melalui API Gateway.
* GET data resep melalui API Gateway.
* Integrasi end-to-end Data Pasien, Appointment, dan Farmasi.
* Integrasi JWT M2M, SOAP Audit, dan RabbitMQ.
* API Key salah menghasilkan `401 Unauthorized`.
* JWT salah atau kedaluwarsa menghasilkan `401 Unauthorized`.
* Pasien tidak ditemukan menghasilkan `404`.
* Request tanpa field wajib menghasilkan `422`.
* Appointment tidak dibuat ketika validasi pasien gagal.
* Resep menyimpan `patient_id` dan `appointment_id`.
