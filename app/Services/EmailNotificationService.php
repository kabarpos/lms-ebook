<?php

namespace App\Services;

use App\Models\EmailMessageTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    protected MailketingService $mailketing;

    public function __construct(MailketingService $mailketing)
    {
        $this->mailketing = $mailketing;
    }

    /**
     * Send registration verification email using templated content
     */
    public function sendRegistrationVerification(User $user): array
    {
        try {
            // Ensure SMTP is applied
            $this->mailketing->applyMailConfig();

            // Generate token if missing
            if (!$user->verification_token) {
                $user->generateVerificationToken();
                $user->refresh();
            }

            $template = EmailMessageTemplate::getByType(EmailMessageTemplate::TYPE_REGISTRATION_VERIFICATION);

            if (!$template) {
                // Fallback: use existing mailable view if template not found
                Mail::to($user->email)->send(new \App\Mail\RegistrationEmailVerification($user));
                return [
                    'success' => true,
                    'message' => 'Registration email sent via fallback mailable',
                    'template' => 'fallback_view'
                ];
            }

            $verificationLink = route('email.verification.verify', [
                'id' => $user->id,
                'token' => $user->verification_token,
            ]);

            $data = [
                'user_name' => $user->name,
                'verification_link' => $verificationLink,
                'app_name' => config('app.name', 'LMS Ebook'),
            ];

            $subject = $template->subject ?: ('Verifikasi Akun ' . config('app.name', 'LMS Ebook'));
            $message = $template->parseMessage($data);

            // Decide to send HTML or plain text based on content heuristics
            if (strip_tags($message) !== $message) {
                Mail::to($user->email)->send(new class($subject, $message) extends \Illuminate\Mail\Mailable {
                    public function __construct(private string $subjectText, private string $htmlBody) {}
                    public function build() {
                        return $this->subject($this->subjectText)->html($this->htmlBody);
                    }
                });
            } else {
                Mail::raw($message, function ($mail) use ($user, $subject) {
                    $mail->to($user->email)->subject($subject);
                });
            }

            Log::info('Registration email verification sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'template_type' => $template->type,
            ]);

            return [
                'success' => true,
                'message' => 'Registration verification email sent',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send registration verification email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send verification email',
                'error' => $e->getMessage(),
            ];
        }
    }
}