<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'admin')->value('id');

        $main = Survey::create([
            'title' => 'Survei Kepuasan Pelanggan PDAM Tirta Pinang 2026',
            'description' => 'Bantu kami meningkatkan kualitas layanan air bersih di Kota Pangkalpinang. Pengisian hanya sekitar 3 menit dan jawaban Anda dijaga kerahasiaannya.',
            'status' => 'published',
            'published_at' => now()->subMonths(5)->startOfMonth(),
            'ends_at' => now()->addMonths(2)->endOfMonth(),
            'created_by' => $adminId,
        ]);

        $ratingQuestions = [
            'Bagaimana kualitas kejernihan air bersih di rumah Anda?',
            'Bagaimana kelancaran tekanan aliran air (pancaran air keran)?',
            'Bagaimana kontinuitas aliran air selama 24 jam?',
            'Bagaimana kecepatan respon layanan pengaduan?',
            'Bagaimana sikap dan keramahan petugas saat membantu Anda?',
            'Bagaimana kebersihan dan kenyamanan kantor pelayanan pelanggan?',
            'Seberapa puas Anda dengan jadwal pemeliharaan dan pemberitahuan gangguan?',
            'Bagaimana kemudahan memperoleh informasi layanan PDAM?',
            'Seberapa efisien proses pemasangan/penyambungan baru?',
            'Bagaimana kualitas air selama musim kemarau atau cuaca ekstrem?',
            'Bagaimana kesan Anda terhadap aplikasi SIMTAG secara keseluruhan?',
            'Seberapa besar kemungkinan Anda merekomendasikan layanan PDAM kepada tetangga?',
        ];

        $order = 1;
        foreach ($ratingQuestions as $text) {
            $main->questions()->create([
                'question_text' => $text,
                'question_type' => 'rating',
                'display_order' => $order++,
                'is_required' => true,
            ]);
        }

        $main->questions()->create([
            'question_text' => 'Melalui media apa Anda paling sering mendapatkan informasi gangguan air?',
            'question_type' => 'multiple_choice',
            'options' => ['WhatsApp / SMS', 'Media sosial PDAM', 'Website resmi', 'Petugas lapangan', 'Tetangga / RT'],
            'display_order' => $order++,
            'is_required' => true,
        ]);

        $main->questions()->create([
            'question_text' => 'Apa satu hal yang paling perlu diperbaiki dari layanan PDAM Tirta Pinang?',
            'question_type' => 'text',
            'display_order' => $order++,
            'is_required' => false,
        ]);

        $quality = Survey::create([
            'title' => 'Survei Kualitas Air Musim Kemarau',
            'description' => 'Survei singkat untuk memetakan kualitas dan ketersediaan air di setiap kecamatan selama musim kemarau.',
            'status' => 'published',
            'published_at' => now()->subWeeks(3),
            'ends_at' => now()->addWeeks(3),
            'created_by' => $adminId,
        ]);

        $quality->questions()->createMany([
            ['question_text' => 'Bagaimana kondisi warna dan kejernihan air dalam 2 minggu terakhir?', 'question_type' => 'rating', 'display_order' => 1, 'is_required' => true],
            ['question_text' => 'Bagaimana bau dan rasa air yang Anda terima?', 'question_type' => 'rating', 'display_order' => 2, 'is_required' => true],
            ['question_text' => 'Berapa jam rata-rata air mengalir setiap hari?', 'question_type' => 'multiple_choice', 'options' => ['Kurang dari 6 jam', '6 - 12 jam', '12 - 18 jam', 'Lebih dari 18 jam'], 'display_order' => 3, 'is_required' => true],
            ['question_text' => 'Pada jam berapa aliran air paling lemah?', 'question_type' => 'multiple_choice', 'options' => ['Pagi (05.00-09.00)', 'Siang (09.00-15.00)', 'Sore (15.00-19.00)', 'Malam (19.00-05.00)'], 'display_order' => 4, 'is_required' => false],
            ['question_text' => 'Ceritakan kendala air yang Anda alami (jika ada).', 'question_type' => 'text', 'display_order' => 5, 'is_required' => false],
        ]);

        $old = Survey::create([
            'title' => 'Survei Layanan Petugas Lapangan 2025',
            'description' => 'Evaluasi kinerja petugas lapangan dalam penanganan gangguan dan pemasangan baru.',
            'status' => 'closed',
            'published_at' => now()->subMonths(10),
            'closed_at' => now()->subMonths(6),
            'created_by' => $adminId,
        ]);

        $old->questions()->createMany([
            ['question_text' => 'Bagaimana ketepatan waktu kedatangan petugas?', 'question_type' => 'rating', 'display_order' => 1, 'is_required' => true],
            ['question_text' => 'Bagaimana kerapian hasil pekerjaan petugas?', 'question_type' => 'rating', 'display_order' => 2, 'is_required' => true],
            ['question_text' => 'Bagaimana komunikasi petugas selama penanganan?', 'question_type' => 'rating', 'display_order' => 3, 'is_required' => true],
        ]);

        $draft = Survey::create([
            'title' => 'Survei Minat Layanan Digital',
            'description' => 'Rencana survei untuk mengukur minat pelanggan terhadap notifikasi digital.',
            'status' => 'draft',
            'created_by' => $adminId,
        ]);

        $draft->questions()->create([
            'question_text' => 'Apakah Anda tertarik menerima notifikasi gangguan melalui WhatsApp?',
            'question_type' => 'multiple_choice',
            'options' => ['Sangat tertarik', 'Tertarik', 'Biasa saja', 'Tidak tertarik'],
            'display_order' => 1,
            'is_required' => true,
        ]);
    }
}
