<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventCategoryResource\Pages;
use App\Models\EventCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventCategoryResource extends Resource
{
    protected static ?string $model = EventCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Events';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category details')
                ->description('Categories power the public event filters and keep your catalog tidy.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(80)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $set('slug', Str::slug((string) $state));
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(90)
                        ->unique(ignoreRecord: true)
                        ->prefixIcon('heroicon-m-link')
                        ->helperText('Used in the public URL. Keep it short and lowercase.'),
                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (EventCategory $record): ?string => Str::limit($record->description, 70)),
                Tables\Columns\TextColumn::make('slug')
                    ->fontFamily('mono')
                    ->color('gray')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Events')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_events')
                    ->label('Only categories with events')
                    ->toggle()
                    ->query(fn ($query, array $data) => $query->when(
                        $data['isActive'],
                        fn ($query) => $query->whereHas('events'),
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Events in this category will remain, but they will no longer be categorized.')
                    ->disabled(fn (EventCategory $record): bool => $record->events()->exists())
                    ->tooltip(fn (EventCategory $record): ?string => $record->events()->exists()
                        ? 'Move or delete this category\'s events first.'
                        : null),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('No categories yet')
            ->emptyStateDescription('Create a category to start organizing your events.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventCategories::route('/'),
            'create' => Pages\CreateEventCategory::route('/create'),
            'edit' => Pages\EditEventCategory::route('/{record}/edit'),
        ];
    }
}
