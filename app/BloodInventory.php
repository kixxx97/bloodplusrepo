<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
	protected $table = 'blood_inventories';
	public $incrementing = false;

	protected $fillable = [
   	 'id','screened_blood_id','blood_type_id','status',
       'br_detail_id'
   	 ];

   public function bloodType()
   {
   		return $this->belongsTo('App\BloodType','blood_type_id','id');
   }

   public function screenedBlood()
   {
   		return $this->belongsTo('App\ScreenedBlood','screened_blood_id','id');
   }

   public function request()
   {
      return $this->belongsTo('App\BloodRequestDetail','br_detail_id','id');
   }
}
