# GLOBAL SUPPLY CHAIN RISK INTELLIGENCE 

Aplikasi Web Monitoring dan Analisis Platform Intelijen Risiko Rantai Pasok Global (Global Supply Chain Risk Intelligence)  berbasis **Laravel 12**. Sistem ini mengintegrasikan berbagai data eksternal secara real-time maupun berkala—seperti cuaca global, indikator ekonomi World Bank, pergerakan kurs mata uang, berita sentimen industri, serta direktori pelabuhan dunia—untuk memberikan kalkulasi skor risiko komposit bagi keputusan rantai pasok.


## Fitur Utama

- **Dashboard Interactive**: Visualisasi peta distribusi pelabuhan, ringkasan cuaca global, metrik risiko utama, dan grafik tren.
- **Monitoring Cuaca Global**: Integrasi Open-Meteo API dengan peta Leaflet interaktif untuk memantau suhu, curah hujan, dan kecepatan angin per negara.
- **Analisis Risiko Negara**: Perhitungan skor risiko komposit berdasarkan parameter Cuaca, Inflasi, Kurs, dan Sentimen Berita.
- **Direktori & Tracking Pelabuhan**: Peta pelabuhan dunia dari World Port Index beserta kalkulasi skor risiko pelabuhan.
- **Monitoring Kurs Mata Uang**: Integrasi ExchangeRate-API untuk memantau fluktuasi nilai tukar terhadap USD.
- **Analisis Sentimen Berita**: GNews API caching dengan analisis otomatis kata positif/negatif.
- **Perbandingan Negara & Watchlist**: Fitur benchmarking antar negara dan penyaringan watchlist per pengguna.
- **Laporan & Export Data**: Ringkasan laporan sistem dengan dukungan Cetak Laporan (`window.print()`) dan **Export CSV** (UTF-8 BOM compatible untuk Excel).
- **Panel Admin & CRUD AJAX**: Pengelolaan User, Negara, Pelabuhan, dan Artikel secara AJAX tanpa full page reload.


## Teknologi & Framework

- **Backend Core**: PHP ^8.2, Laravel ^12.0
- **Database**: MySQL
- **Frontend UI & Styling**: HTML5, JavaScript (ES6), Bootstrap 5, Bootstrap Icons
- **Visualisasi & Peta**: Leaflet.js (OpenStreetMap), Chart.js
- **Teknik Interaksi**: Asynchronous AJAX (Fetch API)



## External API yang Terintegrasi

1. **REST Countries API**: Data dasar negara, bendera, koordinat, dan mata uang.
2. **Open-Meteo API**: Data cuaca terkini (suhu, curah hujan, kecepatan angin).
3. **World Bank API**: Indikator ekonomi tahunan (GDP, inflasi, populasi).
4. **ExchangeRate-API**: Value kurs nilai tukar mata uang real-time.
5. **GNews API**: Cache berita terkait isu rantai pasok dan ekonomi.
6. **World Port Index (NGA)**: Direktori lokasi dan status pelabuhan dunia.

> *Catatan*: Fitur sinkronisasi API memerlukan koneksi internet aktif.


## Laravel Scheduler

Sinkronisasi otomatis dijalankan lewat Laravel Scheduler dan command Artisan berikut:

```bash
php artisan scm:sync-weather
php artisan scm:sync-currency
php artisan scm:sync-news
php artisan scm:recalculate-risks
```

Jadwal otomatis:

- Cuaca Open-Meteo: setiap jam menit `00`
- Kurs ExchangeRate API: setiap jam menit `10`
- Berita GNews: setiap 3 jam menit `20`
- Risk scoring negara: setiap jam menit `30`

Pada Windows/Laragon, jalankan scheduler dari folder project:

```bash
php artisan schedule:work
```

Terminal harus tetap terbuka. Jika terminal ditutup, scheduler ikut berhenti. Tombol sinkronisasi manual di Panel Admin tetap tersedia untuk pembaruan manual.

World Bank, REST Countries, dan World Port Index tetap dijalankan manual dari Panel Admin sesuai kebutuhan data master.


## Struktur Role & Hak Akses

1. **Admin**:
   - Memiliki akses penuh ke halaman internal dan Panel Admin (`/admin`).
   - Melakukan CRUD User, Negara, Pelabuhan, dan Artikel.
   - Menjalankan perintah sinkronisasi API external dan kalkulasi ulang skor risiko.
   - Dilindungi proteksi anti-self-delete dan proteksi minimal 1 akun Admin aktif.
2. **User (Pengguna Biasa)**:
   - Mengakses Dashboard, Halaman Negara, Risiko, Cuaca, Pelabuhan, Kurs, Berita, Perbandingan, Watchlist, dan Laporan.
   - Mengelola Watchlist pribadi dan mengunduh Laporan CSV.
   - Dilarang mengakses `/admin` atau fungsi manajemen sistem.

### Cara Menjalankan Project:

1. **Clone repository & masuk ke direktori project**:
   ```bash
   cd supply-chain-management
   ```

2. **Install dependensi PHP**:
   ```bash
   composer install
   ```

3. **Salin file lingkungan (.env)**:
   ```bash
   copy .env.example .env
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi Database di file `.env`**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=scm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Jalankan Migration & Seeder Data Awal**:
   ```bash
   php artisan migrate --seed
   ```

7. **Konfigurasi API Key (Opsional di `.env`)**:
   ```env
   GNEWS_API_KEY=your_gnews_api_key_here
   ```

   Sesuaikan juga URL/key lain di `.env` jika menggunakan provider API berbeda. Jangan menuliskan API key asli di repository.

   Setelah mengganti key atau konfigurasi API, jalankan:
   ```bash
   php artisan optimize:clear
   ```

8. **Jalankan Dev Server**:
   ```bash
   php artisan serve
   ```
   Buka browser di `http://127.0.0.1:8000`.

---

## Catatan Keamanan

- File `.env` terdaftar pada `.gitignore` dan **TIDAK BUKAN** bagian dari repository versi publik.
- Saat demonstrasi final, disarankan mengatur `APP_DEBUG=false` di file `.env`.
