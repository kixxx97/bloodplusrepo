<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    public $incrementing = false;
    protected $table = 'medical_forms';

    protected $casts = [
    'medical_history' => 'array'
    ];

    protected $fillable = [
    'id','donate_request_id','medical_history','status','remarks'
    ];
}
