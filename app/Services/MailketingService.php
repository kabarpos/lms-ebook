<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailketingService
{
    protected ?SmtpSetting $smtpSetting;

    public function __construct()
    {
        $this->smtpSetting = SmtpSetting::getActive();
    }

    /**
     * Whether SMTP service is available and configured
     */
    public function isAvailable(): bool
    {
        return $this->smtpSetting && $this->smtpSetting->isConfigured();
    }

    /**
     * Apply active SMTP configuration to Laravel's mail config.
     */
    public function applyMailConfig(): void
    {
        if (!$this->isAvailable()) {
            return;
        }

        $cfg = [
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $this->smtpSetting->host,
            'mail.mailers.smtp.port' => (int) $this->smtpSetting->port,
            'mail.mailers.smtp.username' => $this->smtpSetting->username,
            'mail.mailers.smtp.password' => $this->smtpSetting->password,
            'mail.mailers.smtp.encryption' => $this->smtpSetting->encryption ?: 'tls',
            'mail.from.address' => $this->smtpSetting->from_email,
            'mail.from.name' => $this->smtpSetting->from_name,
        ];

        foreach ($cfg as $key => $value) {
            Config::set($key, $value);
        }
    }

    /**
     * Send a simple test email to ensure configuration works.
     */
    public function sendTest(?string $toEmail = null): array
    {
        if (!$this->isAvailable()) {
            return [
                'success' => false,
                'message' => 'SMTP belum dikonfigurasi/diaktifkan',
            ];
        }

        $this->applyMailConfig();

        $recipient = $toEmail ?: (Auth::user()->email ?? $this->smtpSetting->from_email);
        try {
            Mail::raw('Tes koneksi SMTP dari sistem ' . config('app.name'), function ($message) use ($recipient) {
                $message->to($recipient);
                $message->subject('Tes SMTP: ' . config('app.name'));
            });

            return [
                'success' => true,
                'message' => 'Email tes berhasil dikirim ke ' . $recipient,
            ];
        } catch (\Exception $e) {
            Log::warning('SMTP test email failed', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Gagal mengirim email tes: ' . $e->getMessage(),
            ];
        }
    }
}