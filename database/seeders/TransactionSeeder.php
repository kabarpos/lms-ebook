<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Pricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::role('student')->get();
        $pricings = Pricing::all();

        // Create 8 transactions with different statuses
        $transactionsData = [
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'credit_card',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(30),
                'pricing_duration' => 3,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'bank_transfer',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(60),
                'pricing_duration' => 6,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => false,
                'payment_type' => 'e_wallet',
                'proof' => null,
                'started_at' => now()->subDays(5),
                'pricing_duration' => 1,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'credit_card',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(90),
                'pricing_duration' => 12,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'bank_transfer',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(15),
                'pricing_duration' => 1,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => false,
                'payment_type' => 'e_wallet',
                'proof' => null,
                'started_at' => now()->subDays(2),
                'pricing_duration' => 3,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'credit_card',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(45),
                'pricing_duration' => 6,
            ],
            [
                'booking_trx_id' => 'TRX' . strtoupper(Str::random(8)),
                'is_paid' => true,
                'payment_type' => 'bank_transfer',
                'proof' => 'https://via.placeholder.com/400x600/22c55e/ffffff?text=Payment+Proof',
                'started_at' => now()->subDays(10),
                'pricing_duration' => 1,
            ],
        ];

        foreach ($transactionsData as $transactionData) {
            // Get random student and pricing based on duration
            $randomStudent = $students->random();
            $pricing = $pricings->where('duration', $transactionData['pricing_duration'])->first();
            
            if (!$pricing) {
                $pricing = $pricings->random();
            }

            $subTotal = $pricing->price;
            $taxRate = 0.11; // 11% PPN
            $taxAmount = (int) ($subTotal * $taxRate);
            $grandTotal = $subTotal + $taxAmount;

            $startDate = $transactionData['started_at'];
            $endDate = (clone $startDate)->modify("+{$pricing->duration} months");

            Transaction::create([
                'booking_trx_id' => $transactionData['booking_trx_id'],
                'user_id' => $randomStudent->id,
                'pricing_id' => $pricing->id,
                'sub_total_amount' => $subTotal,
                'grand_total_amount' => $grandTotal,
                'total_tax_amount' => $taxAmount,
                'is_paid' => $transactionData['is_paid'],
                'payment_type' => $transactionData['payment_type'],
                'proof' => $transactionData['proof'],
                'started_at' => $startDate,
                'ended_at' => $endDate,
            ]);
        }
    }
}