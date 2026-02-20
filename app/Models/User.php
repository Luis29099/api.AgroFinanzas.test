<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, ApiScopes;

    protected $fillable = [
    'name', 'email', 'password', 'birth_date', 'profile_photo',
    'phone', 'gender', 'experience_years',
    'verification_code',        // ← agregar
    'verification_expires_at',  // ← agregar
    'is_verified', 
];

    protected $hidden = ['password'];

    protected $appends = ['profile_photo_url'];

    protected $allowIncluded = [
        'crops', 'crops.avocado_crop', 'crops.coffe_crop',
        'recommendations', 'finances',
        'animal_productions', 'animal_productions.finance',
        'animal_productions.cattles', 'animal_productions.hens'
    ];

    protected $allowFilter = ['id', 'email'];

    protected $allowSort = ['id', 'name', 'email', 'birth_date'];

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo;
    }

    // Relaciones
    public function crops()
    {
        return $this->belongsToMany(Crop::class, 'crop_user_app', 'user_id', 'id_crop');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'user_id');
    }

    public function finances()
    {
        return $this->hasMany(Finance::class, 'user_id');
    }

    public function animal_productions()
    {
        return $this->hasMany(Animal_production::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}