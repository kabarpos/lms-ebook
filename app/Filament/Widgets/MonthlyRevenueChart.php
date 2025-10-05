<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends LineChartWidget
{
    protected ?string $heading = 'Pendapatan Bulanan';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil 12 bulan terakhir
        $months = collect(range(0, 11))
            ->map(fn ($i) => Carbon::now()->subMonths(11 - $i))
            ->map(fn ($date) => $date->format('Y-m'));

        // Query sum pendapatan per bulan
        $rows = Transaction::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('SUM(grand_total_amount) as total'),
            ])
            ->where('is_paid', true)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = $months->map(fn ($ym) => Carbon::createFromFormat('Y-m', $ym)->locale('id')->translatedFormat('M Y'));
        $data = $months->map(fn ($ym) => (float) ($rows[$ym]->total ?? 0));

        return [
            'labels' => $labels->toArray(),
            'datasets' => [
                [
                    'label' => 'Pendapatan (IDR)',
                    'data' => $data->toArray(),
                    'borderColor' => '#06b6d4',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.2)',
                    'tension' => 0.3,
                ],
            ],
        ];
    }
}