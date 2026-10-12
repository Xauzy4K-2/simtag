<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data demo agar dashboard, grafik dan laporan langsung terisi.
 * Jalankan ulang dengan: php artisan migrate:fresh --seed
 */
class DemoDataSeeder extends Seeder
{
    private const FIRST_NAMES_M = ['Ahmad', 'Rudi', 'Hendra', 'Agus', 'Yusuf', 'Fajar', 'Dedi', 'Rizki', 'Bayu', 'Arif', 'Doni', 'Imam', 'Joko', 'Eko', 'Fikri', 'Hafiz', 'Andi', 'Wahyu', 'Teguh', 'Iwan'];

    private const FIRST_NAMES_F = ['Siti', 'Dewi', 'Rina', 'Nurul', 'Fitri', 'Lestari', 'Maya', 'Ayu', 'Indah', 'Yuni', 'Putri', 'Wulan', 'Sri', 'Ratna', 'Lina', 'Melati', 'Aisyah', 'Novi', 'Diah', 'Tari'];

    private const LAST_NAMES = ['Pratama', 'Saputra', 'Hidayat', 'Kurniawan', 'Wijaya', 'Rahman', 'Lestari', 'Susanto', 'Firmansyah', 'Gunawan', 'Halim', 'Syahputra', 'Ramadhan', 'Nugroho', 'Hasibuan', 'Setiawan'];

    private const STREETS = ['Jl. Jenderal Sudirman', 'Jl. Masjid Jamik', 'Jl. Soekarno Hatta', 'Jl. Depati Amir', 'Jl. Mentok', 'Jl. Koba', 'Jl. Kampung Melayu', 'Jl. Air Itam', 'Jl. Selindung', 'Jl. Pasir Padi', 'Jl. Bukit Merapin', 'Jl. Semabung Lama'];

    private const SUGGESTIONS = [
        'Mohon tekanan air di jam sore lebih ditingkatkan.',
        'Pemberitahuan gangguan sebaiknya dikirim lebih awal lewat WhatsApp.',
        'Petugas sangat ramah dan cepat tanggap, pertahankan!',
        'Air kadang keruh setelah hujan deras, mohon dicek filternya.',
        'Aplikasinya mudah digunakan, terima kasih.',
        'Perbaikan pipa bocor di jalan utama agak lama.',
        'Semoga aliran air bisa 24 jam penuh di wilayah kami.',
        'Pelayanan di kantor sudah bagus dan nyaman.',
        'Tolong jadwal pemeliharaan diumumkan di media sosial juga.',
        'Kualitas air sudah jauh lebih baik dibanding tahun lalu.',
    ];

    private const TEXT_ANSWERS = [
        'Tekanan air di malam hari',
        'Kecepatan perbaikan pipa bocor',
        'Informasi gangguan yang lebih cepat',
        'Kejernihan air setelah hujan',
        'Kontinuitas aliran air di musim kemarau',
        'Respon pengaduan melalui telepon',
    ];

    public function run(): void
    {
        mt_srand(2026);

        $customers = $this->createCustomers(45);
        $demo = Customer::where('email', 'budi.pelanggan@example.com')->first();

        $main = Survey::where('title', 'like', 'Survei Kepuasan Pelanggan%')->first();
        $quality = Survey::where('title', 'like', 'Survei Kualitas Air%')->first();
        $old = Survey::where('status', 'closed')->first();

        // Survei utama: tersebar 6 bulan terakhir dengan tren skor yang membaik.
        foreach ($customers->random(36) as $customer) {
            $monthsAgo = mt_rand(0, 5);
            $date = now()->subMonths($monthsAgo)->startOfMonth()->addDays(mt_rand(0, 27))->setTime(mt_rand(7, 21), mt_rand(0, 59));
            $this->respond($main, $customer, min($date, now()), bias: 3.4 + (5 - $monthsAgo) * 0.12);
        }
        $this->respond($main, $demo, now()->subDays(12), bias: 4.1);

        foreach ($customers->random(17) as $customer) {
            $this->respond($quality, $customer, now()->subDays(mt_rand(0, 20))->setTime(mt_rand(7, 21), mt_rand(0, 59)), bias: 3.2);
        }

        foreach ($customers->random(22) as $customer) {
            $this->respond($old, $customer, now()->subMonths(mt_rand(6, 9))->subDays(mt_rand(0, 25)), bias: 3.6);
        }

        $this->createComplaints($customers, $demo);
    }

    private function createCustomers(int $count)
    {
        $customers = collect();
        $districts = Customer::DISTRICTS;
        $password = Hash::make('pelanggan123');

        for ($i = 1; $i <= $count; $i++) {
            $female = mt_rand(0, 1) === 1;
            $first = $female ? self::FIRST_NAMES_F[array_rand(self::FIRST_NAMES_F)] : self::FIRST_NAMES_M[array_rand(self::FIRST_NAMES_M)];
            $name = $first.' '.self::LAST_NAMES[array_rand(self::LAST_NAMES)];
            $email = Str::slug($name, '.').$i.'@example.com';
            $joined = now()->subDays(mt_rand(200, 700));

            $customer = Customer::create([
                'customer_number' => 'TP-'.$joined->format('ym').str_pad((string) (100 + $i), 4, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'email' => $email,
                'phone' => '08'.mt_rand(11, 59).mt_rand(1000000, 9999999),
                'gender' => $female ? 'female' : 'male',
                'district' => $districts[array_rand($districts)],
                'address' => self::STREETS[array_rand(self::STREETS)].' No. '.mt_rand(1, 150).', Pangkalpinang',
                'status' => $i % 15 === 0 ? 'inactive' : 'active',
                'created_at' => $joined,
                'updated_at' => $joined,
            ]);

            User::create([
                'customer_id' => $customer->id,
                'name' => $name,
                'email' => $email,
                'password_hash' => $password,
                'role' => 'customer',
                'status' => 'active',
            ]);

            $customers->push($customer);
        }

        return $customers;
    }

    private function respond(Survey $survey, Customer $customer, Carbon $date, float $bias): void
    {
        if (SurveyResponse::where('survey_id', $survey->id)->where('customer_id', $customer->id)->exists()) {
            return;
        }

        $survey->loadMissing('questions');
        $ratings = [];

        $response = SurveyResponse::create([
            'survey_id' => $survey->id,
            'customer_id' => $customer->id,
            'suggestion' => mt_rand(1, 100) <= 45 ? self::SUGGESTIONS[array_rand(self::SUGGESTIONS)] : null,
            'submitted_at' => $date,
        ]);

        foreach ($survey->questions as $question) {
            if ($question->question_type === 'rating') {
                $value = (int) max(1, min(5, round($bias + (mt_rand(-150, 150) / 100))));
                $ratings[] = $value;
                $response->answers()->create(['question_id' => $question->id, 'answer_value' => $value]);
            } elseif ($question->question_type === 'multiple_choice') {
                $options = $question->options;
                $response->answers()->create(['question_id' => $question->id, 'answer_text' => $options[array_rand($options)]]);
            } elseif (mt_rand(0, 1)) {
                $response->answers()->create(['question_id' => $question->id, 'answer_text' => self::TEXT_ANSWERS[array_rand(self::TEXT_ANSWERS)]]);
            }
        }

        $response->update(['average_score' => $ratings ? round(array_sum($ratings) / count($ratings), 2) : null]);
    }

    private function createComplaints($customers, Customer $demo): void
    {
        $staffId = User::where('role', 'staff')->value('id');
        $samples = [
            ['Air Tidak Mengalir', 'Air mati sejak pagi', 'Sejak pukul 06.00 pagi air tidak mengalir sama sekali di rumah kami dan beberapa tetangga.'],
            ['Air Keruh / Berbau', 'Air keruh kecoklatan', 'Air yang keluar dari keran berwarna kecoklatan dan sedikit berbau setelah hujan deras semalam.'],
            ['Pipa Bocor', 'Pipa bocor di depan rumah', 'Ada kebocoran pipa di bahu jalan depan rumah, air menggenang dan membuat jalan licin.'],
            ['Tekanan Air Lemah', 'Aliran air sangat kecil', 'Tekanan air sangat lemah setiap sore hari sehingga bak mandi sulit terisi.'],
            ['Kebocoran Meter Pelanggan', 'Meter air rembes', 'Sambungan meter air di depan rumah rembes terus-menerus.'],
            ['Pelayanan Petugas', 'Petugas belum datang', 'Sudah dijadwalkan perbaikan kemarin tetapi petugas belum datang.'],
            ['Gangguan Distribusi', 'Gangguan air satu komplek', 'Seluruh komplek perumahan tidak mendapat air sejak dua hari terakhir.'],
            ['Permintaan Penggantian Meter', 'Meter air rusak', 'Jarum meter air tidak bergerak walaupun air mengalir, mohon diganti.'],
        ];
        $responses = [
            'in_progress' => 'Laporan sudah kami terima dan tim teknis sedang menuju lokasi.',
            'resolved' => 'Perbaikan sudah selesai dilakukan. Terima kasih atas laporan Anda.',
            'closed' => 'Pengaduan ditutup setelah dikonfirmasi bersama pelanggan.',
        ];
        $statuses = ['open', 'open', 'in_progress', 'in_progress', 'resolved', 'resolved', 'resolved', 'closed'];

        foreach ($customers->random(15)->push($demo)->values() as $i => $customer) {
            [$category, $subject, $description] = $samples[array_rand($samples)];
            $status = $customer->email === 'budi.pelanggan@example.com' ? 'in_progress' : $statuses[array_rand($statuses)];
            $created = now()->subDays(mt_rand(0, 60))->setTime(mt_rand(7, 20), mt_rand(0, 59));

            Complaint::create([
                'ticket_number' => 'ADU-'.$created->format('ymd').'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'category' => $category,
                'subject' => $subject,
                'description' => $description,
                'location' => $customer->address,
                'status' => $status,
                'admin_response' => $responses[$status] ?? null,
                'handled_by' => $status === 'open' ? null : $staffId,
                'handled_at' => $status === 'open' ? null : $created->copy()->addHours(mt_rand(2, 30)),
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }
    }
}
