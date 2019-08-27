<?php

namespace App\Http\Controllers\Super;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institution;
use App\Notifications\BloodRequestNotification;
use Carbon\Carbon;
use App\Log;
use App\BloodInventory;
use App\ScreenedBlood;
use Auth;
use App\BloodCategory;
use App\InstitutionAdmin;

class InstitutionsController extends Controller
{
    //create institution (god mode)
    public function getInstitutions(Request $request)
    {
    	$pendingInstitutions = Institution::where('status','Pending')->get();
    	$activeInstitutions = Institution::where('status','active')->get();
    	$inactiveInstitutions = Institution::where('status','inactive')->get();
    	return view('bpadmin.institutions',compact('pendingInstitutions','activeInstitutions','inactiveInstitutions'));
    }

  	public function acceptInstitution(Request $request)
    {
        $institution = Institution::find($request->input('id'));
    	$institution->update([
    	'status' => 'active',
    	'updated_at' => Carbon::now()->toDateTimeString()
    	]);

        //logs
        Log::create([
            'initiated_id' => Auth::user()->super->id,
            'initiated_type' => 'App\God',
            'reference_id' => $institution->id,
            'reference_type' => 'App\Institution',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You accepted an institution to the system.'
            ]);

        Log::create([
            'initiated_id' => $institution->admins->first()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_id' => $institution->id,
            'reference_type' => 'App\Institution',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You have been accepted to BloodPlus!'
            ]);

        //notify
        $admins = InstitutionAdmin::where('institution_id',$institution->id)->get();

        $class = array("class" => "App\Notification",
            "id" => $institution->id,
            "time" => Carbon::now()->toDateTimeString());
        $user = array("name" => Auth::user()->name(),
                "picture" => Auth::user()->picture());
        $message = 'We have accepted your system. You can now receieve blood donations and blood requests.';
        foreach($admins as $admin)  
        {
            $admin->notify(new BloodRequestNotification($class,$user,$message));
        }

    	return redirect('/bpadmin/institutions')->with('status','Successfully accepted institution');
    }

    public function declineInstitution(Request $request)
    {
        $institution = Institution::find($request->input('id'));
        $institution->update([
        'status' => 'decline',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        //logs
        Log::create([
            'initiated_id' => Auth::user()->super->id,
            'initiated_type' => 'App\God',
            'reference_id' => $institution->id,
            'reference_type' => 'App\Institution',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You declined an institution to the system.'
            ]);

        //notify
        $admins = InstitutionAdmin::where('institution_id',$institution->id)->get();

        $class = array("class" => "App\Notification",
            "id" => $institution->id,
            "time" => Carbon::now()->toDateTimeString());
        $user = array("name" => Auth::user()->name(),
                "picture" => Auth::user()->picture());
        $message = 'We have declined your institution to use BloodPlus.';
        foreach($admins as $admin)  
        {
            $admin->notify(new BloodRequestNotification($class,$user,$message));
        }

        return redirect('/bpadmin/institutions')->with('status','Successfully declined institution');
        // dd($request->input());
    }

    public function getInstitution(Institution $institution)
    {
        $bloodTypes = BloodCategory::with([
          'bloodType' => function($query) use ($institution)
          {
            $query->whereIn('category',$institution->settings['bloodtype_available']);
            $query->orderBy('category');
          },'bloodType.inventory' => function ($query)
          {
            $query->where('status','Available');
          }
        ])->orderBy('name')->get();
        return view('bpadmin.showinstitutioninventory',compact('institution','bloodTypes'));
    }

      
}
