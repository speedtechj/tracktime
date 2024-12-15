<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class TracktimesRelationManager extends RelationManager
{
    protected static string $relationship = 'tracktimes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('clockin')
                ->label('Clock In')
                ->sortable()
                ->formatStateUsing(function ($state) {
                $clock_in = Carbon::parse($state);
                        return  $clock_in->format('h:i:s a');
                    
              
                }),
                Tables\Columns\TextColumn::make('clockout')
                ->label('Clock Out')
                ->formatStateUsing(function ($state) {
                    $clock_out = Carbon::parse($state);
                            return  $clock_out->format('h:i:s a');
                    }),
                    // ->default('On Going'),
                Tables\Columns\TextColumn::make('workdate')
                ->label('Work Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalhours')
                    ->label('Total Hours')
                    ->default('On Going')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->searchable(),
            ])
            ->filters([
                Filter::make('workdate')->label('Pickup Date')
                ->form([
                    Section::make('Working Date')
                        ->schema([
                            Forms\Components\DatePicker::make('workdate_from'),
                            Forms\Components\DatePicker::make('workdate_until')
                        ])->collapsible(),

                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['workdate_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('workdate', '>=', $date),
                        )
                        ->when(
                            $data['workdate_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('workdate', '<=', $date),
                        );
                }),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
