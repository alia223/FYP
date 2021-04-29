<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAvailability extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'monday_available_from', 
        'monday_available_until', 
        'tuesday_available_from', 
        'tuesday_available_until',
        'wednesday_available_from',
        'wednesday_available_until',
        'thursday_available_from',
        'thursday_available_until',
        'friday_available_from',
        'friday_available_until',
        'max_hours'
    ];

}
