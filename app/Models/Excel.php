<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excel extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'gender', 'date_of_birth'];
}
