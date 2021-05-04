<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use softDeletes;
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_first_name',
        'student_last_name',
        'student_date_of_birth',
        'student_dietary_requirements',
        'student_food_arrangement'
    ];
}
