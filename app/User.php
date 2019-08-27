<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use App\Institution;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    
    protected $casts = [
    'address' => 'array',
    'settings' => 'array',
    'location' => 'array'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'dob'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','fname','lname','mi', 'email', 'password','status','gender','bloodType','dob',    'contactinfo','email_token','api_token','verified','banner','picture','address','device_token','location','company','affiliate','settings'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email_token','verified'
    ];

    public function requests() {
        return $this->hasMany('App\BloodRequest','initiated_by','id');
    }
    public function donations() {
        return $this->hasMany('App\DonateRequest','initiated_by','id');
    }
    public function name() {
        return $this->fname.' '.$this->lname;
    }
    public function logs() {
        return $this->hasMany('App\Log','initiated_id','id');
    }
    public function attendances() {
        return $this->hasMany('App\Attendance','user_id','id');
    }
    public function posts() {
        return $this->morphMany('App\Post','initiated');
    }
    public function picture()
    {
        return $this->picture;
    }
    public function banner()
    {
        return $this->banner;
    }
    public function receivesBroadcastNotificationsOn()
    {
        return 'users.'.$this->id;
    }
    public function followers()
    {
    return $this->morphToMany('App\User', 'follower','followers')->wherePivot('status', 1)->withTimestamps();
    }
    public function followedUsers()
    {
    return $this->morphedByMany('App\User', 'follower','followers')->wherePivot('status', 1)->withTimestamps();
    }
    public function followedInstitutions()
    {
    return $this->morphedByMany('App\Institution', 'follower','followers')->wherePivot('status', 1)->withTimestamps();
    }
    public function super()
    {
        return $this->hasOne('App\God', 'user_id','id');
    }

    public function routeNotificationForGcm()
    {
        // return "eZ5tgf8gBOg:APA91bEFXtQXINMUNJtiBWRhqEPpWODOZzGLspzBzQYQutux5LGHhYm-J7dUS69xcmkeKxTGkhSQB2RXTQcIAxUtGML4HFLSK6bbiOEegAbUu2alwb28Z18ZvQyl0CWqXmdTJSVh5ajA";
        if($this->device_token == null)
        {
            return " ";
        }
        return $this->device_token;
    }

    public function getAgeAttribute()
    {
        return $this->dob->age;
    }
    public function ageEligibility()
    {
        if($this->age > 16)
        {
            return true;
        }
        else
            return false;
    }
    public function followIfNotFollowed(Institution $institution)
    {

    }

    public function checkDistance(Institution $tmpInstitution, $distance = 2)
    {
        if($this->location == null)
            return false;

        $instituteLong = $tmpInstitution->address['longitude'];
        $institudeLat = $tmpInstitution->address['latitude'];
        $userLong = $this->location['longitude'];
        $userLat = $this->location['latitude'];

        $theta = $userLong - $instituteLong;
        
        $distance = rad2deg(acos(sin(deg2rad($userLat)) * sin(deg2rad($institudeLat)) +  cos(deg2rad($userLat)) * cos(deg2rad($institudeLat)) * cos(deg2rad($theta))));
        $distInMiles = $distance * 60 * 1.1515;

        $distInKm = round($distInMiles * 1.609344,1);   
        if($distInKm <= $distance)
        return true;
        else
        return false;
    }

    public function sendSMS($message)
    {
        $number = "0".$this->contactinfo;
        $apicode = config("app.itexmoCode");
        $ch = curl_init();
        $itexmo = array('1' => $number, '2' => $message, '3' => $apicode);
        curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                  http_build_query($itexmo));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result =  curl_exec ($ch);
        curl_close ($ch);
        return $result;
    }
}
