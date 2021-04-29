<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Injury extends Model
{
    use softDeletes;
    use HasFactory;

    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['pupil_id', 'date_of_injury','comment'];
}
