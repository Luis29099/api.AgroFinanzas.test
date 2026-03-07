<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Recommendation extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = [
        'text', 'user_id', 'category',
        'parent_id', 'media_url', 'media_type',
    ];

    protected $appends = ['content', 'replies_count', 'likes_count', 'liked_by_user'];

    public function getContentAttribute()
    {
        return $this->text;
    }

    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getLikedByUserAttribute()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    protected $allowIncluded = ['user', 'replies'];

    public function user()    { return $this->belongsTo(User::class); }
    public function parent()  { return $this->belongsTo(Recommendation::class, 'parent_id'); }
    public function replies() {
        return $this->hasMany(Recommendation::class, 'parent_id')->with('user');
    }
    public function likes() {
        return $this->hasMany(RecommendationLike::class);
    }
}