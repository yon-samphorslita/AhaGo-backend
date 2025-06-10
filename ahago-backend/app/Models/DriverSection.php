<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSection extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'text', 'link_to'];

    public function buttons() {
        return $this->hasMany(DriverSectionButton::class, 'section_id');

    }
}
