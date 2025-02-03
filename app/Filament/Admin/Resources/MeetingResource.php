<?php

namespace App\Filament\Admin\Resources;

use App\Actions\TransferAction;
use App\Enums\MeetingStatusEnum;
use App\Filament\Admin\Resources\MeetingResource\Pages;
use App\Filament\Admin\Resources\MeetingResource\RelationManagers\TransactionsRelationManager;
use App\Helpers\EnumHelper;
use App\Models\Meeting;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('host_id')
                    ->default(Auth::id())
                    ->required(),
                Select::make('guest_id')
                    ->relationship('userGuest', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->required()
                    ->native(false),
                RichEditor::make('description'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                TextColumn::make('userHost.name')
                    //                    ->searchable()
                    ->sortable(),
                TextColumn::make('userGuest.name')
                    //                    ->searchable()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable()
                    //                    ->searchable()
                    ->alignment(Alignment::Center),
                TextColumn::make('status')
                    ->badge()
                    //                    ->searchable()
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    })
                    ->alignment(Alignment::Center)
            ])
            ->filters([
                DateRangeFilter::make('scheduled_at')
                    ->label('scheduled_at'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(EnumHelper::toAssociativeArray(MeetingStatusEnum::class)),
                SelectFilter::make('atendees')
                    ->options(User::all()->pluck('name', 'id'))
                    ->default([Auth::id()])
                    ->multiple()
                    ->searchable()
                    ->query(function ($query, array $data): Builder {

                        return $query->when(
                            collect($data['values'])->isNotEmpty(),
                            function (Builder $query)  use ($data) {

                                return $query->whereIn('host_id', $data['values'])->orWhereIn('guest_id', $data['values']);
                            }

                        );
                    }),
                SelectFilter::make('host')
                    ->relationship('userHost', 'name')
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('guest')
                    ->relationship('userGuest', 'name')
                    ->multiple()
                    ->searchable()
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->slideOver(),
                    EditAction::make()
                        ->visible(function (Meeting $meeting) {
                            return $meeting->host_id == Auth::id() && $meeting->status === (MeetingStatusEnum::SCHEDULED);
                        })
                        ->color('primary')
                        ->slideOver(),
                    Action::make('Cancel')
                        ->visible(function (Meeting $meeting) {
                            return $meeting->status === (MeetingStatusEnum::SCHEDULED) &&
                                ($meeting->host_id == Auth::id() || $meeting->guest_id == Auth::id());
                        })
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Meeting $meeting) {
                            $meeting->cancel();
                        })
                        ->after(function () {
                            Notification::make()->success()->title('Meeting Canceled')
                                ->duration(5000)
                                ->color('success')
                                ->body('This meeting now is cancceled and not will be possible to reopen.')
                                ->send();
                        }),
                    Action::make('Transfer')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->modalHeading('Register Tranfer')
                        ->modalSubmitActionLabel('Confirm')
                        ->modalSubmitActionLabel('Confirm')
                        ->visible(function (Meeting $meeting): bool {
                            return ($meeting->host_id === Auth::id() || $meeting->guest_id === Auth::id()) && $meeting->status == MeetingStatusEnum::FINISHED;
                        })
                        ->form([
                            TextInput::make('amount')
                            ->label('amount')
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                            TextInput::make('description')
                                ->label('description')

                        ])
                        ->action(function (Meeting $meeting, array $data): void {
                            TransferAction::run($meeting, Auth::id(), $data['amount'], $data['description']);

                            Notification::make()->success()->title('Registered Transfer')
                                ->duration(5000)
                                ->color('success')
                                ->send();
                        })
                ])
            ])
            ->headerActions([
                //ExportAction::make()
                //->exporter(ProductExporter::class)
            ])
            ->defaultSort('scheduled_at', 'desc');;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Main Information')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('Host')
                            ->getStateUsing(function (Meeting $meeting) {
                                return $meeting->userHost->name;
                            }),

                        TextEntry::make('Guest')
                            ->getStateUsing(function (Meeting $meeting) {
                                return $meeting->userGuest->name;
                            }),

                        TextEntry::make('Scheduled At')
                            ->getStateUsing(function (Meeting $meeting) {
                                return $meeting->scheduled_at->format('Y-m-d H:i');
                            }),

                        TextEntry::make('description')
                            ->html(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetings::route('/'),
            //'create' => Pages\CreateMeeting::route('/create'),
            //'edit' => Pages\EditMeeting::route('/{record}/edit'),
            'view' => Pages\ViewMeeting::route('/{record}/view')
        ];
    }
}
