# E-Healthcare: Service Farmasi & Obat

Bagian dari ekosistem **E-Healthcare**, service ini menangani pengelolaan resep digital dan distribusi obat dalam alur proses bisnis rawat jalan. Dibangun di atas Laravel dengan REST API dan GraphQL sebagai protokol komunikasi antar service.

## Fitur

- **REST API**: Endpoint terstruktur untuk manajemen resep dan obat
- **GraphQL**: Query fleksibel dengan Lighthouse, cocok untuk integrasi lintas service
- **Swagger UI**: Dokumentasi API interaktif dengan autentikasi API Key
- **GraphiQL**: Playground untuk eksplorasi skema GraphQL secara langsung

## Stack

| | |
|---|---|
| Framework | [Laravel](https://laravel.com/) |
| REST Docs | [L5 Swagger](https://github.com/DarkaOnLine/L5-Swagger) + [swagger-php](https://github.com/zircote/swagger-php) |
| GraphQL Server | [Lighthouse](https://lighthouse-php.com/) |
| GraphQL UI | [Laravel GraphiQL](https://github.com/mll-lab/laravel-graphiql) |
| Database | SQLite |

## Prasyarat

- PHP >= 8.3
- Composer
- Node.js & NPM

## Cara Menjalankan

```bash
# 1. Clone repo
git clone https://github.com/IAE-2026-48-08/102022400084_Farmasi-dan-Obat.git
cd 102022400084_Farmasi-dan-Obat

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi
php artisan migrate

# 5. Jalankan server
composer run dev
```

Aplikasi akan berjalan di `http://localhost:8000`

## Akses Layanan

| Layanan | URL |
|---|---|
| Landing Page | http://localhost:8000 |
| Swagger UI | http://localhost:8000/api/v1/documentation |
| GraphiQL Playground | http://localhost:8000/graphiql |
| GraphQL Endpoint | http://localhost:8000/graphql |

## Endpoint API

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/v1/pharmacy` | Ambil semua data resep |
| POST | `/api/v1/pharmacy` | Tambah resep digital baru |
| GET | `/api/v1/pharmacy/{id}` | Detail resep |
| PUT | `/api/v1/pharmacy/{id}` | Update status resep |
| DELETE | `/api/v1/pharmacy/{id}` | Hapus resep |

## Autentikasi

Semua endpoint memerlukan API Key via header:

```
X-API-KEY: <api-key>
```

Default key (NIM):
```
102022400084
```

Generate key baru:
```bash
php artisan apikey:generate
```

Tambahkan ke `.env`:
```env
API_KEY=hasil_generate_di_sini
```

## Menjalankan dengan Docker

```bash
docker compose up -d

# Akses di http://localhost:8000

docker compose down
```

**Muhammad Fadhlan - 102022400084**
Sistem Informasi · Fakultas Rekayasa Industri · Telkom University
