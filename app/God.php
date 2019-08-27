<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class God extends Model
{

    use Notifiable;

    public $incrementing = false;

   	protected $fillable = [
   		'id','user_id','status'
   	];

   	public function logs() {
        return $this->hasMany('App\Log','initiated_id','id');
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'god.'.$this->id;
    }
}
