<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pengajar;
use App\Models\Ulasan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@pengajarprivat.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'alamat' => 'Majene, Sulawesi Barat',
            'no_telepon' => '081234567890',
        ]);

        // Create sample Pengajar
        $pengajar1 = User::create([
            'name' => 'Ahmad Hidayat',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'umur' => 28,
            'alamat' => 'Jl. Sudirman No. 10, Majene',
            'latitude' => -3.5403,
            'longitude' => 118.9707,
            'no_telepon' => '082345678901',
        ]);

        Pengajar::create([
            'user_id' => $pengajar1->id,
            'mata_pelajaran' => 'Matematika, Fisika',
            'pendidikan_terakhir' => 'S1 Pendidikan Matematika',
            'pengalaman_tahun' => 5,
            'deskripsi' => 'Berpengalaman mengajar matematika dan fisika untuk SMP dan SMA. Metode pengajaran yang mudah dipahami.',
            'tarif_per_jam' => 50000,
            'status_verifikasi' => true,
            'keahlian_khusus' => 'Persiapan Ujian Nasional, Olimpiade Matematika',
        ]);

        $pengajar2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'umur' => 25,
            'alamat' => 'Jl. Pattimura No. 25, Majene',
            'latitude' => -3.5450,
            'longitude' => 118.9750,
            'no_telepon' => '083456789012',
        ]);

        Pengajar::create([
            'user_id' => $pengajar2->id,
            'mata_pelajaran' => 'Bahasa Inggris, Bahasa Indonesia',
            'pendidikan_terakhir' => 'S1 Sastra Inggris',
            'pengalaman_tahun' => 3,
            'deskripsi' => 'Lulusan Sastra Inggris dengan pengalaman mengajar conversation dan grammar. Siap membantu persiapan TOEFL.',
            'tarif_per_jam' => 45000,
            'status_verifikasi' => true,
            'keahlian_khusus' => 'TOEFL Preparation, English Conversation',
        ]);

        $pengajar3 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'umur' => 30,
            'alamat' => 'Jl. Ahmad Yani No. 15, Majene',
            'latitude' => -3.5380,
            'longitude' => 118.9680,
            'no_telepon' => '084567890123',
        ]);

        Pengajar::create([
            'user_id' => $pengajar3->id,
            'mata_pelajaran' => 'Pemrograman, Komputer',
            'pendidikan_terakhir' => 'S1 Teknik Informatika',
            'pengalaman_tahun' => 4,
            'deskripsi' => 'Programmer profesional yang siap mengajar dasar-dasar pemrograman (Python, JavaScript, PHP) dan penggunaan komputer.',
            'tarif_per_jam' => 60000,
            'status_verifikasi' => true,
            'keahlian_khusus' => 'Web Development, Python, JavaScript',
        ]);

        $pengajar4 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'umur' => 27,
            'alamat' => 'Jl. Diponegoro No. 8, Majene',
            'latitude' => -3.5420,
            'longitude' => 118.9720,
            'no_telepon' => '085678901234',
        ]);

        Pengajar::create([
            'user_id' => $pengajar4->id,
            'mata_pelajaran' => 'Kimia, Biologi',
            'pendidikan_terakhir' => 'S1 Pendidikan Kimia',
            'pengalaman_tahun' => 3,
            'deskripsi' => 'Mengajar kimia dan biologi untuk tingkat SMP dan SMA. Fokus pada pemahaman konsep dan praktikum.',
            'tarif_per_jam' => 48000,
            'status_verifikasi' => true,
            'keahlian_khusus' => 'Praktikum Kimia, Persiapan SBMPTN',
        ]);

        $pengajar5 = User::create([
            'name' => 'Rudi Hermawan',
            'email' => 'rudi@example.com',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'umur' => 26,
            'alamat' => 'Jl. Veteran No. 20, Majene',
            'latitude' => -3.5390,
            'longitude' => 118.9690,
            'no_telepon' => '086789012345',
        ]);

        Pengajar::create([
            'user_id' => $pengajar5->id,
            'mata_pelajaran' => 'Ekonomi, Akuntansi',
            'pendidikan_terakhir' => 'S1 Ekonomi Pembangunan',
            'pengalaman_tahun' => 2,
            'deskripsi' => 'Fresh graduate yang passionate dalam mengajar ekonomi dan akuntansi. Metode belajar yang fun dan interaktif.',
            'tarif_per_jam' => 40000,
            'status_verifikasi' => false,
            'keahlian_khusus' => 'Akuntansi Dasar, Ekonomi Mikro',
        ]);

        // Create sample Pelajar
        $pelajar1 = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'password' => Hash::make('pelajar123'),
            'role' => 'pelajar',
            'umur' => 17,
            'alamat' => 'Jl. Merdeka No. 5, Majene',
            'latitude' => -3.5410,
            'longitude' => 118.9710,
            'no_telepon' => '087890123456',
        ]);

        $pelajar2 = User::create([
            'name' => 'Maya Sari',
            'email' => 'maya@example.com',
            'password' => Hash::make('pelajar123'),
            'role' => 'pelajar',
            'umur' => 16,
            'alamat' => 'Jl. Pahlawan No. 12, Majene',
            'latitude' => -3.5430,
            'longitude' => 118.9730,
            'no_telepon' => '088901234567',
        ]);

        // Create sample Ulasan
        Ulasan::create([
            'user_id' => $pelajar1->id,
            'pengajar_id' => 1,
            'rating' => 5,
            'komentar' => 'Pak Ahmad sangat sabar dalam mengajar. Penjelasannya mudah dipahami!',
        ]);

        Ulasan::create([
            'user_id' => $pelajar2->id,
            'pengajar_id' => 1,
            'rating' => 4,
            'komentar' => 'Bagus, tapi kadang terlalu cepat menjelaskan.',
        ]);

        Ulasan::create([
            'user_id' => $pelajar1->id,
            'pengajar_id' => 2,
            'rating' => 5,
            'komentar' => 'Bu Siti sangat membantu dalam persiapan TOEFL saya. Recommended!',
        ]);

        // $this->command->info('Database seeded successfully!');
    }
}
