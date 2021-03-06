<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Institution extends Model
{
	public $incrementing = false;
	
    protected $count; 

    protected $appends = array('count');
    protected $casts = [
    'address' => 'array',
    'settings' => 'array',
    'credentials' => 'array',
    'links' => 'array'
    ];
    /*$settings = [
        'patient-directed' => 'true/false',
        'bloodbags' => [
        'Karmi' => [ '450s','450d','450t','450q'],
        'Terumo' => [ '450s','450d','450t']
            ],
        'bloodtype_available' [
            'Whole Blood','Packed RBC','Platelets','Fresh Frozen Plasma','Cryoprecipitate'
            ]
        ],
    ];
    $credentials = [
        'pic1' =>
        'pic2' =>  
    ];
    */
    protected $fillable = [
        'id', 'institution','address','credentials','status','settings','links','logo','banner','about_us','contact','email','count','mother_branch','accepted'
    ];

    public function setCountAttribute($count)
    {   
        $this->count = $count;
    }

    public function getCountAttribute()
    {   
        return $this->count;
    }
    public function requests() {
    	return $this->hasMany('App\BloodRequest','institution_id','id');
    }

    public function donations() {
        return $this->hasMany('App\DonateRequest','institution_id','id');
    }
    public function admins() {
    	return $this->hasMany('App\InstitutionAdmin','institution_id','id');
    }
    public function followers() {
        return $this->morphToMany('App\User', 'follower', 'followers')->wherePivot('status', 1)->withTimestamps();
    }
    public function newlyFollowedInstitutions()
    {

        $lastWeek = new Carbon('last sunday');
        $lastWeekStr = $lastWeek->toDateTimeString();
        $nextWeek = new Carbon('next sunday');
        $nextWeekStr = $lastWeek->toDateTimeString();
        return $this->morphToMany('App\User', 'follower','followers')->wherePivot('status', 1)->wherePivot('created_at','>',$lastWeek)->wherePivot('created_at','<',$nextWeek);
    }
    public function name()
    {
        return $this->institution;
    }
    public function banner()
    {
        return $this->banner;
    }
    public function picture()
    {
        if($this->logo == null)
        return "http://bloodplus.usjr.edu.ph/assets/img/bloodplus/Logo2.png";
        else
        return $this->logo;
    }

    public function getCampaignsAttribute()
    {
     $tmpCampaigns = collect();
        foreach($this->admins as $admin)
        {
            foreach($admin->campaigns as $campaign)
            {
                $tmpCampaigns->push($campaign);
            }
        }
        $sortedCampaigns = $tmpCampaigns->sortBy('date_start');
        if(count($sortedCampaigns) == 0)
                
            return response()->json(array(
                'campaigns' => null,
                'message' => 'We have no initiated events or campaigns yet',
                'status' => 'Successful'));

        $campaign = array();
        $counter = 0;
        foreach($sortedCampaigns as $tmpCampaign)
        {
            $campaign[$counter]['id'] = $tmpCampaign->id;
            $campaign[$counter]['name'] = $tmpCampaign->name;
            $campaign[$counter]['picture'] = $tmpCampaign->picture;
            $campaign[$counter]['date_start'] = $tmpCampaign->date_start->toDateTimeString();
            $counter++;
        }
        $sortedCampaigns = $tmpCampaigns->sortBy('created_at');
        return $campaign;
    }
    public function branches()
    {
        return $this->hasMany('App\Institution','mother_branch','id');
    }

    public function motherBranch()
    {
        return $this->belongsTo('App\Institution','mother_branch','id');
    }

    public function distanceFromUser(User $tmpUser, $distance = 2)
    {
        if($tmpUser->location == null)
            return 0;

        $instituteLong = $this->address['longitude'];
        $institudeLat = $this->address['latitude'];
        $userLong = $tmpUser->location['longitude'];
        $userLat = $tmpUser->location['latitude'];

        $theta = $userLong - $instituteLong;
        $dist = sin(deg2rad($userLat)) * sin(deg2rad($institudeLat)) +  cos(deg2rad($userLat)) * cos(deg2rad($institudeLat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        $km = round($miles * 1.609344,1);   
        return $km;
    }
}
//$activity->skills is relation to activity skills
// $activitySkills = $activity->skills;

// //skills is a relation of volunteer to volunteer skills
// Volunteer::whereHas('skills',function ($query) use ($activitySkills){
//     $query->whereIn('name',$activitySkills)
// })
// dd($volunteer);