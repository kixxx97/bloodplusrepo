<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{


    public $incrementing = false;

	protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'request_date'
    ];

    protected $casts = 
    [
        'updates' => 'array'
    ];

    protected $fillable = [
        'id','patient_name','diagnose','institution_id','status','initiated_by','updates','reason','request_date'
    ];

    public function institute() {
    	return $this->belongsTo('App\Institution','institution_id','id');
    }

    public function user() {
    	return $this->belongsTo('App\User','initiated_by');
    }

    public function details() {
        //daghan ta ni
    	return $this->hasOne('App\BloodRequestDetail','blood_request_id','id');
    }

    public function donors() {
        return $this->hasMany('App\BloodRequestDonor','blood_request_id','id');
    }

    public function getDonors()
    {

        return count($this->donors);
        
    }
    public function post() {

        return $this->morphOne('App\Post','reference');

    }

    public function getSuccessfulDonationsAttribute()
    {
        $donors = $this->donors;
        $details = $this->details;
        $filteredCount = count($donors->filter(function ($value,$key) use($details) {
        $donate = $value->donate;
        if($donate)
        {
        $components = $value->donate->screenedBlood;
        if($components)
        {
        $boolean = false;
        if($components->components)
        {
        foreach($components->components as $component)
        {
            $boolean = false;
            $category = $component->bloodType->category;
            if($category == $details->blood_category)
            {
                if($component->status == 'Available')
                {
                $boolean = true;
                break;
                }
            }
        }
        }
        if($boolean)
            return true;
        }
        }
        }));
        return $filteredCount;
    }
}
