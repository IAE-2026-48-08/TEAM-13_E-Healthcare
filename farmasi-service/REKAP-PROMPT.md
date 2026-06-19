# Rekap Prompt sama AI

Nama: Muhammad Fadhlan
NIM: 102022400084

Ini rekap obrolan saya sama AI (Claude) selama ngerjain tugas 2 sama 3, service Farmasi & Obat.

## Tugas 2

Awal-awal saya bingung gimana cara jalanin project laravel di vscode soalnya saya pakai template dari punya temen kelompok (rent-contract punya akhdan), jadi banyak yg harus disesuaiin lagi.

Sempet error php versinya kurang tinggi (masih 8.2 padahal butuh 8.3+), jadi upgrade dulu ke 8.5. Terus error lagi pas composer install karena beberapa extension php nya belum aktif (openssl, fileinfo, zip, pdo_sqlite). Beresin satu2 di php.ini.

Setelah itu baru mulai bikin controller, model, migration buat tabel pharmacy nya. Awalnya masih nyambung sama tabel patient & appointment (karena ngikutin contoh), tapi terus saya putuskan service saya fokus ke farmasi aja jadi tabel patient & appointment nya dihapus.

Pas dihapus malah jadi banyak error baru, soalnya pharmacy controller nya masih nyari PatientResource yg udah ga ada, terus migration pharmacy nya masih ada foreign key ke patients. Beresin satu2.

Swagger nya juga pas pertama generate masih error "Required @OA\Info() not found" gara2 ada 2 anotasi info bertabrakan, jadi salah satunya saya hapus.

Sempet juga minta tampilan swagger nya diganti soalnya defaultnya keliatan plain banget, jadi dikasih dark theme.

## Masalah paling pusing: GraphQL

Pas test graphql nya, error mulu, pesannya "did not find graphql schema import at .../patient.graphql". Ternyata schema.graphql nya masih nyoba import patient & appointment yg udah saya hapus filenya. Setelah schema nya dibenerin biar cuma import pharmacy doang, baru jalan normal.

## Tugas 3

Buat sso sama soap nya lumayan smooth, cuma pas awal salah nulis teamid (saya tulis TEAM-08 padahal harusnya TEAM-13, baru ketauan pas liat response sso nya ada info team disitu).

## Masalah paling lama: RabbitMQ

Ini paling lama selesainya. Awal saya coba connect langsung pakai php-amqplib ke host rabbitmq nya, eh malah error "ACCESS_REFUSED - login was refused". Udah coba ganti2 username password masih gabisa.

Terus saya coba juga pakai token warga (login email+password) buat soap sama rabbitmq, eh malah soap nya yg jadi error 403. Bingung banget di sini.

Akhirnya pas saya cek log laravel nya ketemu pesan "Forbidden: M2M Bearer token required" buat rabbitmq nya. Jadi ternyata rabbitmq dosen itu ga dipanggil langsung pakai protokol amqp, tapi lewat http api, dan emang khusus butuh token m2m bukan token warga. Setelah diganti caranya jadi lewat http endpoint /api/v1/messages/publish pakai token m2m, langsung sukses.

## Hasil akhir

Sekarang kalau bikin resep baru lewat post /api/v1/pharmacy, otomatis ke trigger sso -> soap -> rabbitmq, dan semuanya udah kecek manual juga lewat postman, hasilnya sukses semua.
