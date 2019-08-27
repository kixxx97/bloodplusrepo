<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unsubscribe extends Model
{
    public $incrementing = false;

	protected $fillable = [
	'id','reason'
	];

	
}
