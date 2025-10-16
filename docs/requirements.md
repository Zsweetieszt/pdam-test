# SISTEM PENAGIHAN PDAM SEDERHANA
## Dokumentasi Kebutuhan Sistem

---

---

## 1. Batasan (Constraints)
*(Berlaku untuk sistem secara keseluruhan)*

 [C-1]  Aplikasi tidak memproses transaksi keuangan secara langsung, hanya mencatat dan melacak pembayaran.
 [C-2]  Hanya Admin yang dapat melakukan manajemen pengguna dan verifikasi data pelanggan.
 [C-3]  Hanya Admin yang dapat mengelola data master seperti tarif air dan template notifikasi.
 [C-4]  Hanya Admin yang dapat mengelola konten global (FAQ, Pengaturan Umum, Template WhatsApp).
 [C-5]  Hanya Admin yang dapat meninjau dan mengelola audit logs sistem
 [C-6]  Verifikasi data pelanggan wajib menggunakan dokumen KTP dan data meter air.
 [C-7]  Notifikasi dilakukan via link WhatsApp yang dapat di-copy atau di-klik untuk membuka WhatsApp
 [C-8]  Empat peran pengguna: Admin, Keuangan, Customer, dan Manajemen
 [C-9]  Notifikasi tagihan hanya dapat digenerate pada tagihan dengan status 'pending' atau 'overdue'.
 [C-10]  Tagihan yang sudah dibayar tidak dapat diubah status pembayarannya.
 [C-11]  Kalkulasi tagihan berdasarkan pemakaian m3 dan tarif yang berlaku.
 [C-12]  Database: MariaDB.
 [C-13]  Autentikasi: Laravel Session dengan middleware authentication.
 [C-14]  Aturan password: minimum 8 karakter, kombinasi huruf besar, kecil, dan angka.
 [C-15]  Nomor telepon harus unik dalam sistem dan digunakan untuk autentikasi.
 [C-16]  Avatar menggunakan inisial nama atau foto yang diupload.
 [C-17]  Nama file unggahan di-hash atau di-generate secara acak untuk keamanan.
 [C-18]  Sesi otomatis berakhir setelah 2 jam tidak aktif.
 [C-19]  Notifikasi tagihan hanya dapat digenerate oleh role Keuangan.
 [C-20]  Role Manajemen hanya dapat melihat laporan dan analisis, tidak dapat melakukan transaksi.

---

## 2. Asumsi (Assumptions)
*(Berlaku untuk sistem secara keseluruhan)*

 [A-1]  Password di-hash menggunakan metode bcrypt untuk keamanan.
 [A-2]  created_at dan updated_at dikelola otomatis oleh Eloquent ORM Laravel.
 [A-3]  Penyimpanan file menggunakan Laravel Storage dengan path yang di-hash.
 [A-4]  Pembacaan meter dilakukan secara manual dan diinput ke sistem.
 [A-5]  Tarif air berubah berdasarkan kebijakan PDAM dan diupdate oleh Admin.
 [A-6]  Sistem dapat menangani multiple periode tagihan secara bersamaan
 [A-7]  Backup database dilakukan secara manual oleh Admin.
 [A-8]  Penyimpanan file menggunakan Laravel Storage dengan path yang di-hash.
 [A-9]  Pembacaan meter dilakukan secara manual dan diinput ke sistem.
 [A-10]  Tarif air berubah berdasarkan kebijakan PDAM dan diupdate oleh Admin.
 [A-11]  Sistem dapat menangani multiple periode tagihan secara bersamaan.
 [A-12]  Backup database dilakukan secara manual oleh Admin.

---

## 3. Kebutuhan Fungsional (Functional Requirements)

 3.1 Kebutuhan Front-end (Client-Side)

  [REQ-F-1] Antarmuka Pengguna dan Autentikasi 
 [REQ-F-1.1]  Aplikasi dapat menampilkan form login dengan validasi input di sisi klien untuk nomor telepon dan password. 
*Cek: [C-15], [C-16]*
 [REQ-F-1.2]  Aplikasi dapat menampilkan form registrasi pelanggan baru dengan validasi data KTP dan informasi meter.
*Cek: [C-6]*
 [REQ-F-1.3]  Aplikasi dapat menampilkan pesan error yang diterima dari back-end jika autentikasi gagal.
 [REQ-F-1.4]  Aplikasi dapat menampilkan antarmuka untuk proses reset password melalui verifikasi WhatsApp.
  [REQ-F-2] Antarmuka Manajemen Profil Pengguna 
 [REQ-F-2.1]  Aplikasi dapat menampilkan halaman profil pengguna dengan foto yang diambil dari Gravatar.
*Cek: [C-17]*
 [REQ-F-2.2]  Aplikasi dapat menyediakan form edit profil untuk mengubah informasi personal dan kontak.
 [REQ-F-2.3]  Aplikasi dapat menampilkan riwayat aktivitas pengguna dalam sistem.
  [REQ-F-3] Antarmuka Manajemen Data Pelanggan 
 [REQ-F-3.1]  Aplikasi dapat menampilkan daftar pelanggan dengan fitur pencarian berdasarkan nomor pelanggan, nama, atau nomor meter.
 [REQ-F-3.2]  Aplikasi dapat menampilkan form untuk menambah/mengedit data pelanggan baru beserta informasi meter air.
 [REQ-F-3.3]  Aplikasi dapat menampilkan detail profil pelanggan lengkap dengan riwayat tagihan dan pembayaran.
  [REQ-F-4] Antarmuka Manajemen Tagihan 
 [REQ-F-4.1]  Aplikasi dapat menampilkan halaman daftar tagihan dengan filter berdasarkan periode, status, dan pelanggan.
 [REQ-F-4.2]  Aplikasi dapat menampilkan form input pembacaan meter untuk generate tagihan baru.
 [REQ-F-4.3]  Aplikasi dapat menampilkan detail tagihan lengkap dengan breakdown biaya dan riwayat pembayaran.
 [REQ-F-4.4]  Aplikasi dapat menampilkan tombol aksi untuk mengubah status tagihan (pending, sent, paid, overdue, cancelled).
  [REQ-F-5] Antarmuka Link WhatsApp 
 [REQ-F-5.1]  Aplikasi dapat menampilkan tombol "Generate Link WhatsApp" pada setiap tagihan untuk role Keuangan.
*Cek: [C-19]*
 [REQ-F-5.2]  Aplikasi dapat menampilkan modal dengan link WhatsApp dan tombol copy/buka WhatsApp.
 [REQ-F-5.3]  Aplikasi dapat menampilkan template pesan tagihan yang dapat di-customize.
  [REQ-F-6] Antarmuka Manajemen Pembayaran 
 [REQ-F-6.1]  Aplikasi dapat menampilkan form input pembayaran dengan validasi jumlah dan metode pembayaran.
 [REQ-F-6.2]  Aplikasi dapat menampilkan konfirmasi pembayaran sebelum menyimpan data.
 [REQ-F-6.3]  Aplikasi dapat menampilkan riwayat pembayaran pelanggan dengan detail transaksi.
  [REQ-F-7] Antarmuka Laporan dan Dashboard 
 [REQ-F-7.1]  Aplikasi dapat menampilkan dashboard dengan statistik tagihan, pembayaran, dan tunggakan.
 [REQ-F-7.2]  Aplikasi dapat menampilkan grafik pemakaian air dan trend pembayaran.
 [REQ-F-7.3]  Aplikasi dapat menampilkan laporan dalam bentuk tabel yang dapat diekspor ke PDF atau Excel.
  [REQ-F-8] Antarmuka Manajemen Template 
 [REQ-F-8.1]  Aplikasi dapat menampilkan halaman CRUD untuk template notifikasi WhatsApp.
*Cek: [C-4]*
 [REQ-F-8.2]  Aplikasi dapat menyediakan editor template dengan variabel dinamis (customer_name, amount, due_date, dll).
 [REQ-F-8.3]  Aplikasi dapat menampilkan preview template dengan data sample.
  [REQ-F-9] Antarmuka Manajemen Admin 
 [REQ-F-9.1]  Aplikasi dapat menampilkan dashboard admin dengan visualisasi data sistem dan aktivitas pengguna.
 [REQ-F-9.2]  Aplikasi dapat menampilkan halaman CRUD untuk manajemen pengguna dan role.
*Cek: [C-2]*
 [REQ-F-9.3]  Aplikasi dapat menampilkan audit logs dengan filter berdasarkan pengguna, aksi, dan tanggal.
  [REQ-F-10] Antarmuka Tampilan Data Dinamis (Data Tables) 
 [REQ-F-10.1]  Aplikasi dapat menampilkan data dalam format tabel yang interaktif di berbagai halaman
 [REQ-F-10.2]  Aplikasi dapat menyediakan fitur pencarian teks pada tabel untuk memfilter data secara real-time.
 [REQ-F-10.3]  Aplikasi dapat menyediakan fitur pengurutan (sorting) data dengan mengklik header kolom.
 [REQ-F-10.4]  Aplikasi dapat menampilkan kontrol paginasi dan pilihan jumlah data per halaman.

---

 3.2 Kebutuhan Back-end (Server-Side / API)

  [REQ-B-1] API Pengguna dan Autentikasi 
 [REQ-B-1.1]  Aplikasi dapat menyediakan API endpoint untuk registrasi pengguna baru dengan validasi keunikan nomor telepon.
*Cek: [C-16]*
 [REQ-B-1.2]  Aplikasi dapat menyediakan API endpoint untuk validasi kredensial login dan mengembalikan session token.
*Cek: [C-14]*
 [REQ-B-1.3]  Aplikasi dapat menyediakan API endpoint untuk reset password melalui verifikasi WhatsApp.
 [REQ-B-1.4]  Aplikasi dapat mengelola dan memvalidasi sesi pengguna dengan timeout otomatis.
*Cek: [C-19]*
  [REQ-B-2] API Manajemen Data Pelanggan 
 [REQ-B-2.1]  Aplikasi dapat menyediakan API endpoint CRUD untuk data pelanggan dengan validasi data KTP.
*Cek: [C-6]*
 [REQ-B-2.2]  Aplikasi dapat menyediakan API endpoint untuk pencarian pelanggan berdasarkan multiple criteria.
 [REQ-B-2.3]  Aplikasi dapat menyediakan API endpoint untuk validasi nomor meter yang unik.
  [REQ-B-3] API Manajemen Tagihan 
 [REQ-B-3.1]  Aplikasi dapat menyediakan API endpoint untuk generate tagihan berdasarkan pembacaan meter dan tarif.
*Cek: [C-11]*
 [REQ-B-3.2]  Aplikasi dapat menyediakan API endpoint untuk mengubah status tagihan dengan validasi business rules.
*Cek: [C-9], [C-10]*
 [REQ-B-3.3]  Aplikasi dapat menyediakan API endpoint untuk mengambil daftar tagihan dengan filter dan pagination.
  [REQ-B-4] API Generate Link WhatsApp 
 [REQ-B-4.1]  Aplikasi dapat menyediakan API endpoint untuk generate link WhatsApp dengan validasi role Keuangan.
*Cek: [C-19]*
 [REQ-B-4.2]  Aplikasi dapat menyediakan API endpoint untuk format pesan tagihan dengan template.
*Cek: [C-7]*
 [REQ-B-4.3]  Aplikasi dapat menyediakan API endpoint untuk menyimpan log generate link WhatsApp.
  [REQ-B-5] API Manajemen Pembayaran 
 [REQ-B-5.1]  Aplikasi dapat menyediakan API endpoint untuk input dan validasi pembayaran.
 [REQ-B-5.2]  Aplikasi dapat menyediakan API endpoint untuk verifikasi pembayaran oleh staff keuangan.
 [REQ-B-5.3]  Aplikasi dapat menyediakan API endpoint untuk mengambil riwayat pembayaran pelanggan.
  [REQ-B-6] API Template dan Konfigurasi 
 [REQ-B-6.1]  Aplikasi dapat menyediakan API endpoint CRUD untuk template notifikasi WhatsApp.
*Cek: [C-4]*
 [REQ-B-6.2]  Aplikasi dapat menyediakan API endpoint untuk replace variabel dalam template dengan data real.
 [REQ-B-6.3]  Aplikasi dapat menyediakan API endpoint untuk manajemen pengaturan sistem.
  [REQ-B-7] API Laporan dan Dashboard 
 [REQ-B-7.1]  Aplikasi dapat menyediakan API endpoint untuk data dashboard dengan agregasi statistik.
 [REQ-B-7.2]  Aplikasi dapat menyediakan API endpoint untuk generate laporan dalam berbagai format.
 [REQ-B-7.3]  Aplikasi dapat menyediakan API endpoint untuk export data ke PDF dan Excel.
  [REQ-B-8] API Manajemen Admin 
 [REQ-B-8.1]  Aplikasi dapat menyediakan API endpoint yang terproteksi untuk manajemen pengguna.
*Cek: [C-2]*
 [REQ-B-8.2]  Aplikasi dapat menyediakan API endpoint untuk audit logs dengan filter dan pagination.
*Cek: [C-5]*
 [REQ-B-8.3]  Aplikasi dapat menyediakan API endpoint untuk backup dan restore data sistem.
  [REQ-B-9] API File Management 
 [REQ-B-9.1]  Aplikasi dapat menyediakan API endpoint untuk upload file dengan hashing nama file.
*Cek: [C-18]*
 [REQ-B-9.2]  Aplikasi dapat menyediakan API endpoint untuk validasi dan kompresi file yang diupload.
  [REQ-B-10] API Tampilan Data Dinamis (Data Tables) 
 [REQ-B-10.1]  Aplikasi dapat menyediakan API endpoint yang mampu menerima parameter query untuk pencarian, pengurutan, dan paginasi.
 [REQ-B-10.2]  Aplikasi dapat mengembalikan data yang sudah diproses dalam format JSON dengan metadata pagination.

---

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)

 4.1 Keamanan (Security)

 [REQ-NF-1]  Sistem harus mengenkripsi password menggunakan bcrypt dengan salt.
 [REQ-NF-2]  Sistem harus mengimplementasikan session timeout setelah 2 jam tidak aktif.
 [REQ-NF-3]  Sistem harus mencatat semua aktivitas penting dalam audit logs.
 [REQ-NF-4]  Sistem harus memvalidasi input untuk mencegah SQL injection dan XSS.

 4.2 Performance

 [REQ-NF-5]  Sistem harus dapat menangani minimal 100 concurrent users.
 [REQ-NF-6]  Response time API tidak boleh lebih dari 5 detik untuk query normal.

 4.3 Reliability

 [REQ-NF-7]  Sistem harus memiliki uptime minimal 95%.
 [REQ-NF-8]  Sistem harus dapat melakukan backup database manual oleh Admin.

 4.4 Usability

 [REQ-NF-9]  Interface harus responsive dan dapat diakses melalui mobile device.
 [REQ-NF-10]  Pesan error harus informatif dan mudah dipahami pengguna.

---

## 5. Skenario Penggunaan Utama

 5.1 Skenario Generate Link WhatsApp untuk Tagihan

 Aktor:  Staff Keuangan

 Prasyarat:  
- Staff sudah login dengan role Keuangan
- Tagihan sudah di-generate dan berstatus 'pending'
- Template notifikasi WhatsApp sudah tersedia

 Langkah-langkah: 
1. Staff Keuangan masuk ke halaman Daftar Tagihan
2. Sistem menampilkan daftar tagihan dengan tombol "Generate Link WhatsApp" untuk tagihan yang berstatus pending
3. Staff mengklik tombol "Generate Link WhatsApp" pada tagihan tertentu
4. Sistem menampilkan modal dengan link WhatsApp dan preview pesan
5. Staff dapat copy link atau klik tombol "Buka WhatsApp"
6. Sistem mencatat log generate link
7. Staff mengirim pesan tagihan melalui WhatsApp secara manual

 Hasil Akhir:  Staff mendapatkan link WhatsApp yang siap digunakan untuk mengirim tagihan kepada pelanggan.

 5.2 Skenario Generate dan Kelola Tagihan Bulanan

 Aktor:  Staff Keuangan

 Prasyarat: 
- Data pelanggan dan meter sudah lengkap
- Periode tagihan sudah dibuat
- Tarif air sudah dikonfigurasi

 Langkah-langkah: 
1. Staff masuk ke halaman Input Pembacaan Meter
2. Staff input data pembacaan meter untuk semua pelanggan
3. Sistem otomatis menghitung pemakaian m3 dan generate tagihan
4. Sistem menampilkan daftar tagihan yang telah di-generate
5. Staff melakukan review dan verifikasi tagihan
6. Staff dapat mengirim notifikasi WhatsApp secara batch atau individual

 Hasil Akhir:  Tagihan bulanan berhasil di-generate dan siap untuk ditagihkan kepada pelanggan.

---

## 6. Arsitektur Sistem

 6.1 Technology Stack
-  Database:  MariaDB
-  Backend Framework:  Laravel (PHP MVC Framework)
-  Frontend:  Blade Templates, HTML, CSS, JavaScript, Bootstrap
-  WhatsApp Integration:  Link generator (wa.me links)
-  Authentication:  Laravel Session dengan middleware authentication
-  File Storage:  Laravel Storage dengan hashed filenames
-  ORM:  Eloquent ORM (Laravel)

 6.2 Database Design
Menggunakan Laravel Migrations dan Eloquent Models dengan skema database yang disederhanakan dengan 9 tabel utama:
- users, roles, customers, meters
- billing_periods, bills, payments
- notification_templates, audit_logs

 6.3 API Design Pattern
- RESTful API dengan Laravel Controllers dan Routes
- JSON response format menggunakan Laravel Resources
- Consistent error handling dengan Laravel Exception Handler
- Laravel middleware untuk authentication dan authorization

---

## 7. Integrasi WhatsApp Link Generator

 7.1 WhatsApp Link Implementation
- Generate wa.me links dengan format: `https://wa.me/{phone}?text={encoded_message}`
- Template message composition dengan variabel dinamis
- URL encoding untuk pesan yang akan dikirim
- Copy to clipboard functionality
- Direct link untuk membuka WhatsApp

 7.2 Message Template System
1. Template message dengan variabel dinamis (customer_name, amount, due_date, dll)
2. Replace variabel dengan data real dari database
3. URL encoding untuk compatibility WhatsApp
4. Preview pesan sebelum generate link
5. Log generate link untuk audit trail

 7.3 Link Generation Flow
1. Staff klik tombol "Generate Link WhatsApp" pada tagihan
2. Sistem load template pesan yang sesuai
3. Replace variabel dengan data tagihan pelanggan
4. Generate wa.me link dengan nomor dan pesan
5. Tampilkan modal dengan link dan preview
6. Staff copy link atau klik "Buka WhatsApp"
7. Record log aktivitas ke database

---

*Dokumen ini menjadi acuan pengembangan Sistem Penagihan PDAM Sederhana dengan WhatsApp Link Generator.*
