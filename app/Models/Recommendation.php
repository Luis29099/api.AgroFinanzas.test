<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = [
        'text', 'user_id', 'category',
        'parent_id', 'media_url', 'media_type',
    ];

    protected $allowIncluded = ['user', 'replies'];

    public function user()    { return $this->belongsTo(User::class); }
    public function parent()  { return $this->belongsTo(Recommendation::class, 'parent_id'); }
    public function replies() {
        return $this->hasMany(Recommendation::class, 'parent_id')->with('user');
    }
}