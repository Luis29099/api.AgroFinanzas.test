<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Recommendation extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = [
        'text',
        'id_user_app',
        'category',
        'parent_id'
    ];

    protected $allowIncluded = [
        'user',
        'replies'
    ];

    public function user()
    {
        return $this->belongsTo(User_app::class, 'id_user_app');
    }

    // Comentario padre
    public function parent()
    {
        return $this->belongsTo(Recommendation::class, 'parent_id');
    }

    // Respuestas
    public function replies()
    {
        return $this->hasMany(Recommendation::class, 'parent_id')
                    ->with('user');
    }
}
