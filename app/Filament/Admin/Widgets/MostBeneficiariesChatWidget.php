<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class MostBeneficiariesChatWidget extends ChartWidget
{
    protected static ?string $heading = 'Users More Beneficiaries';

    protected function getData(): array
    {
        $topContributors = Transaction::selectRaw('to_id, SUM(amount) as total_amount')
            ->groupBy('to_id')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        $userNames = User::whereIn('id', $topContributors->pluck('to_id'))
            ->pluck('name', 'id');

        return [
            'datasets' => [
                [
                    'label' => 'Transaction Value',
                    'data' => $topContributors->map(fn($contributor) => $contributor->total_amount)->toArray(),
                ],
            ],
            'labels' => $topContributors->map(fn($contributor) => $userNames[$contributor->to_id] ?? 'Unknown')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
