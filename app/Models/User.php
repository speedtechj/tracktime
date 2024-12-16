<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Location;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function canAccessPanel(Panel $panel): bool
    {
        $user_role  = Auth::user()->hasRole('super_admin');
        $user_ip  = Auth::user()->userip;
        $current_ip = request()->Ip();

        if(!$user_role){
            if($user_ip == $current_ip)
            {
            return true;
            }else {
                return false;
                Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
            }
        }else {
            return true;
        }
       
        
       
       
    }
}
