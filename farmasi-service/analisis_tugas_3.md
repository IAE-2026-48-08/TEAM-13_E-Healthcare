# Analisis Tugas 3 - Service Farmasi & Obat

Nama: Muhammad Fadhlan
NIM: 102022400084
Service: E-Healthcare Farmasi & Obat

## Transaksi kritis yang dipilih

Saya pilih transaksi pembuatan resep digital, endpoint `POST /api/v1/pharmacy`.

Kenapa ini yg saya anggap kritis: karena ini transaksi yang ngubah state, bukan cuma baca data doang. Begitu resep dibuat, itu jadi acuan buat petugas farmasi nyiapin obat, jadi kalau ada kesalahan di sini efeknya nyambung ke proses berikutnya (status obat, stok, dll). Selain itu data yang masuk juga data medis (nama obat, dosis, jumlah) yang menurut saya emang seharusnya diaudit, bukan transaksi yang sifatnya cuma nampilin data aja.

## Alur ke layanan terpusat

Pas ada request POST resep baru, urutannya gini:

1. Resep disimpan dulu ke db lokal (sqlite)
2. Service saya login ke SSO dosen pakai API Key (M2M) buat dapet JWT
3. JWT itu dipakai buat kirim audit ke endpoint SOAP dosen (`/soap/v1/audit`), isinya TeamID, ActivityName (PrescriptionCreated), sama data resepnya dalam json
4. Kalau SOAP nya sukses, dapet ReceiptNumber dari dosen
5. Terakhir publish event ke RabbitMQ dosen lewat endpoint `/api/v1/messages/publish`, biar service2 lain (kalau ada) bisa dengar ada resep baru

```
Client -> Farmasi Service -> SSO Dosen (login, dapet token)
                           -> SOAP Audit (kirim pakai token, dapet receipt)
                           -> RabbitMQ (publish event pakai token)
       <- response balik ke client (data resep + status integrasi)
```

## Catatan implementasi

- SSO pakai M2M API Key `KEY-MHS-157`
- TeamID nya `TEAM-13` (sempet salah nulis TEAM-08 di awal, ternyata dari response sso ketauan team yg bener)
- SOAP dan RabbitMQ ternyata dua²nya emang harus pakai token M2M, bukan token user/warga. Awalnya saya kira bebas pakai salah satu, taunya beda kebutuhan.

Response akhirnya kalau berhasil semua:
```json
"integration": {
    "sso": "success",
    "soap": "success",
    "rabbitmq": "success",
    "soap_receipt": "IAE-LOG-2026-54DAC4D3"
}
```
