<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\AdminResetPasswordNotification;


class InstitutionAdmin extends Authenticatable
{

    use Notifiable;
    

	public $incrementing = false;


	protected $fillable = [
        'id','institution_id','name','password','status'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    public function institute() {
    	return $this->belongsTo('App\Institution','institution_id','id');
    }
    public function receivesBroadcastNotificationsOn()
    {
        return 'admin.'.$this->id;
    }

    public function picture()
    {
        return $this->institute->picture();
    }
    public function name()
    {
        return $this->institute->name();
    }

    public function posts() {
        return $this->morphMany('App\Post','initiated');
    }

    public function campaigns() {
        return $this->hasMany('App\Campaign','initiated_by','id');
    }
    public function sendPasswordResetNotification($token)
    {
      $this->notify(new SellerResetPasswordNotification($token));
    }
    public function routeNotificationForGcm()
    {
        if($this->device_token == null)
        {
            return " ";
        }
        return $this->device_token;
    }
}
