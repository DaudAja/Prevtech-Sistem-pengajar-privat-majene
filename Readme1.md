# Sistem Rekomendasi Pengajar Privat - Laravel

Sistem rekomendasi pengajar privat di Kabupaten Majene menggunakan algoritma K-Nearest Neighbor (KNN) untuk mencocokkan pengajar dengan pelajar berdasarkan kriteria dan lokasi terdekat.

## Fitur Utama

### 1. Autentikasi Multi-Role
- **Admin**: Kelola data pengajar, pelajar, dan rekomendasi
- **Pengajar**: Kelola profil, lihat jadwal, konfirmasi permintaan
- **Pelajar**: Cari pengajar, lihat detail, hubungi pengajar, beri ulasan

### 2. Algoritma KNN
- Perhitungan jarak Euclidean untuk menentukan pengajar terdekat
- Matching berdasarkan lokasi, mata pelajaran, dan pengalaman
- Rekomendasi top-K pengajar terbaik

<?php

namespace App\Services;

use App\Models\Pengajar;
use App\Models\User;
use App\Models\Rekomendasi;
use Illuminate\Support\Facades\DB;

class KNNService
{
    /**
     * Hitung jarak Euclidean antara dua koordinat
     * Menggunakan rumus Haversine untuk akurasi lebih baik
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam kilometer
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Hitung nilai kemiripan berdasarkan multiple criteria
     *
     * @param User $pelajar
     * @param Pengajar $pengajar
     * @param string $mataPelajaranDicari
     * @return float Nilai kemiripan (0-100)
     */
    public function calculateSimilarity($pelajar, $pengajar, $mataPelajaranDicari = null)
    {
        $similarity = 0;
        $weights = [
            'distance' => 0.5,      // 50% bobot untuk jarak
            'subject' => 0.3,       // 30% bobot untuk mata pelajaran
            'experience' => 0.2,    // 20% bobot untuk pengalaman
        ];

        // 1. Similarity berdasarkan jarak (semakin dekat semakin tinggi)
        if ($pelajar->latitude && $pelajar->longitude &&
            $pengajar->user->latitude && $pengajar->user->longitude) {

            $distance = $this->calculateDistance(
                $pelajar->latitude,
                $pelajar->longitude,
                $pengajar->user->latitude,
                $pengajar->user->longitude
            );

            // Normalisasi jarak (max 50km)
            $maxDistance = 50;
            $distanceScore = max(0, (1 - ($distance / $maxDistance)) * 100);
            $similarity += $distanceScore * $weights['distance'];
        }

        // 2. Similarity berdasarkan mata pelajaran
        if ($mataPelajaranDicari) {
            $subjectMatch = stripos($pengajar->mata_pelajaran, $mataPelajaranDicari) !== false;
            $similarity += ($subjectMatch ? 100 : 0) * $weights['subject'];
        }

        // 3. Similarity berdasarkan pengalaman (normalisasi max 10 tahun)
        $maxExperience = 10;
        $experienceScore = min(100, ($pengajar->pengalaman_tahun / $maxExperience) * 100);
        $similarity += $experienceScore * $weights['experience'];

        return round($similarity, 2);
    }

    /**
     * Cari K pengajar terdekat menggunakan algoritma KNN
     *
     * @param int $userId ID pelajar
     * @param string $mataPelajaran Mata pelajaran yang dicari
     * @param int $k Jumlah tetangga terdekat
     * @return array Daftar pengajar terdekat dengan jarak dan similarity
     */
    public function findNearestTeachers($userId, $mataPelajaran = null, $k = 5)
    {
        $pelajar = User::findOrFail($userId);

        // Validasi koordinat pelajar
        if (!$pelajar->latitude || !$pelajar->longitude) {
            throw new \Exception('Lokasi pelajar belum diset. Silakan lengkapi profil Anda.');
        }

        // Ambil semua pengajar yang terverifikasi
        $query = Pengajar::with('user', 'ulasan')
            ->verified();

        // Filter berdasarkan mata pelajaran jika ada
        if ($mataPelajaran) {
            $query->byMataPelajaran($mataPelajaran);
        }

        $pengajarList = $query->get();

        // Hitung jarak dan similarity untuk setiap pengajar
        $results = [];
        foreach ($pengajarList as $pengajar) {
            if (!$pengajar->user->latitude || !$pengajar->user->longitude) {
                continue; // Skip pengajar tanpa koordinat
            }

            $distance = $this->calculateDistance(
                $pelajar->latitude,
                $pelajar->longitude,
                $pengajar->user->latitude,
                $pengajar->user->longitude
            );

            $similarity = $this->calculateSimilarity($pelajar, $pengajar, $mataPelajaran);

            $results[] = [
                'pengajar' => $pengajar,
                'distance' => $distance,
                'similarity' => $similarity,
                'average_rating' => $pengajar->average_rating,
                'total_ulasan' => $pengajar->total_ulasan,
            ];
        }

        // Urutkan berdasarkan similarity (tertinggi ke terendah)
        usort($results, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Ambil K terdekat
        $topK = array_slice($results, 0, $k);

        // Simpan hasil rekomendasi ke database
        $this->saveRecommendations($userId, $topK);

        return $topK;
    }

    /**
     * Simpan hasil rekomendasi ke database
     *
     * @param int $userId ID pelajar
     * @param array $recommendations Hasil rekomendasi
     * @return void
     */
    private function saveRecommendations($userId, $recommendations)
    {
        // Hapus rekomendasi lama (opsional, bisa dikomentari jika ingin menyimpan history)
        // Rekomendasi::where('user_id', $userId)->delete();

        foreach ($recommendations as $rec) {
            Rekomendasi::updateOrCreate(
                [
                    'user_id' => $userId,
                    'pengajar_id' => $rec['pengajar']->id,
                ],
                [
                    'nilai_kemiripan' => $rec['similarity'],
                    'jarak_km' => $rec['distance'],
                    'tanggal_rekomendasi' => now(),
                ]
            );
        }
    }

    /**
     * Get rekomendasi history untuk user
     *
     * @param int $userId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendationHistory($userId, $limit = 10)
    {
        return Rekomendasi::with(['pengajar.user', 'pengajar.ulasan'])
            ->where('user_id', $userId)
            ->orderBy('tanggal_rekomendasi', 'desc')
            ->limit($limit)
            ->get();
    }
}


### 3. Peta Digital
- Integrasi Google Maps / Leaflet
- Visualisasi lokasi pengajar dan pelajar
- Perhitungan jarak real-time

## Requirements

- PHP >= 8.1
- Composer
- MySQL >= 5.7 atau PostgreSQL >= 12
- Node.js >= 16.x
- NPM atau Yarn

## Instalasi

### 1. Clone atau Extract Project

```bash
cd /path/to/your/project
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengajar_privat
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi Database

```bash
# Jalankan migrasi
php artisan migrate

# (Opsional) Seed data dummy
php artisan db:seed
```

### 6. Jalankan Aplikasi

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Compile assets
npm run dev
```

Aplikasi akan berjalan di: `http://localhost:8000`

## Struktur Database

### Tabel Users
- id, name, email, password, role (admin/pengajar/pelajar)
- umur, alamat, latitude, longitude
- created_at, updated_at

### Tabel Pengajar
- id, user_id (foreign key)
- mata_pelajaran, pendidikan_terakhir
- pengalaman_tahun, deskripsi
- tarif_per_jam, status_verifikasi
- created_at, updated_at

### Tabel Rekomendasi
- id, user_id (pelajar), pengajar_id
- nilai_kemiripan (hasil KNN)
- tanggal_rekomendasi
- created_at, updated_at

### Tabel Ulasan
- id, user_id (pelajar), pengajar_id
- rating (1-5), komentar
- created_at, updated_at

### Tabel Permintaan
- id, user_id (pelajar), pengajar_id
- mata_pelajaran, deskripsi
- status (pending/diterima/ditolak)
- created_at, updated_at

## Penggunaan

### Login Default

**Admin:**
- Email: admin@pengajarprivat.com
- Password: admin123

**Pengajar (contoh):**
- Email: pengajar1@example.com
- Password: pengajar123

**Pelajar (contoh):**
- Email: pelajar1@example.com
- Password: pelajar123

### Alur Penggunaan

1. **Pelajar** melakukan registrasi dan login
2. **Pelajar** input kriteria pencarian (mata pelajaran, lokasi)
3. **Sistem** menjalankan algoritma KNN untuk mencari pengajar terdekat
4. **Pelajar** melihat daftar rekomendasi pengajar
5. **Pelajar** menghubungi pengajar atau membuat permintaan
6. **Pengajar** menerima notifikasi dan konfirmasi permintaan
7. **Pelajar** memberikan ulasan setelah pembelajaran

## Algoritma KNN

Implementasi algoritma K-Nearest Neighbor menggunakan rumus Euclidean Distance:

```
d = √[(x₂ - x₁)² + (y₂ - y₁)²]
```

Dimana:
- (x₁, y₁) = koordinat lokasi pelajar
- (x₂, y₂) = koordinat lokasi pengajar
- d = jarak dalam kilometer

Nilai K default = 5 (menampilkan 5 pengajar terdekat)

## API Endpoints

### Authentication
- POST `/register` - Registrasi user baru
- POST `/login` - Login user
- POST `/logout` - Logout user

### Pelajar
- GET `/pelajar/dashboard` - Dashboard pelajar
- GET `/pelajar/cari-pengajar` - Form pencarian pengajar
- POST `/pelajar/rekomendasi` - Proses KNN dan tampilkan hasil
- GET `/pelajar/pengajar/{id}` - Detail pengajar
- POST `/pelajar/permintaan` - Buat permintaan ke pengajar
- POST `/pelajar/ulasan` - Beri ulasan pengajar

### Pengajar
- GET `/pengajar/dashboard` - Dashboard pengajar
- GET `/pengajar/profil` - Lihat/edit profil
- PUT `/pengajar/profil` - Update profil
- GET `/pengajar/permintaan` - Daftar permintaan masuk
- POST `/pengajar/permintaan/{id}/konfirmasi` - Konfirmasi permintaan

### Admin
- GET `/admin/dashboard` - Dashboard admin
- GET `/admin/pengajar` - Kelola data pengajar
- GET `/admin/pelajar` - Kelola data pelajar
- GET `/admin/rekomendasi` - Kelola data rekomendasi
- POST `/admin/pengajar/{id}/verifikasi` - Verifikasi pengajar

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Permission denied (storage/logs)
```bash
chmod -R 775 storage bootstrap/cache
```

### Error: Mix manifest not found
```bash
npm run dev
```

### Database connection error
- Pastikan MySQL/PostgreSQL sudah running
- Cek konfigurasi di file `.env`
- Buat database manual jika belum ada

## Teknologi yang Digunakan

- **Laravel 10.x** - PHP Framework
- **MySQL/PostgreSQL** - Database
- **Bootstrap 5** - CSS Framework
- **Leaflet.js** - Peta interaktif
- **Chart.js** - Visualisasi data (dashboard)
- **jQuery** - JavaScript library

## Lisensi

Project ini dibuat untuk keperluan akademik (Tugas Rekayasa Perangkat Lunak - Universitas Sulawesi Barat)

## Kontak

Untuk pertanyaan atau bantuan, hubungi:
- Email: support@pengajarprivat.com
- GitHub: [repository-url]

---

**Catatan Penting:**
- Sistem ini adalah prototype untuk keperluan akademik
- Pastikan untuk mengubah kredensial default sebelum production
- Backup database secara berkala
- Gunakan HTTPS untuk keamanan data
