<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Meeting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPanelStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userGrowthData = $this->getUserGrowthData();
        $transactionGrowthData = $this->getTransactionGrowthData();
        $meetingGrowthData = $this->getMeetingGrowthData();

        return [
            Stat::make('Users', User::count())
                ->description('Total number of users')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->chart($userGrowthData),
            Stat::make('Meetings', Meeting::count())
                ->description('Total meetings between users')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('gray')
                ->chart($meetingGrowthData),
            Stat::make('Transactions', 'R$ ' . (Transaction::sum('amount')))
                ->description('Total transactions exchange between users')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart($transactionGrowthData),
        ];
    }

    private function getTransactionGrowthData(): array
    {
        $transactionCount = Transaction::count();

        if ($transactionCount === 0) {
            return []; // Retorna vazio se não houver transações
        }

        $firstTransaction = Transaction::orderBy('created_at', 'asc')->first()->created_at;
        $lastTransaction = Transaction::orderBy('created_at', 'desc')->first()->created_at;

        $duration = $firstTransaction->diffInDays($lastTransaction);

        if ($duration <= 1) {
            $interval = 'hour';
        } elseif ($duration <= 7) {
            $interval = 'day';
        } elseif ($duration <= 30) {
            $interval = 'week';
        } elseif ($duration <= 365) {
            $interval = 'month';
        } else {
            $interval = 'year';
        }

        return $this->fetchTransactionGrowthData($interval, $firstTransaction, $lastTransaction);
    }

    private function fetchTransactionGrowthData(string $interval, Carbon $startDate, Carbon $endDate): array
    {
        switch ($interval) {
            case 'hour':
                $format = "%Y-%m-%d %H:00:00"; // Formata para horas
                break;
            case 'day':
                $format = "%Y-%m-%d"; // Formata para dias
                break;
            case 'week':
                $format = "%u-%Y"; // Formata para semanas
                return Transaction::selectRaw("YEARWEEK(created_at, 1) as period, SUM(amount) as total")
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('total')
                    ->toArray();
            case 'month':
                $format = "%Y-%m"; // Formata para meses
                break;
            case 'year':
                $format = "%Y"; // Formata para anos
                break;
            default:
                throw new \InvalidArgumentException("Invalid interval: $interval");
        }

        return Transaction::selectRaw("DATE_FORMAT(created_at, '$format') as period, SUM(amount) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total')
            ->toArray();
    }

    private function getUserGrowthData(): array
    {
        $userCount = User::count();

        if ($userCount === 0) {
            return []; // Retorna vazio se não houver usuários
        }

        $firstUser = User::orderBy('created_at', 'asc')->first()->created_at;
        $lastUser = User::orderBy('created_at', 'desc')->first()->created_at;

        $duration = $firstUser->diffInDays($lastUser);

        if ($duration <= 1) {
            $interval = 'hour';
        } elseif ($duration <= 7) {
            $interval = 'day';
        } elseif ($duration <= 30) {
            $interval = 'week';
        } elseif ($duration <= 365) {
            $interval = 'month';
        } else {
            $interval = 'year';
        }

        return $this->fetchGrowthData($interval, $firstUser, $lastUser);
    }

    private function fetchGrowthData(string $interval, Carbon $startDate, Carbon $endDate): array
    {
        switch ($interval) {
            case 'hour':
                $format = "%Y-%m-%d %H:00:00"; // Formata para horas
                break;
            case 'day':
                $format = "%Y-%m-%d"; // Formata para dias
                break;
            case 'week':
                $format = "%u-%Y"; // Formata para semanas
                return User::selectRaw("YEARWEEK(created_at, 1) as period, COUNT(*) as count")
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count')
                    ->toArray();
            case 'month':
                $format = "%Y-%m"; // Formata para meses
                break;
            case 'year':
                $format = "%Y"; // Formata para anos
                break;
            default:
                throw new \InvalidArgumentException("Invalid interval: $interval");
        }

        return User::selectRaw("DATE_FORMAT(created_at, '$format') as period, COUNT(*) as count")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count')
            ->toArray();
    }

    private function getMeetingGrowthData(): array
    {
        $meetingCount = Meeting::count();

        if ($meetingCount === 0) {
            return []; // Retorna vazio se não houver reuniões
        }

        $firstMeeting = Meeting::orderBy('created_at', 'asc')->first()->created_at;
        $lastMeeting = Meeting::orderBy('created_at', 'desc')->first()->created_at;

        $duration = $firstMeeting->diffInDays($lastMeeting);

        if ($duration <= 1) {
            $interval = 'hour';
        } elseif ($duration <= 7) {
            $interval = 'day';
        } elseif ($duration <= 30) {
            $interval = 'week';
        } elseif ($duration <= 365) {
            $interval = 'month';
        } else {
            $interval = 'year';
        }

        return $this->fetchMeetingGrowthData($interval, $firstMeeting, $lastMeeting);
    }

    private function fetchMeetingGrowthData(string $interval, Carbon $startDate, Carbon $endDate): array
    {
        switch ($interval) {
            case 'hour':
                $format = "%Y-%m-%d %H:00:00"; // Formata para horas
                break;
            case 'day':
                $format = "%Y-%m-%d"; // Formata para dias
                break;
            case 'week':
                $format = "%u-%Y"; // Formata para semanas
                return Meeting::selectRaw("YEARWEEK(created_at, 1) as period, COUNT(*) as count")
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count')
                    ->toArray();
            case 'month':
                $format = "%Y-%m"; // Formata para meses
                break;
            case 'year':
                $format = "%Y"; // Formata para anos
                break;
            default:
                throw new \InvalidArgumentException("Invalid interval: $interval");
        }

        return Meeting::selectRaw("DATE_FORMAT(created_at, '$format') as period, COUNT(*) as count")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count')
            ->toArray();
    }
}
