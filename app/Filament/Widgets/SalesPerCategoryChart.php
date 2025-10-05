<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class SalesPerCategoryChart extends BarChartWidget
{
    protected ?string $heading = 'Penjualan per Kategori';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Hitung transaksi terbayar per kategori course
        $rows = DB::table('transactions')
            ->join('courses', 'transactions.course_id', '=', 'courses.id')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->where('transactions.is_paid', true)
            ->select('categories.name as category', DB::raw('COUNT(*) as total'))
            ->groupBy('categories.name')
            ->orderBy('total', 'desc')
            ->get();

        $labels = $rows->pluck('category')->toArray();
        $data = $rows->pluck('total')->map(fn ($v) => (int) $v)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $data,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                ],
            ],
        ];
    }
}