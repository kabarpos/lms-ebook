<?php

namespace App\Services;

use App\Helpers\TransactionHelper;
use App\Models\Pricing;
use App\Repositories\PricingRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $midtransService;
    protected $pricingRepository;
    protected $transactionRepository;

    public function __construct(
        MidtransService $midtransService,
        PricingRepositoryInterface $pricingRepository,
        TransactionRepositoryInterface $transactionRepository
    )
    {
        $this->midtransService = $midtransService;
        $this->pricingRepository = $pricingRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function createPayment(int $pricingId)
    {
        $user = Auth::user();
        // $pricing = Pricing::findOrFail($pricingId);
        $pricing = $this->pricingRepository->findById($pricingId);

        $tax = 0.11;
        $totalTax = $pricing->price * $tax;
        $grandTotal = $pricing->price + $totalTax;

        $params = [
            'transaction_details' => [
                'order_id' => TransactionHelper::generateUniqueTrxId(),
                'gross_amount' => (int) $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => '089998501293218'
            ],
            'item_details' => [
                [
                    'id' => $pricing->id,
                    'price' => (int) $pricing->price,
                    'quantity' => 1,
                    'name' => $pricing->name,
                ],
                [
                    'id' => 'tax',
                    'price' => (int) $totalTax,
                    'quantity' => 1,
                    'name' => 'PPN 11%',
                ],
            ],
            'custom_field1' => $user->id,
            'custom_field2' => $pricingId,
        ];

        return $this->midtransService->createSnapToken($params);

    }

    public function handlePaymentNotification()
    {
        Log::info('Processing Midtrans notification...');
        
        $notification = $this->midtransService->handleNotification();
        
        Log::info('Received Midtrans notification:', $notification);

        if (in_array($notification['transaction_status'], ['capture', 'settlement'])) {
            Log::info('Transaction status is valid for processing: ' . $notification['transaction_status']);
            
            $pricing = Pricing::findOrFail($notification['custom_field2']);
            
            Log::info('Found pricing:', ['id' => $pricing->id, 'name' => $pricing->name]);
            
            $result = $this->createTransaction($notification, $pricing);
            
            Log::info('Transaction creation result:', ['success' => $result !== null]);
        } else {
            Log::warning('Transaction status not processed: ' . $notification['transaction_status']);
        }

        return $notification['transaction_status'];
    }

    protected function createTransaction(array $notification, Pricing $pricing)
    {
        Log::info('Creating transaction with data:', $notification);
        
        $startedAt = now();
        $endedAt = $startedAt->copy()->addMonths($pricing->duration);

        $transactionData = [
            'user_id' => $notification['custom_field1'],
            'pricing_id' => $notification['custom_field2'],
            'sub_total_amount' => $pricing->price,
            'total_tax_amount' => $pricing->price * 0.11,
            'grand_total_amount' => $notification['gross_amount'],
            'payment_type' => 'Midtrans',
            'is_paid' => true,
            'booking_trx_id' => $notification['order_id'],
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
        
        Log::info('Transaction data to be created:', $transactionData);

        try {
            $transaction = $this->transactionRepository->create($transactionData);
            
            Log::info('Transaction successfully created:', [
                'id' => $transaction->id,
                'booking_trx_id' => $transaction->booking_trx_id,
                'user_id' => $transaction->user_id
            ]);
            
            return $transaction;
        } catch (Exception $e) {
            Log::error('Failed to create transaction:', [
                'error' => $e->getMessage(),
                'data' => $transactionData
            ]);
            throw $e;
        }
    }

}
