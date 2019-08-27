<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
	public $incrementing = false;

	protected $dates = [
	'date_start',
	'date_end'
	];
	protected $casts = [
	'address' => 'array'
	];

	//blood drive or not.

	protected $fillable = [
	'id','name','address','description','date_start','date_end','status','picture','initiated_by','type','quota'
	];
	    
	public function initiated()
	{
		return $this->belongsTo('App\InstitutionAdmin','initiated_by');
	}

	public function attendance()
	{
		return $this->hasMany('App\Attendance','campaign_id','id');
	}

	public function attendanceUserModel()
	{
		return $this->belongsToMany('App\User','attendances','campaign_id','user_id')->withPivot('status')->withTimeStamps()->using('App\ReactionPost');
	}
	public function name()
	{
		return $this->name;
	}

	public function picture()
	{
		return $this->picture;
	}
}
