<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class MoreContributedChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Users More Contributed';

    protected function getData(): array
    {
        $topContributors = Transaction::selectRaw('from_id, SUM(amount) as total_amount')
            ->groupBy('from_id')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        $userNames = User::whereIn('id', $topContributors->pluck('from_id'))
            ->pluck('name', 'id');

        return [
            'datasets' => [
                [
                    'label' => 'Transaction Value',
                    'data' => $topContributors->map(fn($contributor) => $contributor->total_amount)->toArray(),
                ],
            ],
            'labels' => $topContributors->map(fn($contributor) => $userNames[$contributor->from_id] ?? 'Unknown')->toArray(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
