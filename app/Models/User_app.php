<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Storage;

class User_app extends Model
{
    use HasFactory, ApiScopes;
    protected $fillable = ['name', 'email', 'password', 'birth_date','profile_photo'];

    protected $hidden = ['password'];

    protected $appends = ['profile_photo_url'];

    protected $allowIncluded = [
        'crops', 'crops.avocado_crop', 'crops.coffe_crop',
        'recommendations', 'finances',
        'animal_productions', 'animal_productions.finance',
        'animal_productions.cattles', 'animal_productions.hens'
    ];

    public function getProfilePhotoUrlAttribute()
    {
        // Verifica si existe la ruta en la DB y si el archivo existe en el disco 'public'
        if ($this->profile_photo && Storage::disk('public')->exists($this->profile_photo)) {
            // Genera la URL completa usando la ayuda de Storage::url()
            // Esto devolverá algo como http://api.AgroFinanzas.test/storage/profile_photos/nombre.jpg
            return url('storage/' . $this->profile_photo);
        }
        
        // Devuelve una imagen por defecto si no hay foto
        return null; // O la URL de tu imagen por defecto si la tienes en el API
    }
    
    protected $allowFilter = ['id', 'email', 'password'];

    protected $allowSort = ['id', 'name', 'email', 'birth_date'];

    // Relaciones
    public function crops()
    {
        return $this->belongsToMany(Crop::class, 'crop_user_app', 'id_user_app', 'id_crop');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'id_user_app');
    }


    public function finances()
    {
        return $this->belongsToMany(Finance::class, 'finance_user_app', 'id_user_app', 'id_finance');
    }


    public function animal_productions()
    {
    return $this->hasMany(Animal_production::class, 'id_user_app');
    }



    
}