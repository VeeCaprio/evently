<?php

namespace App\Filament\Admin\Resources;

use App\Enums\EventStatus;
use App\Filament\Admin\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Events';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Event::query()->published()->upcoming()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'location', 'organizer'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category?->name ?? 'Uncategorized',
            'Starts' => $record->start_at?->format('d M Y, H:i'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event details')
                ->description('The essentials guests will see on the public event page.')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(160)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $set('slug', Str::slug((string) $state));
                        })
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(180)
                        ->unique(ignoreRecord: true)
                        ->prefixIcon('heroicon-m-link')
                        ->helperText('Used in the public event URL. Generated from the title, editable anytime.'),
                    Forms\Components\Select::make('event_category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(80)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(90)
                                ->unique('event_categories', 'slug'),
                            Forms\Components\Textarea::make('description')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->createOptionModalHeading('Create category'),
                    Forms\Components\TextInput::make('organizer')
                        ->maxLength(160)
                        ->prefixIcon('heroicon-m-building-office-2'),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(9)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Banner')
                ->description('A wide image works best. Recommended size: 1600 × 900 px.')
                ->schema([
                    Forms\Components\FileUpload::make('banner')
                        ->image()
                        ->disk('public')
                        ->directory('events')
                        ->visibility('public')
                        ->imageEditor()
                        ->imagePreviewHeight('220')
                        ->panelAspectRatio('16/9')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->helperText('JPG, PNG, or WebP up to 4 MB.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Schedule, venue & tickets')
                ->schema([
                    Forms\Components\DateTimePicker::make('start_at')
                        ->required()
                        ->seconds(false)
                        ->minutesStep(15)
                        ->displayFormat('d M Y, H:i')
                        ->prefixIcon('heroicon-m-play'),
                    Forms\Components\DateTimePicker::make('end_at')
                        ->required()
                        ->seconds(false)
                        ->minutesStep(15)
                        ->displayFormat('d M Y, H:i')
                        ->after('start_at')
                        ->prefixIcon('heroicon-m-stop'),
                    Forms\Components\TextInput::make('location')
                        ->required()
                        ->maxLength(180)
                        ->prefixIcon('heroicon-m-map-pin')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('quota')
                        ->required()
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->default(100)
                        ->suffix('seats'),
                    Forms\Components\TextInput::make('ticket_price')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->step(1000)
                        ->prefix('Rp')
                        ->default(0)
                        ->helperText('Informational only. Evently V1 does not process payments.'),
                    Forms\Components\Select::make('status')
                        ->options(EventStatus::class)
                        ->required()
                        ->default(EventStatus::Draft)
                        ->native(false),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Feature on the public homepage')
                        ->default(false),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner')
                    ->disk('public')
                    ->height(44)
                    ->width(72)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover'])
                    ->defaultImageUrl(asset('images/evently-banner-placeholder.svg'))
                    ->label(''),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->description(fn (Event $record): ?string => $record->location),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn (Event $record): string => 'Ends ' . $record->end_at->format('d M Y, H:i')),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registered')
                    ->badge()
                    ->formatStateUsing(fn (int $state, Event $record): string => "{$state} / {$record->quota}")
                    ->color(fn (Event $record): string => $record->isSoldOut()
                        ? 'danger'
                        : ($record->remainingQuota() <= 10 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EventStatus::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('event_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->toggle()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['isActive'],
                        fn (Builder $query) => $query->where('start_at', '>=', now()),
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('This permanently deletes the event, its registrations, and attendance records.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => EventStatus::Published]);

                            Notification::make()
                                ->title('Events published')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('markCompleted')
                        ->label('Mark as completed')
                        ->icon('heroicon-m-flag')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => EventStatus::Completed]);

                            Notification::make()
                                ->title('Events marked as completed')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('start_at')
            ->emptyStateHeading('No events yet')
            ->emptyStateDescription('Create your first event and share it with the world.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
