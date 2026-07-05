<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
    'first_name', 'last_name', 'birth_date', 'birth_place', 
    'address', 'cin_number', 'cin_image_path', 'ocr_raw_text', 'created_by'
];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function diplomas()
    {
        return $this->hasMany(Diploma::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
