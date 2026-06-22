# Kontribusi Anggota Tim

Dokumen ini menjelaskan pembagian tanggung jawab dan kontribusi setiap anggota pada proyek **TEAM 13 E-Healthcare**.

1. Faiza Clara Vimanda (102022400300): Appointment Service

Kontribusi:

- Mengembangkan dan menyempurnakan Appointment Service.
- Mengimplementasikan keamanan API Key `KEY-MHS-390`.
- Mengintegrasikan Appointment Service dengan Data Pasien Service.
- Menambahkan atribut `patient_id` pada data appointment.
- Mengintegrasikan Appointment Service dengan Farmasi dan Obat Service.
- Mengimplementasikan autentikasi JWT M2M melalui IAE SSO.
- Mengimplementasikan pencatatan transaksi melalui SOAP Audit.
- Mengimplementasikan publikasi event melalui RabbitMQ Publisher.
- Mengatur API Gateway menggunakan Nginx.
- Mengatur Docker Compose dan komunikasi internal antarmicroservice.
- Menambahkan beberapa PHP CLI server worker untuk mencegah deadlock pada validasi silang antarlayanan.

Commit utama: `be0343d` — Secure JWT SSO and integrate healthcare services, `02c13bf` — Complete patient and pharmacy end-to-end integration.

2. Tabitha Glorya Yobelitha Sirait (102022400238): Data Pasien Service

Kontribusi:

- Mengembangkan Data Pasien Service.
- Menyediakan endpoint untuk menampilkan daftar pasien.
- Menyediakan endpoint untuk menampilkan detail pasien.
- Menyediakan endpoint untuk menambahkan data pasien.
- Menyediakan data pasien yang digunakan dalam validasi appointment.
- Mengubah API Key agar dibaca dari environment Docker.
- Menggunakan API Key Data Pasien `KEY-MHS-279`.
- Mendukung integrasi Appointment Service dengan Data Pasien Service.

Commit utama: `93447ef` — Use service API key from Docker environment.

3. Muhammad Fadhlan S. (102022400084): Farmasi dan Obat Service

Kontribusi:

- Mengembangkan Farmasi dan Obat Service.
- Menyediakan endpoint untuk menampilkan dan menambahkan data resep.
- Mengimplementasikan validasi pasien ke Data Pasien Service.
- Mengimplementasikan validasi appointment ke Appointment Service.
- Mengubah API Key Farmasi agar dibaca dari environment Docker.
- Menggunakan API Key Farmasi `KEY-MHS-157`.
- Menambahkan validasi field `patient_id` dan `appointment_id`.
- Memperbaiki format encoding file `routes/console.php`.
- Mendukung integrasi SOAP Audit dan RabbitMQ pada proses pembuatan resep.

Commit utama: `993f45b` — Secure API key and validate integration fields.

## Kontribusi Bersama

Seluruh anggota berkontribusi dalam:

- Pengujian komunikasi antarmicroservice.
- Pengujian endpoint melalui API Gateway.
- Pemeriksaan struktur response JSON.
- Validasi API Key masing-masing service.
- Verifikasi persistensi database SQLite.
- Penyelesaian alur bisnis E-Healthcare dari data pasien, appointment, hingga resep obat.

## Ringkasan Alur Integrasi

Alur integrasi yang berhasil diuji:

1. Data pasien dibuat dan disimpan pada Data Pasien Service.
2. Appointment Service memvalidasi data pasien.
3. Appointment Service memperoleh token JWT M2M.
4. Transaksi appointment dicatat melalui SOAP Audit.
5. Event `appointment.created` dipublikasikan melalui RabbitMQ.
6. Appointment Service mengirim data pasien dan appointment ke Farmasi Service.
7. Farmasi Service memvalidasi pasien dan appointment.
8. Resep obat berhasil dibuat dan disimpan.
