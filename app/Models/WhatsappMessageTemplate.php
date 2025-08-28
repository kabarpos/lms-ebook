<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappMessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'subject',
        'message',
        'variables',
        'is_active',
        'description',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    // Template types constants
    const TYPE_REGISTRATION_VERIFICATION = 'registration_verification';
    const TYPE_ORDER_COMPLETION = 'order_completion';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';

    /**
     * Get template by type
     */
    public static function getByType(string $type)
    {
        return static::where('type', $type)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Replace variables in message with actual values
     */
    public function parseMessage(array $data = []): string
    {
        $message = $this->message;
        
        // Replace variables with actual data
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        
        return $message;
    }

    /**
     * Get available template types
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_REGISTRATION_VERIFICATION => 'Verifikasi Pendaftaran',
            self::TYPE_ORDER_COMPLETION => 'Penyelesaian Order',
            self::TYPE_PAYMENT_RECEIVED => 'Pembayaran Diterima',
        ];
    }

    /**
     * Get default variables for each template type
     */
    public static function getDefaultVariables(string $type): array
    {
        $variables = [
            self::TYPE_REGISTRATION_VERIFICATION => [
                'user_name' => 'Nama pengguna',
                'verification_link' => 'Link verifikasi',
                'app_name' => 'Nama aplikasi',
            ],
            self::TYPE_ORDER_COMPLETION => [
                'user_name' => 'Nama pengguna',
                'order_id' => 'ID Pesanan',
                'course_name' => 'Nama Kursus',
                'total_amount' => 'Total Pembayaran',
                'payment_link' => 'Link Pembayaran',
                'app_name' => 'Nama aplikasi',
            ],
            self::TYPE_PAYMENT_RECEIVED => [
                'user_name' => 'Nama pengguna',
                'order_id' => 'ID Pesanan',
                'course_name' => 'Nama Kursus',
                'total_amount' => 'Total Pembayaran',
                'app_name' => 'Nama aplikasi',
            ],
        ];

        return $variables[$type] ?? [];
    }
}
