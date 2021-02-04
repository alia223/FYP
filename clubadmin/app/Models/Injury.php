<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Injury extends Model
{
    use HasFactory;

    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['studentid', 'date_of_injury','comment'];
}
