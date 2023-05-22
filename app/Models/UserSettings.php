<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $table = 'user_settings';
    protected $fillable = ['user_id','source_id','category_id','author_id','created_at','updated_at'];
    // Relationships
    // A record must belong to a user - via user_id
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    // A user can have many preferred sources
    public function sources()
    {
        return $this->hasMany(Sources::class, 'source_id');
    }
    public function categories()
    {
        return $this->hasMany(Categories::class, 'category_id');
    }
    public function authors()
    {
        return $this->hasMany(Authors::class, 'author_id');
    }
}
