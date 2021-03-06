<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloodRequestDetail extends Model
{
    protected $fillable = [
        'id','bloodbag_id','blood_request_id','blood_type','blood_category','units','status'
    ];


    public function request() {

    	return $this->belongsTo('App\BloodRequest','blood_request_id');
    }

    public function bloodType() {
    	return $this->hasOne('App\BloodType','id','bloodbag_id');
    }

    public function blood() {

    	$bloodType = $this->bloodBag->bloodType->name;
    	$bloodCategory  = $this->bloodBag->category;
    	return array('bloodType' => $bloodType,
    		'bloodCategory' => $bloodCategory);
    }
    public function bloodCategory() {
    	return $this->bloodBank->category;
    }

    public function releasedBlood()
    {
        return $this->hasMany('App\BloodInventory','br_detail_id','id');
    }

}
