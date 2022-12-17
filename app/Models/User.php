<?php

namespace App\Models;

use App\Notifications\PasswordCreateNotification;
use App\Notifications\PasswordResetNotification;
use App\Traits\HasTwoFactor;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasTwoFactor, HasRoles;

    protected $guard = 'web';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'sip_number',
        'password',
        'gender',
        'specialist_id',
        'kode_provider',
        'blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role'];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }

    public function getRoleAttribute()
    {
        return $this->roles->first()->id;
    }

    /**
     * Memfilter user yang bukan superuser
     */
    public function scopeNotSuperuser($query)
    {
        return $query->whereHas('roles', function($query) {
            $query->where('name', '!=', 'Superuser');
        });
    }

    /**
     * Memfilter user yang memiliki role superuser
     */
    public function scopeIsSuperuser($query)
    {
        return $query->whereHas('roles', function($query) {
            $query->where('name', 'Superuser');
        });
    }

    /**
     * Memfilter user yang bukan superuser
     */
    public function scopeisDoctor($query)
    {
        return $query->whereHas('roles', function($query) {
            $query->where('name', 'Dokter');
        });
    }

    public function specialist()
    {
        return $this->hasOne(Specialist::class, 'id', 'specialist_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Send the password create notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordCreateNotification()
    {
        $this->notify(new PasswordCreateNotification());
    }
}
