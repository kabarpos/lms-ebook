<?php

namespace Database\Seeders;

use App\Models\WhatsappMessageTemplate;
use Illuminate\Database\Seeder;

class WhatsappMessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Verifikasi Pendaftaran',
                'type' => WhatsappMessageTemplate::TYPE_REGISTRATION_VERIFICATION,
                'subject' => 'Verifikasi Akun {app_name}',
                'message' => "Halo {user_name}! 👋\n\nSelamat datang di {app_name}!\n\nUntuk melengkapi proses pendaftaran, silakan verifikasi akun Anda dengan mengklik link berikut:\n\n🔗 {verification_link}\n\n⚠️ Penting: Jika link verifikasi tidak diklik, akun Anda TIDAK AKAN AKTIF dan tidak bisa digunakan untuk login.\n\nJika Anda tidak mendaftar di {app_name}, abaikan pesan ini.\n\nTerima kasih! 🙏",
                'variables' => [
                    'user_name' => 'Nama pengguna yang mendaftar',
                    'verification_link' => 'Link verifikasi untuk aktivasi akun',
                    'app_name' => 'Nama aplikasi LMS',
                ],
                'is_active' => true,
                'description' => 'Template untuk mengirim link verifikasi kepada user yang baru mendaftar. Link ini wajib diklik agar akun menjadi aktif.',
            ],
            [
                'name' => 'Penyelesaian Order',
                'type' => WhatsappMessageTemplate::TYPE_ORDER_COMPLETION,
                'subject' => 'Order Berhasil - {order_id}',
                'message' => "Halo {user_name}! 🎉\n\nTerima kasih telah melakukan pemesanan di {app_name}!\n\n📋 Detail Order:\n• ID Order: {order_id}\n• Kursus: {course_name}\n• Total Pembayaran: {total_amount}\n\n💳 Untuk menyelesaikan pembayaran, silakan klik link berikut:\n{payment_link}\n\n⏰ Segera lakukan pembayaran agar dapat mengakses kursus Anda.\n\nJika ada pertanyaan, jangan ragu untuk menghubungi kami.\n\nTerima kasih! 🙏",
                'variables' => [
                    'user_name' => 'Nama pengguna',
                    'order_id' => 'ID pesanan',
                    'course_name' => 'Nama kursus yang dipesan',
                    'total_amount' => 'Total pembayaran',
                    'payment_link' => 'Link pembayaran',
                    'app_name' => 'Nama aplikasi LMS',
                ],
                'is_active' => true,
                'description' => 'Template untuk notifikasi setelah user menyelesaikan order, berisi detail pesanan dan link pembayaran.',
            ],
            [
                'name' => 'Pembayaran Diterima',
                'type' => WhatsappMessageTemplate::TYPE_PAYMENT_RECEIVED,
                'subject' => 'Pembayaran Diterima - {order_id}',
                'message' => "Halo {user_name}! ✅\n\nKabar gembira! Pembayaran Anda telah kami terima dan dikonfirmasi.\n\n🎊 Detail Pembayaran:\n• ID Order: {order_id}\n• Kursus: {course_name}\n• Total: {total_amount}\n\n🚀 Sekarang Anda sudah bisa mengakses seluruh materi kursus!\n\nAyo mulai belajar dan capai tujuan Anda bersama {app_name}!\n\n📚 Selamat belajar dan semoga sukses!\n\nTerima kasih telah mempercayai kami. 🙏",
                'variables' => [
                    'user_name' => 'Nama pengguna',
                    'order_id' => 'ID pesanan',
                    'course_name' => 'Nama kursus',
                    'total_amount' => 'Total pembayaran',
                    'app_name' => 'Nama aplikasi LMS',
                ],
                'is_active' => true,
                'description' => 'Template untuk ucapan terima kasih setelah pembayaran dikonfirmasi oleh admin.',
            ],
        ];

        foreach ($templates as $template) {
            WhatsappMessageTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}