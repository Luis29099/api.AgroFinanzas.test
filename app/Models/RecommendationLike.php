<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationLike extends Model
{
    protected $fillable = ['user_id', 'recommendation_id'];

    public function user()           { return $this->belongsTo(User::class); }
    public function recommendation() { return $this->belongsTo(Recommendation::class); }
}