<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class LeastActiveParticipantsTable extends BaseWidget
{

    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 1;
    }

    public function table(Table $table): Table
    {

        return $table
            ->heading('LeastActiveParticipantsTable')
            ->query(
                User::leftJoin('meetings', function ($join) {
                    $join->on('users.id', '=', 'meetings.host_id')
                        ->orOn('users.id', '=', 'meetings.guest_id');
                })
                    ->select('users.*', DB::raw('COUNT(meetings.id) as meetings'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByRaw('meetings')
                    
            )
            ->columns([
                //TextColumn::make('avatar'),
                TextColumn::make('name'),
                TextColumn::make('meetings')->label('total'),
            ])->paginated([5]);
    }
}
