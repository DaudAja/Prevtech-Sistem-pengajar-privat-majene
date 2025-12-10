# Penjelasan Algoritma K-Nearest Neighbor (KNN)

## Konsep Dasar

Algoritma K-Nearest Neighbor (KNN) adalah algoritma machine learning yang sederhana namun powerful untuk klasifikasi dan rekomendasi. Dalam sistem ini, KNN digunakan untuk menemukan pengajar yang paling "dekat" atau "mirip" dengan kebutuhan pelajar.

## Cara Kerja dalam Sistem

### 1. Input dari Pelajar

Pelajar memberikan input berupa:
- **Lokasi** (latitude, longitude)
- **Mata Pelajaran** yang diinginkan
- **Nilai K** (jumlah pengajar yang ingin ditampilkan, default: 5)

### 2. Perhitungan Jarak

Sistem menghitung jarak antara lokasi pelajar dengan setiap pengajar menggunakan **rumus Haversine**:

```
a = sin²(Δφ/2) + cos φ1 ⋅ cos φ2 ⋅ sin²(Δλ/2)
c = 2 ⋅ atan2(√a, √(1−a))
d = R ⋅ c
```

Dimana:
- φ = latitude (dalam radian)
- λ = longitude (dalam radian)
- R = radius bumi (6371 km)
- d = jarak dalam kilometer

**Contoh Implementasi PHP:**

```php
public function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Jarak dalam km
}
```

### 3. Perhitungan Similarity (Kemiripan)

Sistem menghitung nilai kemiripan berdasarkan 3 faktor dengan bobot berbeda:

```
Similarity = (Jarak × 50%) + (Mata Pelajaran × 30%) + (Pengalaman × 20%)
```

**Breakdown:**

#### A. Similarity Jarak (50% bobot)
```php
$maxDistance = 50; // km
$distanceScore = max(0, (1 - ($distance / $maxDistance)) * 100);
$similarity += $distanceScore * 0.5;
```

Contoh:
- Jarak 0 km → Score 100 → Kontribusi: 100 × 0.5 = 50
- Jarak 10 km → Score 80 → Kontribusi: 80 × 0.5 = 40
- Jarak 25 km → Score 50 → Kontribusi: 50 × 0.5 = 25
- Jarak 50 km → Score 0 → Kontribusi: 0 × 0.5 = 0

#### B. Similarity Mata Pelajaran (30% bobot)
```php
$subjectMatch = stripos($pengajar->mata_pelajaran, $mataPelajaranDicari) !== false;
$similarity += ($subjectMatch ? 100 : 0) * 0.3;
```

Contoh:
- Cocok → Score 100 → Kontribusi: 100 × 0.3 = 30
- Tidak cocok → Score 0 → Kontribusi: 0 × 0.3 = 0

#### C. Similarity Pengalaman (20% bobot)
```php
$maxExperience = 10; // tahun
$experienceScore = min(100, ($pengalaman_tahun / $maxExperience) * 100);
$similarity += $experienceScore * 0.2;
```

Contoh:
- 0 tahun → Score 0 → Kontribusi: 0 × 0.2 = 0
- 5 tahun → Score 50 → Kontribusi: 50 × 0.2 = 10
- 10 tahun → Score 100 → Kontribusi: 100 × 0.2 = 20

### 4. Contoh Perhitungan Lengkap

**Skenario:**
- Pelajar di lokasi: (-3.5410, 118.9710)
- Mencari pengajar Matematika
- K = 5

**Pengajar A:**
- Lokasi: (-3.5403, 118.9707)
- Mata Pelajaran: "Matematika, Fisika"
- Pengalaman: 5 tahun

**Perhitungan:**
1. Jarak = 0.08 km
   - Distance Score = (1 - 0.08/50) × 100 = 99.84
   - Kontribusi = 99.84 × 0.5 = 49.92

2. Mata Pelajaran = Cocok (ada "Matematika")
   - Subject Score = 100
   - Kontribusi = 100 × 0.3 = 30

3. Pengalaman = 5 tahun
   - Experience Score = (5/10) × 100 = 50
   - Kontribusi = 50 × 0.2 = 10

**Total Similarity = 49.92 + 30 + 10 = 89.92**

### 5. Sorting dan Seleksi Top-K

Setelah menghitung similarity untuk semua pengajar:

```php
// Urutkan berdasarkan similarity (tertinggi ke terendah)
usort($results, function ($a, $b) {
    return $b['similarity'] <=> $a['similarity'];
});

// Ambil K terdekat
$topK = array_slice($results, 0, $k);
```

### 6. Penyimpanan Hasil

Hasil rekomendasi disimpan ke database untuk tracking:

```php
Rekomendasi::create([
    'user_id' => $pelajar->id,
    'pengajar_id' => $pengajar->id,
    'nilai_kemiripan' => $similarity,
    'jarak_km' => $distance,
    'tanggal_rekomendasi' => now(),
]);
```

## Keunggulan Algoritma KNN

1. **Sederhana dan Mudah Dipahami**
   - Tidak memerlukan training data yang kompleks
   - Logika mudah dijelaskan kepada pengguna

2. **Adaptif**
   - Dapat disesuaikan dengan menambah/mengurangi faktor
   - Bobot dapat diubah sesuai kebutuhan

3. **Real-time**
   - Perhitungan dilakukan on-demand
   - Hasil selalu up-to-date dengan data terbaru

4. **Transparan**
   - Setiap rekomendasi dapat dijelaskan
   - User dapat melihat mengapa pengajar direkomendasikan

## Optimasi yang Dapat Dilakukan

### 1. Caching
```php
// Cache hasil untuk query yang sama
$cacheKey = "recommendations_{$userId}_{$mataPelajaran}_{$k}";
$recommendations = Cache::remember($cacheKey, 3600, function() {
    return $this->findNearestTeachers(...);
});
```

### 2. Database Indexing
```sql
-- Index untuk mempercepat query
CREATE INDEX idx_pengajar_verifikasi ON pengajar(status_verifikasi);
CREATE INDEX idx_user_location ON users(latitude, longitude);
CREATE INDEX idx_pengajar_mata_pelajaran ON pengajar(mata_pelajaran);
```

### 3. Spatial Indexing (MySQL 8.0+)
```sql
-- Gunakan spatial data types untuk query lokasi lebih cepat
ALTER TABLE users ADD COLUMN location POINT;
CREATE SPATIAL INDEX idx_location ON users(location);
```

### 4. Pre-filtering
```php
// Filter pengajar dalam radius tertentu sebelum perhitungan detail
$nearbyPengajar = Pengajar::whereRaw(
    "ST_Distance_Sphere(
        point(user.longitude, user.latitude),
        point(?, ?)
    ) <= ?",
    [$pelajar->longitude, $pelajar->latitude, 50000] // 50km
)->get();
```

## Variasi Algoritma

### 1. Weighted KNN
Memberikan bobot lebih pada tetangga yang lebih dekat:

```php
$weight = 1 / ($distance + 1); // Hindari division by zero
$weightedSimilarity = $similarity * $weight;
```

### 2. Dynamic K
Menyesuaikan nilai K berdasarkan jumlah data:

```php
$k = min($requestedK, floor($totalPengajar * 0.1)); // Max 10% dari total
```

### 3. Multi-criteria KNN
Menambah lebih banyak kriteria:

```php
$weights = [
    'distance' => 0.4,
    'subject' => 0.25,
    'experience' => 0.15,
    'rating' => 0.15,
    'price' => 0.05,
];
```

## Testing Algoritma

### Unit Test Example
```php
public function testKNNCalculation()
{
    $knnService = new KNNService();
    
    // Test distance calculation
    $distance = $knnService->calculateDistance(
        -3.5410, 118.9710,
        -3.5403, 118.9707
    );
    $this->assertEqualsWithDelta(0.08, $distance, 0.1);
    
    // Test similarity calculation
    $similarity = $knnService->calculateSimilarity(
        $pelajar, $pengajar, 'Matematika'
    );
    $this->assertGreaterThan(80, $similarity);
}
```

## Referensi

1. Cover, T., & Hart, P. (1967). "Nearest neighbor pattern classification"
2. Altman, N. S. (1992). "An introduction to kernel and nearest-neighbor nonparametric regression"
3. Haversine Formula: https://en.wikipedia.org/wiki/Haversine_formula

## Kesimpulan

Algoritma KNN dalam sistem ini memberikan rekomendasi pengajar yang:
- **Relevan** berdasarkan lokasi dan kebutuhan
- **Terukur** dengan nilai similarity yang jelas
- **Dapat dijelaskan** kepada pengguna
- **Efisien** untuk skala data menengah

Sistem dapat dikembangkan lebih lanjut dengan menambah faktor-faktor lain seperti rating, harga, ketersediaan jadwal, dll.
