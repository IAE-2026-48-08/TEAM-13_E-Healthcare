# Changelog

Seluruh perubahan pada proyek TEAM 13 E-Healthcare dicatat dalam dokumen ini.

## Added

- Tiga microservice Laravel:
  1. Data Pasien Service
  2. Appointment Service
  3. Farmasi dan Obat Service
  4. API Gateway menggunakan Nginx pada port `8081`
  5. Docker Compose untuk menjalankan seluruh service
  6. Database SQLite dengan named volume untuk setiap service
  7. Middleware API Key pada setiap microservice
  8. Integrasi JWT M2M melalui IAE SSO
  9. Integrasi SOAP Audit untuk mencatat transaksi
  10. Integrasi RabbitMQ Publisher untuk mengirim event
  11. Integrasi Appointment Service dengan Data Pasien Service
  12. Integrasi Appointment Service dengan Farmasi Service
  13. Validasi pasien dan appointment pada Farmasi Service sebelum resep dibuat
  14. Atribut `patient_id` pada data appointment
  15. Atribut `patient_id` dan `appointment_id` pada data resep
  16. Dokumentasi pembagian kontribusi anggota tim

## Changed

1. API Key seluruh service dipindahkan ke environment Docker
2. Proses pembuatan appointment sekarang melakukan validasi pasien dulu
3. Appointment Service otomatis menjalankan integrasi SSO, SOAP, RabbitMQ, dan Farmasi Service
4. Farmasi Service sekarang memvalidasi data pasien dan appointment sebelum menyimpan resep
5. Konfigurasi komunikasi antar service dipusatkan di `docker-compose.yml`
6. Command server di seluruh service diganti dari `php artisan serve` ke `php -S 0.0.0.0:8000 public/index.php`

## Fixed

1. Memperbaiki API Key untuk seluruh service
2. Memperbaiki validasi `patient_id` pada Appointment Service
3. Memperbaiki validasi `patient_id` dan `appointment_id` pada Farmasi Service
4. Memperbaiki URL komunikasi antar service
5. Memperbaiki masalah `patient_check` dan `appointment_check` yang gagal secara tidak konsisten meskipun environment variable sudah benar. Setelah ditelusuri, masalahnya bukan di kode atau API key, tapi karena `php artisan serve` tidak selalu meneruskan environment variable Docker dengan benar ke proses yang menangani request HTTP (beda dengan proses CLI biasa seperti `php artisan tinker` yang selalu baca env dengan benar). Solusinya ganti command server jadi `php -S 0.0.0.0:8000 public/index.php` di ketiga service, dan setelah itu integrasi jadi konsisten sukses bahkan setelah container di-rebuild ulang
6. Memperbaiki encoding file `farmasi-service/routes/console.php`
7. Merapikan konfigurasi `docker-compose.yml`

## Tested

1. GET dan POST Data Pasien melalui API Gateway
2. GET dan POST Appointment melalui API Gateway
3. GET dan POST Resep melalui API Gateway
4. Validasi API Key berhasil menghasilkan response yang sesuai
5. JWT tidak valid menghasilkan `401 Unauthorized`
6. Data pasien yang tidak ditemukan menghasilkan `404 Not Found`
7. Request tanpa field wajib menghasilkan `422 Unprocessable Entity`
8. Validasi pasien sebelum pembuatan appointment
9. Validasi pasien dan appointment sebelum pembuatan resep
10. Integrasi JWT M2M, SOAP Audit, dan RabbitMQ
11. Integrasi end-to-end antara Data Pasien, Appointment, dan Farmasi Service
12. Pengiriman event ke RabbitMQ berhasil dilakukan
13. SOAP receipt berhasil diterima dari layanan audit
14. Seluruh integrasi (SSO, SOAP, RabbitMQ, patient_check, appointment_check) tetap sukses setelah seluruh container di-rebuild dari awal dengan `--force-recreate`, membuktikan fix command server sudah stabil
