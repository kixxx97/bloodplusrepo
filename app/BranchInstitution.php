<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchInstitution extends Model
{
    protected $fillable = [
        'id','mother_id','branch_id','status'
    ];

    public function mother()
    {
    	return $this->belongsTo('App\Institution','mother_id','id');
    }

    public function branch()
    {
    	
    }
}
