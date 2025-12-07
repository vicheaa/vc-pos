<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SequenceNumber extends Model
{
    /** @use HasFactory<\Database\Factories\SequenceNumberFactory> */
    use HasFactory;

    protected $guarded = [];
}
