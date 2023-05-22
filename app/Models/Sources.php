<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sources extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'sources';
    protected $fillable = ['source_id','source_name'];
    // Relationships
}
