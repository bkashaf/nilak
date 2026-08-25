<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'name',
        'email',
        'mobile',
        'secondary_phone',
        'postal_code',
        'address',
        'password',
        'status',
        'preferred_locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function permissions()
    {
        return $this->roles->map->permissions->flatten()->unique('id');
    }

    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->count() > 0;
    }

    public function isProfileComplete(): bool
    {
        return filled($this->first_name)
            && filled($this->last_name)
            && filled($this->mobile)
            && filled($this->secondary_phone)
            && filled($this->postal_code)
            && filled($this->address);
    }

    public function profileMissingFields(): array
    {
        $missing = [];

        if (! filled($this->first_name)) $missing[] = 'نام';
        if (! filled($this->last_name)) $missing[] = 'نام خانوادگی';
        if (! filled($this->mobile)) $missing[] = 'شماره موبایل';
        if (! filled($this->secondary_phone)) $missing[] = 'شماره ضروری دوم';
        if (! filled($this->postal_code)) $missing[] = 'کد پستی';
        if (! filled($this->address)) $missing[] = 'آدرس';

        return $missing;
    }
}
