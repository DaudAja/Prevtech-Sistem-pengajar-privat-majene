<?php
namespace App\Services;

use App\Models\Pengajar;
use Illuminate\Support\Str;

class KnnService
{
    /**
     * Menghitung Euclidean Distance antara dua koordinat Lat/Lon.
     * NILAI INI DIGUNAKAN UNTUK PERHITUNGAN FSS (Distance Metric).
     */
    protected static function euclideanDistance($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return INF;
        }

        // Euclidean Distance Formula: sqrt((x2 - x1)^2 + (y2 - y1)^2)
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        return sqrt($dLat**2 + $dLon**2);
    }

    /**
     * Menghitung jarak Haversine antara dua koordinat dalam Kilometer.
     * NILAI INI DIGUNAKAN HANYA UNTUK TUJUAN DISPLAY (TAMPILAN JARAK FISIK).
     */
    protected static function calculateHaversine($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
             // Jika ada data koordinat null, kembalikan jarak yang besar
             return INF;
        }
        $earthRadius = 6371.0; // Radius bumi dalam KM
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Mencari K pengajar terdekat menggunakan Logika Bobot KNN.
     */
    public static function recommend(array $criteria, int $k = 5): array
    {
        $lat = $criteria['latitude'] ?? null;
        $lon = $criteria['longitude'] ?? null;
        $subject = isset($criteria['mata_pelajaran']) ? strtolower($criteria['mata_pelajaran']) : null;

        $pengajars = Pengajar::verified()->with('user')->get();
        $results = [];

        // Definisi Bobot (Total 1.0)
        $weights = [
            'distance' => 0.7,
            'subject' => 0.3,
        ];

        foreach ($pengajars as $p) {

            // 1. Perhitungan Jarak Euclidean (Untuk FSS/KNN)
            $distance_euc = self::euclideanDistance($lat, $lon, $p->latitude, $p->longitude);
            if ($distance_euc === INF) continue; // Skip jika koordinat tidak valid

            // 1.5. Perhitungan Jarak Haversine (Untuk Display KM)
            $distance_km = self::calculateHaversine($lat, $lon, $p->latitude, $p->longitude);

            // 2. Kontribusi Jarak (70% Bobot)
            // Mengubah Jarak Euclidean menjadi Skor Kemiripan D-Norm
            $distanceScoreNorm = 1 / (1 + $distance_euc);
            // Kontribusi dihitung: (D-Norm * 100) * Bobot
            $distanceContribution = ($distanceScoreNorm * 100) * $weights['distance'];


            // 3. Kontribusi Mata Pelajaran (30% Bobot)
            $subjectScore = 0;
            if ($subject && $p->mata_pelajaran) {
                $mp = strtolower($p->mata_pelajaran);
                // Logika kecocokan string
                if (Str::contains($mp, $subject) || Str::contains($subject, $mp)) {
                    $subjectScore = 100; // Match Penuh (Skor Mentah)
                }
            }
            $subjectContribution = $subjectScore * $weights['subject'];


            // 4. Total Similarity Score (FSS - Max 100)
            $totalSimilarity = $distanceContribution + $subjectContribution;

            $results[] = [
                'pengajar' => $p,
                // Tambahkan kedua jarak
                'distance_euc' => round($distance_euc, 6),
                'distance_km' => round($distance_km, 2), // Gunakan ini untuk tampilan KM
                'score' => round($totalSimilarity, 2),
            ];
        }

        // Urutkan berdasarkan Similarity Score (tertinggi ke terendah)
        usort($results, fn($a,$b) => $b['score'] <=> $a['score']);

        // Ambil Top-K
        return array_slice($results, 0, $k);
    }
}
