<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['brand_logo', 'brand_colour', 'text_colour', 'club_start', 'end_start', 'club_duration_step', 'booking_interval', 'pupil_ratio'];
}
