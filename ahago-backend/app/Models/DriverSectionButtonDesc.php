<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverSectionButtonDesc extends Model
{
    use HasFactory;

    protected $fillable = ['button_id', 'title', 'text', 'svg'];

    public function button(){
        return $this->belongsTo(DriverSectionButton::class, 'button_id');
    }
}
