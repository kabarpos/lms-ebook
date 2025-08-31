<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Course;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DiscountService
{
    /**
     * Create a new discount
     */
    public function createDiscount(array $data): Discount
    {
        return Discount::create($data);
    }
    
    /**
     * Update discount
     */
    public function updateDiscount(Discount $discount, array $data): Discount
    {
        $discount->update($data);
        return $discount->fresh();
    }
    
    /**
     * Delete discount
     */
    public function deleteDiscount(Discount $discount): bool
    {
        return $discount->delete();
    }
    
    /**
     * Get all active discounts
     */
    public function getActiveDiscounts(): Collection
    {
        return Discount::active()->available()->get();
    }
    
    /**
     * Find discount by code
     */
    public function findByCode(string $code): ?Discount
    {
        return Discount::where('code', $code)
            ->active()
            ->available()
            ->first();
    }
    
    /**
     * Validate discount for course
     */
    public function validateDiscountForCourse(string $discountCode, Course $course): array
    {
        $discount = $this->findByCode($discountCode);
        
        if (!$discount) {
            return [
                'valid' => false,
                'message' => 'Kode diskon tidak valid atau sudah tidak aktif.',
                'discount' => null
            ];
        }
        
        if (!$discount->isValid($course->price)) {
            return [
                'valid' => false,
                'message' => $this->getInvalidDiscountMessage($discount, $course),
                'discount' => $discount
            ];
        }
        
        $discountAmount = $discount->calculateDiscount($course->price);
        $finalPrice = max(0, $course->price - $discountAmount);
        
        return [
            'valid' => true,
            'message' => 'Kode diskon berhasil diterapkan!',
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'savings' => $course->price - $finalPrice
        ];
    }
    
    /**
     * Apply discount to course
     */
    public function applyDiscountToCourse(Course $course, string $discountCode): array
    {
        $validation = $this->validateDiscountForCourse($discountCode, $course);
        
        if (!$validation['valid']) {
            return $validation;
        }
        
        return [
            'success' => true,
            'original_price' => $course->price,
            'discount_amount' => $validation['discount_amount'],
            'final_price' => $validation['final_price'],
            'discount' => $validation['discount'],
            'savings' => $validation['savings']
        ];
    }
    
    /**
     * Use discount (increment usage count)
     */
    public function useDiscount(Discount $discount): bool
    {
        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return false;
        }
        
        $discount->incrementUsage();
        return true;
    }
    
    /**
     * Get discount statistics
     */
    public function getDiscountStatistics(Discount $discount): array
    {
        return [
            'total_usage' => $discount->used_count,
            'remaining_usage' => $discount->usage_limit ? max(0, $discount->usage_limit - $discount->used_count) : null,
            'usage_percentage' => $discount->usage_limit ? round(($discount->used_count / $discount->usage_limit) * 100, 2) : 0,
            'is_expired' => $discount->end_date ? Carbon::now()->gt($discount->end_date) : false,
            'days_remaining' => $discount->end_date ? max(0, Carbon::now()->diffInDays($discount->end_date, false)) : null
        ];
    }
    
    /**
     * Get courses with active discounts
     */
    public function getCoursesWithDiscounts(): Collection
    {
        return Course::with(['category'])
            ->where('original_price', '>', 0)
            ->whereColumn('original_price', '>', 'price')
            ->get();
    }
    
    /**
     * Generate unique discount code
     */
    public function generateUniqueCode(string $prefix = 'DISC'): string
    {
        do {
            $code = $prefix . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (Discount::where('code', $code)->exists());
        
        return $code;
    }
    
    /**
     * Get invalid discount message
     */
    private function getInvalidDiscountMessage(Discount $discount, Course $course): string
    {
        $reasons = [];
        
        if ($discount->minimum_amount && $course->price < $discount->minimum_amount) {
            $reasons[] = 'Minimum pembelian Rp ' . number_format($discount->minimum_amount, 0, '', '.');
        }
        
        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            $reasons[] = 'Kuota penggunaan sudah habis';
        }
        
        if ($discount->start_date && now() < $discount->start_date) {
            $reasons[] = 'Diskon belum berlaku';
        }
        
        if ($discount->end_date && now() > $discount->end_date) {
            $reasons[] = 'Diskon sudah berakhir';
        }
        
        return 'Kode diskon tidak dapat digunakan: ' . implode(', ', $reasons);
    }
}