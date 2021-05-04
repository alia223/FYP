<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pupil extends Model
{
    use softDeletes;
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pupil_first_name',
        'pupil_last_name',
        'pupil_date_of_birth',
        'pupil_food_arrangement'
    ];
}
