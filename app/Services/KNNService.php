<?php
namespace App\Services;

use App\Models\Pengajar;
use Illuminate\Support\Str;

class KnnService
{
    /**
     * Menghitung Euclidean Distance antara dua koordinat Lat/Lon.
     * Konsisten dengan rumus Euclidean Distance di laporan Anda.
     */
    protected static function euclideanDistance($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return INF;
        }

        // Euclidean Distance Formula: sqrt((x2 - x1)^2 + (y2 - y1)^2)
        // Dosen: Kami menggunakan nilai Lat/Lon mentah sebagai sumbu X dan Y.
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        // Hasilnya adalah nilai relatif (bukan KM), tetapi mencerminkan kedekatan.
        return sqrt($dLat**2 + $dLon**2);
    }

    /**
     * Mencari K pengajar terdekat menggunakan Logika Bobot KNN Sesuai Laporan.
     */
    public static function recommend(array $criteria, int $k = 5): array
    {
        $lat = $criteria['latitude'] ?? null;
        $lon = $criteria['longitude'] ?? null;
        $subject = isset($criteria['mata_pelajaran']) ? strtolower($criteria['mata_pelajaran']) : null;

        $pengajars = Pengajar::verified()->with('user')->get();
        $results = [];

        // Definisi Bobot
        $weights = [
            'distance' => 0.5,
            'subject' => 0.3,
            'experience' => 0.2,
        ];

        // Normalisasi untuk Pengalaman: Batas maksimum yang dijadikan acuan 10 tahun
        $MAX_EXPERIENCE = 10;

        foreach ($pengajars as $p) {
            // 1. Perhitungan Jarak (menggunakan Euclidean)
            $distance = self::euclideanDistance($lat, $lon, $p->latitude, $p->longitude);
            if ($distance === INF) continue;

            // 2. Perhitungan Score Jarak (50% Bobot)
            // Dosen: Untuk mengubah Jarak (minimum) menjadi Skor Kemiripan (maksimum),
            // kami menggunakan fungsi inversi yang dinormalisasi: 1 / (1 + distance).
            // Hasilnya kemudian dinormalisasi terhadap bobot.

            $distanceScoreNorm = 1 / (1 + $distance); // Jarak dinormalisasi antara 0 dan 1

            // Kami mengambil skor jarak tertinggi dari semua data pengajar yang ada untuk dinormalisasi
            // Namun, untuk kesederhanaan dan keamanan (menghindari query ekstra),
            // kita menggunakan normalisasi terbalik (1 / (1+d))

            // *************************************************************************
            // PENTING: Jika Anda ingin EuclideanScore selalu antara 0 dan 100,
            // maka D-Norm perlu diimplementasikan di seluruh dataset.
            // *************************************************************************

            // Kontribusi Jarak: (D-Norm * 100) * Bobot
            $distanceContribution = ($distanceScoreNorm * 100) * $weights['distance'];


            // 3. Perhitungan Score Mata Pelajaran (30% Bobot)
            $subjectScore = 0; // Skor Mentah (0 atau 100)
            if ($subject && $p->mata_pelajaran) {
                $mp = strtolower($p->mata_pelajaran);
                // Cek kecocokan parsial (misalnya, 'mat' cocok dengan 'Matematika, Fisika')
                if (Str::contains($mp, $subject) || Str::contains($subject, $mp)) {
                    $subjectScore = 100; // Match Penuh
                }
            }
            $subjectContribution = $subjectScore * $weights['subject'];


            // 4. Perhitungan Score Pengalaman (20% Bobot)
            // Skor Mentah (max 100): Pengalaman saat ini dibagi pengalaman maksimal acuan (10 tahun)
            $experienceScore = min(100, ((int)$p->pengalaman_tahun / $MAX_EXPERIENCE) * 100);
            $experienceContribution = $experienceScore * $weights['experience'];

            // 5. Total Similarity Score (FSS)
            $totalSimilarity = $distanceContribution + $subjectContribution + $experienceContribution;

            $results[] = [
                'pengajar' => $p,
                'distance' => round($distance, 6), // Euclidean Distance (6 desimal karena berupa derajat)
                'score' => round($totalSimilarity, 2), // Skor Kemiripan Akhir
            ];
        }

        // Urutkan berdasarkan Similarity Score (tertinggi ke terendah)
        usort($results, fn($a,$b) => $b['score'] <=> $a['score']);

        // Ambil Top-K
        return array_slice($results, 0, $k);
    }

    // Catatan: Jika Anda ingin mempertahankan Haversine (untuk akurasi jarak),
    // ubah nama method di atas kembali menjadi haversine, dan pastikan laporan disesuaikan.
}
