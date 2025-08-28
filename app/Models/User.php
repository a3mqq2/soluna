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
        'institution_id',
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


    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'user_institutions');
    }


    public function activeInstitutions()
    {
        return $this->belongsToMany(Institution::class, 'user_institutions')
                    ->where('institutions.is_active', true);
    }

    
    public function getInstitutionsCountAttribute()
    {
        return $this->institutions()->count();
    }

 
    public function getActiveInstitutionsCountAttribute()
    {
        return $this->activeInstitutions()->count();
    }

    public function belongsToInstitution($institutionId)
    {
        return $this->institutions()->where('institution_id', $institutionId)->exists();
    }


    public function syncInstitutions(array $institutionIds)
    {
        return $this->institutions()->sync($institutionIds);
    }

 
    public function attachInstitution($institutionId)
    {
        return $this->institutions()->attach($institutionId);
    }

  
    public function detachInstitution($institutionId)
    {
        return $this->institutions()->detach($institutionId);
    }

 
    public function scopeByInstitution($query, $institutionId)
    {
        return $query->whereHas('institutions', function ($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        });
    }


    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}