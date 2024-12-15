<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clockbtn;
use App\Models\TimeZone;
use Filament\Forms\Form;
use App\Models\Tracktime;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TracktimeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TracktimeResource\RelationManagers;

class TracktimeResource extends Resource
{
    protected static ?string $model = Tracktime::class;
    protected static ?string $navigationLabel = 'Track Time ';
    public static ?string $label = 'Track Time';
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\FileUpload::make('imagecapture')
                ->label('Verification Image')
                ->multiple()
                ->panelLayout('grid')
                ->uploadingMessage('Uploading attachment...')
                ->image()
                ->openable()
                ->disk('public')
                ->directory('images')
                ->visibility('private')
                ->required()
                ->removeUploadedFileButtonPosition('right')
                ->minFiles(2),
            Forms\Components\RichEditor::make('note')
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(Tracktime::query()->where('user_id', Auth::user()->id))
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
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalhours')
                    ->label('Total Hours')
                    ->default('On Going')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('ClockIn')
                    ->visible(function () {
                        $is_on = User::where('id', Auth::user()->id)->first()->is_on;
                        return $is_on;
                    })
                    ->label('Clock In')
                    ->icon('heroicon-m-clock')
                    ->form([
                        Forms\Components\FileUpload::make('imagecapture')
                            ->label('Verification Image')
                            ->multiple()
                            ->panelLayout('grid')
                            ->uploadingMessage('Uploading attachment...')
                            ->image()
                            ->openable()
                            ->disk('public')
                            ->directory('images')
                            ->visibility('private')
                            ->required()
                            ->removeUploadedFileButtonPosition('right')
                            ->minFiles(2),
                        Forms\Components\RichEditor::make('note')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        // dd(Carbon::now()->timezone('Asia/Manila')->format('h:i:s a'));
                        $timezone = TimeZone::where('user_id', Auth::user()->id)->first();
                        
                        Tracktime::create([
                            'user_id' => Auth::user()->id,
                            'clockin' => Carbon::now()->tz($timezone->time_zone)->format('H:i:s'),
                            'workdate' => Carbon::now()->tz($timezone->time_zone)->format('Y-m-d'),
                            'note' => $data['note'],
                            'imagecapture' => $data['imagecapture'],
                        ]);
                        $get_current_user = User::where('id', Auth::user()->id);
                        $get_current_user->update(['is_on' => false]);


                    }),
                Action::make('ClockOut')
                    ->label('Clock Out')
                    ->icon('heroicon-m-clock')
                    ->visible(function () {
                        $is_on = User::where('id', Auth::user()->id)->first()->is_on;
                        return !$is_on;
                    })
                    ->form([
                    ])
                    ->action(function (array $data): void {
                        $getcurrent_record = Tracktime::where('user_id', Auth::user()->id)->where('is_active', 1)->first();
                        $timezone = TimeZone::where('user_id', Auth::user()->id)->first();
                        
                        $startTime = Carbon::parse($getcurrent_record->clockin);
                        $endTime = Carbon::parse(Carbon::now()->tz($timezone->time_zone));
                        if ($endTime->lt($startTime)) {
                            $endTime->addDay(); // Add 24 hours to the end time
                        }
                        $getcurrent_record->update([
                            'clockout' => Carbon::now()->tz($timezone->time_zone)->format('H:i'),
                            'totalhours' => Carbon::parse($getcurrent_record->clockin)->diffInHours(Carbon::now()->tz('America/Edmonton')->format('H:i')),
                            'is_active' => 0
                        ]);
                        $get_current_user = User::where('id', Auth::user()->id);
                        $get_current_user->update(['is_on' => true]);
                    })
                
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
            ->actions([
                // Tables\Actions\EditAction::make()
                // ->iconButton()
                //     ->icon('heroicon-s-pencil'),
                // Tables\Actions\DeleteAction::make()
                // ->iconButton()
                //     ->icon('heroicon-s-trash'),
                
                    // Tables\Actions\Action::make('Calculate')
                    // ->label('Calculate')
                    // ->action(function (array $data, Model $record) {
                       
                       
                    //     // $totalhours = $starttime->diffInHours($endtime);
                    //     // dd($totalhours);
                       
                    //     // $record->update([
                    //     //     'totalhours' => $startTime->diffInHours($endTime),
                    //     // ]);

                        
                    //      }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTracktimes::route('/'),
            // 'create' => Pages\CreateTracktime::route('/create'),
            // 'edit' => Pages\EditTracktime::route('/{record}/edit'),
        ];
    }
}
