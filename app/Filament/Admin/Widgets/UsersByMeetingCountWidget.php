<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class UsersByMeetingCountWidget extends BaseWidget
{

    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    public function table(Table $table): Table
    {

        return $table
            ->heading('Top Meeting Participants')
            ->query(
                User::leftJoin('meetings', function ($join) {
                    $join->on('users.id', '=', 'meetings.host_id')
                        ->orOn('users.id', '=', 'meetings.guest_id');
                })
                    ->select('users.*', DB::raw('COUNT(meetings.id) as total_meetings'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByRaw('total_meetings DESC')
            )
            ->columns([
                //TextColumn::make('avatar'),
                TextColumn::make('name'),
                TextColumn::make('total_meetings')->label('total'),
            ])->paginated([5]);
    }

    /*public static function grid(): array
    {
        return [
            'default' => '1/1', // Widget ocupa 1 coluna por padrão
            //'lg' => '2/2', // Widget ocupa 2 colunas em telas grandes
            //'xl' => '3/3', // Widget ocupa 3 colunas em telas extra grandes
        ];
    }*/
}
/*
  
 
$users = User::withCount(['hostedMeetings', 'guestMeetings'])
    ->select('users.id', 'users.name', 'users.email', DB::raw('
        (COALESCE(hosted_meetings_count, 0) + COALESCE(guest_meetings_count, 0)) as total_meetings
    '))
    ->get();

 
 */