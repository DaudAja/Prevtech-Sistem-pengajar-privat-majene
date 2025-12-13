<?php
namespace App\Services;

use App\Models\Pengajar;
use Illuminate\Support\Str;

class KnnService
{
    /**
     * Menghitung jarak Haversine antara dua koordinat dalam Kilometer.
     * Konsisten dengan rumus di laporan Anda.
     */
    protected static function haversine($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
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
     * Mencari K pengajar terdekat menggunakan Logika Bobot KNN Sesuai Laporan.
     * Bobot: Jarak (50%), Mata Pelajaran (30%), Pengalaman (20%).
     * Normalisasi: Semua skor dikalikan 100 agar total Similarity Score max 100.
     */
    public static function recommend(array $criteria, int $k = 5): array
    {
        $lat = $criteria['latitude'] ?? null;
        $lon = $criteria['longitude'] ?? null;
        $subject = isset($criteria['mata_pelajaran']) ? strtolower($criteria['mata_pelajaran']) : null;

        // Ambil semua pengajar yang terverifikasi (Sesuai DFD dan Logika Bisnis)
        // Pastikan relasi user di-load untuk mengakses lokasi (yang sudah kita perbaiki di Model Pengajar)
        $pengajars = Pengajar::verified()->with('user')->get();
        $results = [];

        // Definisi Bobot & Batas Maksimal (Sesuai PenjelasanKNN.md)
        $weights = [
            'distance' => 0.5,
            'subject' => 0.3,
            'experience' => 0.2,
        ];
        $MAX_DISTANCE = 50; // km
        $MAX_EXPERIENCE = 10; // tahun

        foreach ($pengajars as $p) {
            // 1. Perhitungan Jarak
            $distance = self::haversine($lat, $lon, $p->latitude, $p->longitude);
            if ($distance === INF) continue;

            // 2. Perhitungan Score Jarak (50% Bobot)
            // Menggunakan normalisasi linier: semakin dekat ke 0km, score semakin tinggi (max 100)
            // Rumus: max(0, (1 - (distance / MaxDistance))) * 100
            $distanceScore = max(0, (1 - ($distance / $MAX_DISTANCE))) * 100;
            $distanceContribution = $distanceScore * $weights['distance'];


            // 3. Perhitungan Score Mata Pelajaran (30% Bobot)
            // Logika: Cocok Penuh (100), Cocok Parsial (50, jika ingin lebih lunak), Tidak Cocok (0)
            $subjectScore = 0;
            if ($subject && $p->mata_pelajaran) {
                 $mp = strtolower($p->mata_pelajaran);
                 if (Str::contains($mp, $subject) || Str::contains($subject, $mp)) {
                    $subjectScore = 100; // Match Penuh
                 }
            }
            $subjectContribution = $subjectScore * $weights['subject'];


            // 4. Perhitungan Score Pengalaman (20% Bobot)
            // Rumus: min(100, (pengalaman / MaxExperience)) * 100
            $experienceScore = min(100, ((int)$p->pengalaman_tahun / $MAX_EXPERIENCE) * 100);
            $experienceContribution = $experienceScore * $weights['experience'];

            // 5. Total Similarity Score (Max 100)
            $totalSimilarity = $distanceContribution + $subjectContribution + $experienceContribution;

            $results[] = [
                'pengajar' => $p,
                'distance' => round($distance, 3),
                'score' => round($totalSimilarity, 2), // Skor di-round ke 2 desimal
            ];
        }

        // Urutkan berdasarkan Similarity Score (tertinggi ke terendah)
        usort($results, fn($a,$b) => $b['score'] <=> $a['score']);

        // Ambil Top-K
        return array_slice($results, 0, $k);
    }
}
