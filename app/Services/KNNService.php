<?php
namespace App\Services;

use App\Models\Pengajar;
use Illuminate\Support\Str;

class KnnService
{
    protected static function haversine($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return INF;
        }
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public static function recommend(array $criteria, int $k = 5): array {
        $lat = $criteria['latitude'] ?? null;
        $lon = $criteria['longitude'] ?? null;
        $subject = isset($criteria['mata_pelajaran']) ? strtolower($criteria['mata_pelajaran']) : null;

        $pengajars = Pengajar::whereNotNull('latitude')->whereNotNull('longitude')->with('ulasan')->get();
        $results = [];

        foreach ($pengajars as $p) {
            $distance = self::haversine($lat, $lon, (float)$p->latitude, (float)$p->longitude);
            if ($distance === INF) continue;

            $mp = strtolower($p->mata_pelajaran ?? '');
            $subjectScore = 0.5;
            if ($subject) {
                if ($mp === $subject) $subjectScore = 1.0;
                else if (Str::contains($mp, $subject) || Str::contains($subject, $mp)) $subjectScore = 0.75;
                else $subjectScore = 0.0;
            }

            $expScore = min((int)$p->pengalaman_tahun, 20) / 20;
            $distanceScore = 1 / (1 + $distance);
            $totalScore = ($distanceScore * 0.6) + ($subjectScore * 0.25) + ($expScore * 0.15);

            $results[] = [
                'pengajar' => $p,
                'distance' => round($distance, 3),
                'score' => round($totalScore, 4),
            ];
        }

        usort($results, fn($a,$b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $k);
    }
}
