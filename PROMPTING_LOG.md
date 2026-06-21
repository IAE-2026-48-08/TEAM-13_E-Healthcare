# Prompting Log

Dokumen ini mencatat penggunaan AI sebagai alat bantu selama pengembangan proyek **TEAM 13 E-Healthcare**. AI digunakan untuk membantu analisis error, penyusunan langkah pengujian, perbaikan konfigurasi, dan dokumentasi. Seluruh kode dan hasil tetap diperiksa serta diuji oleh anggota tim.

## 1. Pemeriksaan Struktur Microservice

**Prompt:**

> Periksa struktur proyek E-Healthcare yang terdiri dari Appointment Service, Data Pasien Service, Farmasi Service, Docker Compose, dan API Gateway. Jelaskan bagian yang belum sesuai dengan kebutuhan integrasi end-to-end.

**Hasil yang digunakan:**

* Memeriksa struktur folder masing-masing Laravel service.
* Memastikan setiap service memiliki endpoint API.
* Memastikan ketiga service berada pada Docker network yang sama.
* Memastikan akses client dilakukan melalui API Gateway.

**Validasi manual:**

* Menjalankan `docker compose config`.
* Menjalankan `docker ps`.
* Menguji setiap endpoint melalui port API Gateway `8081`.

---

## 2. Perbaikan API Key Appointment Service

**Prompt:**

> Periksa middleware API Key Appointment Service dan sesuaikan agar menggunakan API Key Clara, yaitu KEY-MHS-390, dari environment Docker.

**Hasil yang digunakan:**

* Middleware membaca nilai dari `IAE_API_KEY`.
* Default API Key disesuaikan menjadi `KEY-MHS-390`.
* Konfigurasi ditambahkan ke `docker-compose.yml`.

**Validasi manual:**

* Request dengan API Key benar berhasil.
* Request dengan `KEY-SALAH` menghasilkan `401 Unauthorized`.

---

## 3. Integrasi Appointment dengan Data Pasien

**Prompt:**

> Tambahkan patient_id pada Appointment Service dan validasi patient_id ke Data Pasien Service sebelum appointment disimpan.

**Hasil yang digunakan:**

* Menambahkan migration kolom `patient_id`.
* Menambahkan `patient_id` ke model Appointment.
* Appointment Service memanggil endpoint detail pasien.
* `patient_name` diambil dari Data Pasien Service.
* Appointment ditolak ketika pasien tidak ditemukan.

**Validasi manual:**

* `patient_id: 1` berhasil mengambil pasien Budi Santoso.
* `patient_id: 999999` menghasilkan status `404`.
* ID appointment tidak bertambah ketika validasi pasien gagal.

---

## 4. Integrasi JWT M2M SSO

**Prompt:**

> Implementasikan JWT M2M pada proses POST Appointment menggunakan endpoint IAE SSO, API Key KEY-MHS-390, dan NIM 102022400300.

**Hasil yang digunakan:**

* Token diperoleh dari IAE SSO.
* Token dikirim melalui header `Authorization: Bearer`.
* Appointment Service menolak JWT yang salah atau kedaluwarsa.

**Validasi manual:**

* Token valid memiliki panjang sekitar 762 karakter.
* Token valid menghasilkan integrasi M2M berstatus sukses.
* `Bearer TOKEN-SALAH` menghasilkan `401 Unauthorized`.

---

## 5. Integrasi SOAP Audit

**Prompt:**

> Setelah appointment tersimpan dan token M2M berhasil diperoleh, panggil SOAP Audit dan simpan receipt number pada data appointment.

**Hasil yang digunakan:**

* SOAP Audit dipanggil pada alur pembuatan appointment.
* Response SOAP disimpan pada `soap_audit_response`.
* Receipt number disimpan pada `soap_receipt_number`.

**Validasi manual:**

* SOAP menghasilkan status sukses.
* Receipt number seperti `IAE-LOG-2026-693C042D` tersimpan pada appointment.

---

## 6. Integrasi RabbitMQ Publisher

**Prompt:**

> Setelah SOAP Audit berhasil, publikasikan event appointment.created melalui RabbitMQ Publisher.

**Hasil yang digunakan:**

* Event menggunakan exchange `iae.central.exchange`.
* Routing key menggunakan `appointment.created`.
* Publikasi dilakukan setelah SOAP Audit berhasil.

**Validasi manual:**

Response integrasi menunjukkan:

```json
{
  "status": "success",
  "exchange": "iae.central.exchange",
  "routing_key": "appointment.created"
}
```

---

## 7. Integrasi Appointment dengan Farmasi

**Prompt:**

> Setelah appointment dibuat, kirim patient_id dan appointment_id ke Farmasi Service agar resep standar dapat dibuat.

**Hasil yang digunakan:**

* Appointment Service memanggil Farmasi Service.
* Farmasi menerima `patient_id` dan `appointment_id`.
* Resep standar Vitamin C dibuat secara otomatis.
* Data resep memiliki hubungan dengan pasien dan appointment.

**Validasi manual:**

Resep berhasil tersimpan dengan data:

```text
patient_id: 1
appointment_id: 5
medicine_name: Konsultasi Standar (Vitamin C)
status: PENDING
```

---

## 8. Perbaikan Konfigurasi Farmasi

**Prompt:**

> Farmasi gagal menghubungi Data Pasien dan Appointment Service. Periksa environment variable, URL internal Docker, dan API Key yang digunakan.

**Hasil yang digunakan:**

Environment Farmasi ditambahkan:

```text
DATA_PASIEN_SERVICE_URL=http://data-pasien-service:8000
DATA_PASIEN_API_KEY=KEY-MHS-279
APPOINTMENT_SERVICE_URL=http://appointment-service:8000
APPOINTMENT_API_KEY=KEY-MHS-390
```

**Validasi manual:**

* Farmasi berhasil mengakses Data Pasien Service.
* Farmasi berhasil mengakses Appointment Service.
* `patient_check` dan `appointment_check` menghasilkan status sukses.

---

## 9. Penyelesaian Deadlock Appointment dan Farmasi

**Prompt:**

> Appointment Service memanggil Farmasi Service, tetapi Farmasi melakukan request kembali ke Appointment Service dan request gagal. Analisis penyebabnya.

**Hasil analisis:**

Terjadi request saling menunggu karena `php artisan serve` hanya menjalankan satu worker.

**Perbaikan yang digunakan:**

```yaml
PHP_CLI_SERVER_WORKERS: "4"
```

Perintah server Appointment diubah menjadi:

```text
php artisan serve --host=0.0.0.0 --port=8000 --no-reload
```

**Validasi manual:**

* Peringatan single server worker hilang.
* Farmasi berhasil memvalidasi Appointment Service.
* Integrasi end-to-end berhasil penuh.

---

## 10. Perbaikan Docker Compose

**Prompt:**

> Perbaiki struktur dan indentasi docker-compose.yml agar seluruh service, environment, volume, network, dan command terbaca dengan benar.

**Hasil yang digunakan:**

* Memperbaiki indentasi YAML.
* Memisahkan konfigurasi environment setiap service.
* Mempertahankan named volume.
* Menambahkan URL dan API Key internal.
* Menambahkan worker Appointment Service.

**Validasi manual:**

```text
docker compose config
```

berhasil tanpa error.

---

## 11. Pemisahan Commit Anggota

**Prompt:**

> Pisahkan commit berdasarkan tanggung jawab Clara, Tabitha, dan Fadhlan agar kontribusi GitHub terlihat jelas.

**Hasil yang digunakan:**

Commit dipisahkan menjadi:

* `02c13bf` — Clara: integrasi Appointment end-to-end.
* `93447ef` — Tabitha: API Key Data Pasien.
* `993f45b` — Fadhlan: API Key dan validasi Farmasi.

**Validasi manual:**

* Identitas Git diatur sesuai anggota saat commit.
* `git status --short` bersih.
* Branch lokal sinkron dengan `origin/main`.

---

## 12. Penyusunan Dokumentasi

**Prompt:**

> Buat dokumentasi root repository yang menjelaskan arsitektur, endpoint, integrasi, pengujian, kontribusi anggota, changelog, dan penggunaan AI.

**Hasil yang digunakan:**

Dokumentasi yang dibuat:

* `README.md`
* `CONTRIBUTIONS.md`
* `CHANGELOG.md`
* `PROMPTING_LOG.md`

## Catatan Penggunaan AI

AI digunakan sebagai pendamping teknis, bukan sebagai pengganti proses pengembangan. Setiap saran AI diperiksa melalui:

* Pemeriksaan kode.
* PHP syntax check.
* Docker Compose validation.
* Pengujian endpoint.
* Pemeriksaan response HTTP.
* Pemeriksaan data pada setiap microservice.
* Pemeriksaan riwayat Git dan kontribusi anggota.
