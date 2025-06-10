<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverSectionButton extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'img_src', 'name', 'text', 'link'];

    public function section(){
        return $this->belongsTo(DriverSection::class, 'section_id');
    }

    public function descriptions(){
        return $this->hasMany(DriverSectionButtonDesc::class, 'button_id');
    }
}
