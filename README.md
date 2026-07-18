# PROJECT FINAL: Global Supply Chain Risk Intelligence Platform
### Platform Monitoring Risiko Rantai Pasok Global Berbasis Multi-API dan Analitik Data

---

## 📋 Spesifikasi Proyek
Proyek ini dikembangkan untuk mengelola dan memantau keputusan bisnis rantai pasok global dengan sangat bergantung pada data untuk:
- Mengelola risiko logistik  
- Memantau kondisi cuaca ekstrem  
- Menganalisis gangguan transportasi  
- Mengamati kondisi ekonomi suatu negara  
- Membantu pengambilan keputusan bisnis  

Proyek ini memperlihatkan kemampuan mahasiswa dalam:
- **Full Stack Development**  
- **API Integration**  
- **Data Engineering**  
- **Dashboard Analytics**  
- **Geospatial Visualization**  
- **Business Intelligence**  
- **Decision Support System**  

---

## 💼 Studi Kasus
Sebuah perusahaan ingin mengimpor barang dari berbagai negara di seluruh dunia.
### Masalah:
- Cuaca buruk dapat mengganggu jalannya pengiriman logistik.  
- Nilai tukar mata uang berfluktuasi secara dinamis.  
- Konflik geopolitik meningkatkan risiko keamanan pengiriman.  
- Kemacetan pelabuhan laut menyebabkan keterlambatan pasokan barang.  
- Tingkat inflasi di suatu negara mempengaruhi biaya produksi barang impor.  

**Solusi**: Dibangunlah sistem **TrackFlow** yang dapat memantau seluruh indikator krusial tersebut dalam satu dashboard analitik yang terpadu.

---

## 🛠️ Spesifikasi Teknologi
### Backend
- **PHP** (>= 8.2)
- **Laravel Framework** (v11)
- **MySQL / MariaDB**

### Frontend
- **Bootstrap 5** (untuk antarmuka responsif premium)
- **AJAX** (untuk interaksi dinamis tanpa reload)
- **JavaScript ES6** (manipulasi DOM dan inisialisasi modul)

### Visualisasi
- **Chart.js** (grafik analitik garis, batang, dan radar)
- **Leaflet.js** & **OpenStreetMap** (peta geospatial interaktif)

---

## 🔌 API Gratis yang Digunakan

1. **Open-Meteo API (Cuaca Global)**
   - **Kegunaan**: Pemantauan suhu udara, curah hujan, kecepatan angin, dan risiko badai.
   - **Autentikasi**: Tanpa API Key (Gratis).
   - **Website**: [Open-Meteo API](https://open-meteo.com/)

2. **World Bank API (Kondisi Ekonomi Makro)**
   - **Kegunaan**: Mengambil indikator GDP, Inflasi tahunan, Populasi, Ekspor, dan Impor negara terkait.
   - **Autentikasi**: Tanpa API Key (Gratis).
   - **Website**: [World Bank API](https://data.worldbank.org/)

3. **REST Countries API (Profil Negara)**
   - **Kegunaan**: Menyediakan data profil dasar negara, mata uang lokal, wilayah geografis, dan bahasa.
   - **Autentikasi**: Menggunakan Bearer token (Gratis).
   - **Website**: [REST Countries API](https://restcountries.com/)

4. **ExchangeRate API (Kurs Valas Real-time)**
   - **Kegunaan**: Mengambil nilai tukar mata uang asing secara real-time terhadap USD ($).
   - **Autentikasi**: Tanpa API Key (Gratis).
   - **Website**: [ExchangeRate API](https://www.exchangerate-api.com/)

5. **Marine Traffic Alternative API (World Port Index)**
   - **Kegunaan**: Memvisualisasikan titik lokasi pelabuhan internasional dan negara asalnya.
   - **Dataset**: [World Port Index Dataset](https://msi.nga.mil/Publications/WPI) (Gratis).

6. **GNews API (News API Alternatif)**
   - **Kegunaan**: Menyediakan berita teraktual terkait Logistik, Perdagangan, Pelayaran, dan Geopolitik Ekonomi.
   - **Autentikasi**: Menggunakan API Key (Gratis).
   - **Website**: [GNews API](https://gnews.io/)

---

## 🌟 Fitur Utama Sistem

### 1. Global Country Dashboard
Menampilkan visualisasi lengkap profil negara yang dipilih pengguna (misal: Germany, China, Indonesia, Australia):
- Nilai GDP, Inflasi, Populasi, dan Mata Uang lokal.
- Laporan cuaca satelit terkini secara komprehensif.

### 2. Risk Scoring Engine (Weighted Risk Model)
Algoritma internal buatan mahasiswa untuk menghitung skor risiko supply chain suatu negara secara dinamis:
$$\text{Risk Score} = (\text{Weather} \times 0.3) + (\text{Inflation} \times 0.2) + (\text{Exchange Rate} \times 0.1) + (\text{News Sentiment} \times 0.4)$$
- **Output**: Skor 0-100 dan status risiko (misal: Germany: 22 [Low Risk], China: 47 [Medium Risk]).

### 3. Global Weather Monitoring
Peta interaktif Leaflet.js yang menunjukkan indikator hujan, badai, dan angin kencang secara geospatial berdasarkan negara yang dipantau.

### 4. Currency Impact Dashboard
Menampilkan visualisasi grafik pergerakan kurs valuta asing real-time dan konverter kalkulator cerdas menggunakan Chart.js.

### 5. News Intelligence (Sentiment Analysis)
Menyajikan berita supply chain terhangat (Logistics, Trade, Shipping, Economy) dari GNews API.

### 6. Port Location Dashboard
Peta geospatial titik pelabuhan internasional yang dilengkapi fitur pencarian pelabuhan, pencarian negara, dan marker interaktif.

### 7. Data Visualization Dashboard
Menyajikan tren data makro dalam satu halaman menggunakan Chart.js:
- GDP Trend
- Inflation Trend
- Currency Volatility Trend
- Risk Level Trend

### 8. Country Comparison Engine
Halaman komparasi dinamis berdampingan (*side-by-side*) antara dua negara (contoh: Germany vs Australia) membandingkan GDP, Inflasi, Tingkat Risiko, Parameter Cuaca, dan Nilai Tukar Valas.

### 9. Favorite Monitoring List (My Favorite)
Menyimpan negara-negara pantauan favorit pengguna secara aman (menggunakan Database untuk user login dan Web Session untuk user guest).

### 10. Admin Dashboard (Panel Administrator)
Panel khusus admin dengan hak akses penuh untuk mengelola:
- Pengguna (*Users*)
- Dataset pelabuhan (*Ports*)
- Artikel analisis industri (*Articles*)

---

## 🧠 Fitur AI / Data Science (Lexicon Based Sentiment Analysis)
Analisis sentimen berita dikembangkan menggunakan metode **Lexicon-Based Sentiment Analysis** berbasis PHP murni. Algoritma melakukan tokenisasi kata dari konten berita lalu mencocokkannya dengan kamus kata positif dan negatif di database.

### Struktur Tabel Database Sentimen:
- **`positive_words`**: Menyimpan kata-kata positif (misal: `growth`, `increase`, `profit`, `stable`, `improve`).
- **`negative_words`**: Menyimpan kata-kata negatif (misal: `war`, `crisis`, `inflation`, `delay`, `disaster`).

### Contoh Alur Kerja Analisis Sentimen:
1. Berita: *"Inflation increases while exports decrease due to war."*
2. **Tokenisasi & Klasifikasi**:
   - Kata Positif found: `increase` (+1)
   - Kata Negatif found: `inflation` (-1), `war` (-1), `decrease` (-1)
3. **Kalkulasi**: Positive: 1, Negative: 3.
4. **Output Sentimen**: **Negative** (Risiko Rantai Pasok Naik).

---

## 🔌 Daftar REST API yang Dibuat Mahasiswa
Aplikasi ini menyediakan endpoint internal REST API mandiri:
1. `GET /api/countries` : Mengambil seluruh data koordinat geografis negara.
2. `GET /api/risk` : Mengambil skor risiko akhir suatu negara dan data penyusunnya.
3. `GET /api/ports` : Mengambil titik lokasi pelabuhan dan relasi negaranya.
4. `GET /api/news` : Mengambil artikel berita supply chain beserta hasil kalkulasi sentimennya.
5. `GET /api/currency` : Mengambil kurs valas real-time terhadap USD ($).

---

## 💻 Cara Menjalankan Aplikasi di Komputer Lokal

### 1. Persiapan Awal
Pastikan Anda sudah menginstal PHP (>= 8.2), Composer, dan Laragon / XAMPP.

### 2. Jalankan Perintah Instalasi
Buka terminal pada direktori proyek Anda:
```bash
# 1. Install semua dependensi PHP
composer install

# 2. Salin environment file
cp .env.example .env

# 3. Generate key keamanan aplikasi
php artisan key:generate
```

### 3. Setup Database & Seeding
Sesuaikan kredensial database di `.env` (misal nama database: `trackflow`). Kemudian migrasikan database dan jalankan *seeder* bawaan untuk mengisi data awal:
```bash
php artisan migrate:fresh --seed
```

### 4. Bersihkan Cache
```bash
php artisan view:clear
php artisan cache:clear
```

### 5. Jalankan Server
```bash
php artisan serve
```
Buka browser Anda dan akses: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔐 Akun Admin Default
- **Email**: `admin@gmail.com`
- **Password**: `12345678`
