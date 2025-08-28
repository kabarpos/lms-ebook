<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\WhatsappMessageTemplate;
use App\Services\DripsenderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class WhatsappNotificationService
{
    protected $dripsenderService;

    public function __construct(DripsenderService $dripsenderService)
    {
        $this->dripsenderService = $dripsenderService;
    }

    /**
     * Send registration verification notification
     */
    public function sendRegistrationVerification(User $user): array
    {
        try {
            $template = WhatsappMessageTemplate::getByType(WhatsappMessageTemplate::TYPE_REGISTRATION_VERIFICATION);
            
            if (!$template) {
                throw new \Exception('Registration verification template not found');
            }

            if (!$user->whatsapp_number) {
                throw new \Exception('User WhatsApp number is not available');
            }

            // Generate verification token if not exists
            if (!$user->verification_token) {
                $user->generateVerificationToken();
                $user->refresh(); // Refresh model to get updated token
            }

            // Create verification link
            $verificationLink = URL::signedRoute('verification.verify', [
                'id' => $user->id,
                'token' => $user->verification_token
            ]);

            $messageData = [
                'user_name' => $user->name,
                'verification_link' => $verificationLink,
                'app_name' => config('app.name', 'LMS Ebook'),
            ];

            $message = $template->parseMessage($messageData);

            $result = $this->dripsenderService->sendMessage(
                $user->whatsapp_number,
                $message
            );

            Log::info('Registration verification WhatsApp sent', [
                'user_id' => $user->id,
                'phone' => $user->whatsapp_number,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send registration verification WhatsApp', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send verification message',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send order completion notification
     */
    public function sendOrderCompletion(Transaction $transaction): array
    {
        try {
            $template = WhatsappMessageTemplate::getByType(WhatsappMessageTemplate::TYPE_ORDER_COMPLETION);
            
            if (!$template) {
                throw new \Exception('Order completion template not found');
            }

            $user = $transaction->user;
            if (!$user || !$user->whatsapp_number) {
                throw new \Exception('User or WhatsApp number is not available');
            }

            // Get course details
            $pricing = $transaction->pricing;
            $courseName = $pricing ? $pricing->name : 'Unknown Course';

            // Create payment link (assuming you have a payment route)
            $paymentLink = route('front.checkout', ['transaction' => $transaction->id]);

            $messageData = [
                'user_name' => $user->name,
                'order_id' => $transaction->id,
                'course_name' => $courseName,
                'total_amount' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                'payment_link' => $paymentLink,
                'app_name' => config('app.name', 'LMS Ebook'),
            ];

            $message = $template->parseMessage($messageData);

            $result = $this->dripsenderService->sendMessage(
                $user->whatsapp_number,
                $message
            );

            Log::info('Order completion WhatsApp sent', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'phone' => $user->whatsapp_number,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send order completion WhatsApp', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send order completion message',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceived(Transaction $transaction): array
    {
        try {
            $template = WhatsappMessageTemplate::getByType(WhatsappMessageTemplate::TYPE_PAYMENT_RECEIVED);
            
            if (!$template) {
                throw new \Exception('Payment received template not found');
            }

            $user = $transaction->user;
            if (!$user || !$user->whatsapp_number) {
                throw new \Exception('User or WhatsApp number is not available');
            }

            // Get course details
            $pricing = $transaction->pricing;
            $courseName = $pricing ? $pricing->name : 'Unknown Course';

            $messageData = [
                'user_name' => $user->name,
                'order_id' => $transaction->id,
                'course_name' => $courseName,
                'total_amount' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                'app_name' => config('app.name', 'LMS Ebook'),
            ];

            $message = $template->parseMessage($messageData);

            $result = $this->dripsenderService->sendMessage(
                $user->whatsapp_number,
                $message
            );

            Log::info('Payment received WhatsApp sent', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'phone' => $user->whatsapp_number,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send payment received WhatsApp', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send payment received message',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send custom WhatsApp message
     */
    public function sendCustomMessage(string $phone, string $message, ?string $mediaUrl = null): array
    {
        try {
            // Validate phone number
            if (!$this->dripsenderService->validatePhoneNumber($phone)) {
                throw new \Exception('Invalid phone number format');
            }

            $result = $this->dripsenderService->sendMessage($phone, $message, $mediaUrl);

            Log::info('Custom WhatsApp message sent', [
                'phone' => $phone,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send custom WhatsApp message', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send custom message',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk messages to multiple users
     */
    public function sendBulkMessage(array $userIds, string $message, ?string $mediaUrl = null): array
    {
        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user || !$user->whatsapp_number) {
                    $results[$userId] = [
                        'success' => false,
                        'message' => 'User not found or WhatsApp number not available'
                    ];
                    $failureCount++;
                    continue;
                }

                $result = $this->dripsenderService->sendMessage(
                    $user->whatsapp_number,
                    $message,
                    $mediaUrl
                );

                $results[$userId] = $result;
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                }

            } catch (\Exception $e) {
                $results[$userId] = [
                    'success' => false,
                    'message' => 'Exception occurred',
                    'error' => $e->getMessage()
                ];
                $failureCount++;
            }
        }

        Log::info('Bulk WhatsApp messages sent', [
            'total_users' => count($userIds),
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ]);

        return [
            'success' => $successCount > 0,
            'summary' => [
                'total' => count($userIds),
                'success' => $successCount,
                'failure' => $failureCount
            ],
            'details' => $results
        ];
    }

    /**
     * Test WhatsApp connection and configuration
     */
    public function testConnection(): array
    {
        try {
            // Use DripsenderService's comprehensive test connection method
            return $this->dripsenderService->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to test WhatsApp connection',
                'error' => $e->getMessage()
            ];
        }
    }
}