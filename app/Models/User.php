<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
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
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's status as text.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    /**
     * Get the user's status class for styling.
     */
    public function getStatusClassAttribute(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-danger';
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

   
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

  
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
        return $this;
    }

  
    public function getAllUserPermissions()
    {
        return $this->getAllPermissions();
    }

  
    public function hasAnyPermissions()
    {
        return $this->getAllPermissions()->isNotEmpty();
    }


    public function getPermissionsCountAttribute()
    {
        return $this->getAllPermissions()->count();
    }

  
    public function getRolesCountAttribute()
    {
        return $this->roles->count();
    }
}