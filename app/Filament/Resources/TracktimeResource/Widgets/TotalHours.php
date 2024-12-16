<?php

namespace App\Filament\Resources\TracktimeResource\Widgets;

use Carbon\Carbon;
use App\Models\Tracktime;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TotalHours extends BaseWidget
{
    protected function getStats(): array
    {   
        $current_time = Tracktime::where('user_id', Auth::user()->id)->latest()->first();
        // dd(Carbon::parse($current_time->clockout));
        if($current_time->clockout == null){
                $currentout = 'Ongoing';
        }else {
            $currentout = Carbon::parse($current_time->clockout)->format('h:i a');
        }
        return [
            Stat::make('Clock In',  Carbon::parse($current_time->clockin)->format('h:i a')),
            Stat::make('Clock Out', $currentout),
            Stat::make('Total', $current_time->totalhours ?? 'Ongoing'),
        ];
    }
}
