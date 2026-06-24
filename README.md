# TEAM 13, E-Healthcare Microservices

Proyek E-Healthcare merupakan sistem berbasis arsitektur microservices yang dikembangkan untuk memenuhi Tugas Besar mata kuliah Integrasi Aplikasi Enterprise. Sistem mengintegrasikan pengelolaan data pasien, appointment, serta resep dan obat melalui API Gateway, Docker, JWT M2M, SOAP Audit, dan RabbitMQ Publisher.

## Anggota Tim

- Faiza Clara Vimanda (102022400300): Appointment Service
- Tabitha Glorya Yobelitha Sirait (102022400238): Data Pasien Service
- Muhammad Fadhlan S. (102022400084): Farmasi dan Obat Service

## Arsitektur Sistem

Sistem terdiri dari empat komponen utama.

**API Gateway**
- Menggunakan Nginx
- Menjadi satu-satunya akses dari client
- Berjalan pada port 8081

**Data Pasien Service**
- Mengelola identitas dan informasi pasien
- Memvalidasi pasien yang digunakan pada proses appointment dan farmasi

**Appointment Service**
- Mengelola jadwal konsultasi pasien
- Menjadi pengatur utama alur integrasi end-to-end

**Farmasi dan Obat Service**
- Mengelola resep serta informasi obat
- Menyimpan hubungan resep dengan pasien dan appointment

## Alur Integrasi End-to-End

Ketika client membuat appointment, sistem menjalankan proses berikut:
1. Client mengirim request melalui API Gateway
2. Appointment Service memvalidasi API Key dan JWT
3. Appointment Service memeriksa `patient_id` ke Data Pasien Service
4. Data appointment disimpan apabila pasien ditemukan
5. Appointment Service memperoleh token M2M dari IAE SSO
6. Aktivitas transaksi dicatat melalui SOAP Audit
7. Event `appointment.created` dikirim melalui RabbitMQ Publisher
8. Appointment Service mengirim data ke Farmasi Service
9. Farmasi Service memvalidasi pasien dan appointment
10. Farmasi Service membuat resep standar yang terhubung dengan `patient_id` dan `appointment_id`

## Teknologi

- PHP
- Laravel
- SQLite
- Docker
- Docker Compose
- Nginx API Gateway
- REST API
- JWT M2M
- SOAP
- RabbitMQ Publisher
- Git dan GitHub

## Struktur Repository

```
TEAM-13_E-Healthcare/
  appointment-service/
  data-pasien-service/
  farmasi-service/
  nginx/
    default.conf
  docker-compose.yml
  README.md
  CONTRIBUTIONS.md
  CHANGELOG.md
  PROMPTING_LOG.md
```

## Menjalankan Sistem

Pastikan Docker Desktop sudah aktif, lalu buka terminal pada root repository.

Build dan menjalankan seluruh service:
```
docker compose up -d --build
```

Memeriksa container:
```
docker ps
```

Container yang seharusnya aktif:
- api-gateway
- appointment-service
- data-pasien-service
- farmasi-service

Memeriksa konfigurasi Docker Compose:
```
docker compose config
```

Memeriksa log:
```
docker compose logs --tail=50
```

Menghentikan sistem:
```
docker compose down
```

Jangan menggunakan opsi `-v` apabila data pada volume SQLite ingin dipertahankan.

Catatan: setiap service di compose menjalankan server lewat `php -S 0.0.0.0:8000 public/index.php`, bukan `php artisan serve`. Ini sengaja diganti karena `php artisan serve` ternyata nggak selalu konsisten meneruskan environment variable Docker ke proses yang menangani request HTTP, yang sempat bikin validasi antar service gagal random walau konfigurasinya sudah benar. Detail lengkapnya ada di `PROMPTING_LOG.md` poin 9.

## API Gateway

Base URL seluruh endpoint:
```
http://localhost:8081/api/v1
```

Semua request dilakukan melalui API Gateway. Port internal 8000 hanya digunakan untuk komunikasi antar microservice di dalam Docker network.

## API Key

| Service | Header | Nilai |
|---|---|---|
| Data Pasien | X-IAE-KEY | KEY-MHS-279 |
| Appointment | X-IAE-KEY | KEY-MHS-390 |
| Farmasi | X-IAE-KEY | KEY-MHS-157 |

API Key dibaca dari environment pada `docker-compose.yml`.

## Endpoint Data Pasien

Mengambil seluruh pasien:
```
GET /api/v1/patients
X-IAE-KEY: KEY-MHS-279
```

Mengambil detail pasien:
```
GET /api/v1/patients/{id}
X-IAE-KEY: KEY-MHS-279
```

Menambahkan pasien:
```
POST /api/v1/patients
X-IAE-KEY: KEY-MHS-279
Content-Type: application/json
```

Contoh request:
```json
{
    "nik": "3201010101010002",
    "name": "Siti Aminah",
    "birth_date": "1995-08-20",
    "gender": "Perempuan",
    "address": "Bandung",
    "medical_history": "Tidak ada"
}
```

## Endpoint Appointment

Mengambil seluruh appointment:
```
GET /api/v1/appointments
X-IAE-KEY: KEY-MHS-390
```

Mengambil detail appointment:
```
GET /api/v1/appointments/{id}
X-IAE-KEY: KEY-MHS-390
```

Membuat appointment:
```
POST /api/v1/appointments
X-IAE-KEY: KEY-MHS-390
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json
```

Contoh request:
```json
{
    "patient_id": 1,
    "doctor_name": "dr. Clara",
    "specialization": "Dokter Umum",
    "appointment_date": "2026-06-23",
    "appointment_time": "09:00",
    "status": "scheduled"
}
```

`patient_name` nggak perlu dikirim karena diperoleh dari Data Pasien Service berdasarkan `patient_id`.

## Endpoint Farmasi dan Obat

Mengambil seluruh resep:
```
GET /api/v1/pharmacy
X-IAE-KEY: KEY-MHS-157
```

Mengambil detail resep:
```
GET /api/v1/pharmacy/{id}
X-IAE-KEY: KEY-MHS-157
```

Menambahkan resep:
```
POST /api/v1/pharmacy
X-IAE-KEY: KEY-MHS-157
Content-Type: application/json
```

Contoh request:
```json
{
    "patient_id": "1",
    "appointment_id": "5",
    "medicine_name": "Vitamin C",
    "dosage": "1 tablet",
    "frequency": "1x sehari",
    "quantity": 10,
    "instructions": "Diminum setelah makan",
    "status": "PENDING"
}
```

## Contoh Pengujian PowerShell

GET Data Pasien:
```powershell
Invoke-RestMethod -Uri "http://localhost:8081/api/v1/patients" -Headers @{"X-IAE-KEY"="KEY-MHS-279"}
```

GET Appointment:
```powershell
Invoke-RestMethod -Uri "http://localhost:8081/api/v1/appointments" -Headers @{"X-IAE-KEY"="KEY-MHS-390"}
```

GET Farmasi:
```powershell
Invoke-RestMethod -Uri "http://localhost:8081/api/v1/pharmacy" -Headers @{"X-IAE-KEY"="KEY-MHS-157"}
```

## Keamanan

Sistem menggunakan dua lapisan keamanan.

**API Key**

Setiap microservice memiliki API Key yang berbeda. Request dengan API Key salah atau tanpa API Key menghasilkan `401 Unauthorized`.

**JWT M2M**

Endpoint pembuatan appointment dilindungi oleh JWT yang diperoleh dari layanan IAE SSO. Token dikirim melalui header:
```
Authorization: Bearer {JWT_TOKEN}
```

JWT salah, tidak tersedia, atau kedaluwarsa akan ditolak dengan status `401 Unauthorized`.

## Integrasi Central Infrastructure

**IAE SSO**

Digunakan untuk memperoleh token JWT M2M menggunakan identitas masing-masing service.

**SOAP Audit**

Mencatat transaksi appointment dan resep, lalu menghasilkan receipt number, contohnya `IAE-LOG-2026-693C042D`.

**RabbitMQ Publisher**

Mengirim event dengan konfigurasi:
- Exchange: `iae.central.exchange`
- Routing Key: `appointment.created` (Appointment), `pharmacy.prescription.created` (Farmasi)

## Database dan Persistensi

Setiap service menggunakan database SQLite terpisah dan named volume Docker:
- Appointment: `appointment-data`
- Data Pasien: `patient-data`
- Farmasi: `pharmacy-data`

Data tetap tersedia setelah container dihentikan atau dibangun ulang selama volume nggak dihapus.

## Hasil Pengujian

Pengujian yang telah berhasil dilakukan:
- Build ulang seluruh Docker image
- Menjalankan seluruh container melalui Docker Compose
- Mengakses seluruh service melalui API Gateway
- GET Data Pasien berhasil
- GET Appointment berhasil
- GET Farmasi berhasil
- Appointment memvalidasi pasien sebelum menyimpan data
- Appointment memperoleh token JWT M2M
- SOAP Audit menghasilkan receipt number
- RabbitMQ Publisher mengirim event
- Farmasi menyimpan `patient_id` dan `appointment_id`
- API Key salah menghasilkan `401 Unauthorized`
- JWT salah menghasilkan `401 Unauthorized`
- Pasien tidak ditemukan menghasilkan `404`
- Request tanpa field wajib menghasilkan `422`
- Data SQLite tetap tersedia setelah container dibangun ulang
- Integrasi end-to-end Data Pasien, Appointment, dan Farmasi berhasil, termasuk setelah seluruh container di-rebuild dari awal pakai `--force-recreate`

## Dokumentasi Tambahan

- `CONTRIBUTIONS.md`, pembagian kontribusi anggota
- `CHANGELOG.md`, riwayat perubahan proyek
- `PROMPTING_LOG.md`, dokumentasi penggunaan AI selama pengembangan

## Repository

https://github.com/IAE-2026-48-08/TEAM-13_E-Healthcare
