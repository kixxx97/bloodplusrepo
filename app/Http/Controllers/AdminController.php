<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\BloodRequest;
use App\phpSerial;
use App\User;
use App\DonateRequest;
use App\Jobs\SendTextBlast;
use Illuminate\Console\Command;
use Artisan;
use Auth;
use App\Notifications\BloodRequestNotification;
use App\donorstemp;
use Carbon\Carbon;
use App\Post;
use App\Log;
use App\Campaign;
use App\BloodInventory;

class AdminController extends Controller
{
    

    public function index()
    {
        //get new donors since last week.
        $nxt = new Carbon('last sunday');
        // dd($nxt->toDateTimeString());
        $newlyDonors = count(Auth::guard('web_admin')->user()->institute->newlyFollowedInstitutions);
        // dd($newlyDonors);
        $tmpEvents = Campaign::with('initiated.institute')->whereHas('initiated.institute', function ($query) {
            $query->where('id',Auth::guard('web_admin')->user()->institute->id);
        })->get();
        $events = array();
        $counter = 0;
        foreach($tmpEvents as $tmp)
        {
            $events[$counter]['title'] = $tmp->name;
            $events[$counter]['start'] = $tmp->date_start->toDateTimeString();
            $events[$counter]['end'] = $tmp->date_end->toDateTimeString();
            $events[$counter]['backgroundColor'] = "#f56954";
            $events[$counter]['borderColor'] = "#f56954";
            $counter++;
        }
        $events = json_encode($events);
        $bloodDonors = count(User::where('status','active')->get());
        $campaignCount = count(Campaign::where('status','Done')->get());
        $logs = Log::where('initiated_id',Auth::guard('web_admin')->user()->id)->orderBy('created_at','desc')->paginate(10);

    	return view('admin.dashboard',compact('logs','campaignCount','newlyDonors','nxt','events'));
    }

    public function request() {

        $id = Auth::guard('web_admin')->user()->institute->id;

        $requests = BloodRequest::with(['details','user' => function ($query) {
        }])->where('institution_id',$id)->orderBy('created_at')->get();
        $ongoingRequests = BloodRequest::with([
            'donors' => function ($query) {
                $query->where('status','Ongoing');
            }])->where('institution_id',$id)->where('status', 'Ongoing')->orderBy('created_at')->get();
        
        // dd($requests);
    	return view('admin.request',compact('requests','ongoingRequests'));
    }

    public function donors() {
        $tmpDonors = Auth::guard('web_admin')->user()->institute->followers;
        $donors = array();
        $count = 0;
        // dd($donors);
        foreach($tmpDonors as $donor)
        {
            // dd($donor->name());
            $donors[$count]['id'] = $donor->id;
            $donors[$count]['blood_type'] = $donor->bloodType;
            $donors[$count]['name'] = $donor->name();
            $donors[$count]['gender'] =  $donor->gender;
            $donors[$count]['contact'] = '0'.$donor->contactinfo;
            $donors[$count]['email'] = $donor->email;
            $donors[$count]['joinDate'] = $donor->pivot->created_at->format('F d Y');

            $lastRequest = DonateRequest::where('status','Done')->where('initiated_by',$donor->id)->orderBy('appointment_time','desc')->first();
            if($lastRequest)
            {
                if($lastRequest->appointment_time)
                {
                    $donors[$count]['last'] = $lastRequest->appointment_time->format('F d Y');
                    $date = $lastRequest->appointment_time;
                }
                else
                {
                    $donors[$count]['last'] = $lastRequest->created_at->format('F d Y');
                    $date = $lastRequest->created_at;
                }
            $now = Carbon::now();
            if($date->addDays(90) >= $now)
                {
                    $donors[$count]['eligible'] = 'No';
                }
            else
                {
                    $donors[$count]['eligible'] = 'Yes';
                }
            }
            else
            {
            $donors[$count]['last'] = '';
            $ongoingRequest = DonateRequest::where('initiated_by',$donor->id)->whereIn('status',['Pending','Ongoing'])->get();
            if(count($ongoingRequest) > 0) 
                {
                    $donors[$count]['eligible'] = 'No';
                }
            else
                {
                    $donors[$count]['eligible'] = 'Yes';
                }
            }
            $count++;
        }

        // dd($donors);
        return view('admin.donor',compact('donors'));
    }
    public function pendingRequests() {

    	$id = Auth::guard('web_admin')->user()->institute->id;

    	$requests = BloodRequest::with(['details','user' => function ($query) {
            
    	}])->where('institution_id',$id)->get();
        // dd($requests);
    	return $requests;
    }

    public function viewRequest(Request $request)
    {
        // dd(BloodRequest::with('details','user')->find($request->input('id'))->updates);
        return BloodRequest::with('details','user')->find($request->input('id'));

    }

    public function updateToActive(Request $request)
    {
        $bloodRequest = BloodRequest::find($request->input('id'));
        // $this->sendTextBlast($bloodRequest);
        $updates = $bloodRequest->updates;              
        $updates[] = 'The request has been accepted and notified to eligible donors.';    
        $bloodRequest->update([
            'status' => 'Ongoing',
            'updates' => $updates,
            'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        $bloodRequest->details()->update([
            'status' => 'Ongoing',
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        if($bloodRequest->details->blood_type == 'AB+')
            $picture = asset('assets/img/bloodtype/ab+.jpg');
        else if($bloodRequest->details->blood_type == 'AB-')
            $picture = asset('assets/img/bloodtype/ab-.jpg');
        else if($bloodRequest->details->blood_type == 'A-')
            $picture = asset('assets/img/bloodtype/a-.jpg');
        else if($bloodRequest->details->blood_type == 'A+')
            $picture = asset('assets/img/bloodtype/a+.jpg');
        else if($bloodRequest->details->blood_type == 'B-')
            $picture = asset('assets/img/bloodtype/b-.jpg');
        else if($bloodRequest->details->blood_type == 'B+')
            $picture = asset('assets/img/bloodtype/b+.jpg');
        else if($bloodRequest->details->blood_type == 'O-')
            $picture = asset('assets/img/bloodtype/O-.jpg');
        else if($bloodRequest->details->blood_type == 'O+')
            $picture = asset('assets/img/bloodtype/O+.jpg');

        // dd($picture);
        $post = Post::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'Someone is in need of blood heroes!.',
            'picture' => $picture,
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\BloodRequest',
            'reference_id' => $bloodRequest->id
            ]);
        Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\BloodRequest',
            'reference_id' => $bloodRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => Auth::guard('web_admin')->user()->institute->name().' just accepted a blood request!'
            ]);

        $user = $bloodRequest->user;
        $class = array("class" => "App\CallToBloodRequest",
            "id" => $bloodRequest->id,
            "time" => $bloodRequest->created_at->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $user->notify(new BloodRequestNotification($class,$usersent,'We have just accepted your request amd broadcasted to eligible donors.'));
        
        // notify uban donors
        $sameBloodTypeUsers = User::with(['donations' => function ($query) {
        //latest niyang donation that is not cancelled
            $query->where('status','!=','Cancelled')->orderBy('created_at','desc')->first();
        }])->whereHas('followedInstitutions', function($query) {
            $query->where('id',Auth::guard('web_admin')->user()->institution_id);
        })->where('bloodType',$bloodRequest->details->blood_type)->get();

        // return response()->json($sameBloodTypeUsers);
        foreach($sameBloodTypeUsers as $user)
        {
            if($user->id != $bloodRequest->initiated_by)
            {
            if(count($user->donations) != 0)
            {
                //eligible to donate and has previous donations so tan.awn nato ang latest niya nga donation and if it is done then check the appointment_date if it is greater than 3 months.
                if($user->donations->first()->status == 'Done')
                {
                    $date = $user->donations->first()->appointment_time;
                    $now = Carbon::now();
                    if($date->addDays(90) >= $now)
                    {
                        $institution = Auth::guard('web_admin')->user()->institute->name();
                        $message = "Someone is in need of your blood. Please donate to ".$institution.".";
                        $user->notify(new BloodRequestNotification($class,$usersent,$message));


                            // $mobile = $user->contactinfo;
                            // Chikka::send($mobile, $message);
                    }
                }
            }
            //wa pa siyay donation jd so eligible siya mu donate, and if age is greater than 15
            else
            {
                $institution = Auth::guard('web_admin')->user()->institute->name();
                $message = "Someone is in need of your blood. Please donate to ".$institution.".";
                $user->notify(new BloodRequestNotification($class,$usersent,$message));


                    // $mobile = $user->contactinfo;
                    // Chikka::send($mobile, $message);
            }
            }
        }
        return redirect('/admin/request')->with('status', 'Request successfully accepted. Notified eligible donors!');
    }

    public function claimRequest(Request $request)
    {
        // dd(Carbon::now()->toDateTimeString());
        // return response()->json($request->input());
        $bloodRequest = BloodRequest::find($request->input('acceptId'));
        $user = $bloodRequest->user;
        $class = array("class" => "App\BloodRequest",
            "id" => $bloodRequest->id,
            "time" => Carbon::now()->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $user->notify(new BloodRequestNotification($class,$usersent,'Your blood bags are ready to be claimed. Please come as soon as possible.'));
        return response()->json(['status' => 'User successfully notified!']);
    }

    public function showCompleteRequest(Request $request, BloodRequest $bloodRequest)
    {
        if($bloodRequest->status == "Done" || $bloodRequest->status == "Declined")
        {
            return redirect("admin/request");            
        }
        if(Auth::guard('web_admin')->user()->institute->settings['patient-directed'] == 'false')
        {
        $availBloods = $bloodRequest->details->bloodType->nonReactive(Auth::guard('web_admin')->user()->institute->id);
        $selectedBloods = collect();

        return view('admin.completerequestform',compact('bloodRequest','availBloods','selectedBloods'));
        }
        else
        {
            $tmpAvailBloods = $bloodRequest->details->bloodType->nonReactive(Auth::guard('web_admin')->user()->institute->id);
            $selectedBloods = BloodInventory::has('screenedBlood.donation.bloodrequest')->where('status','Available')->get();
            $availBloods = $tmpAvailBloods->diff($selectedBloods); 
            // dd($availBloods);        
            return view('admin.completerequestform',compact('bloodRequest','availBloods','selectedBloods')); 
        }
    }
    public function updateToDone(Request $request, BloodRequest $bloodRequest)
    {

        BloodInventory::whereIn('id',$request->input('serial'))
        ->update([
            'status' => 'Sold',
            'br_detail_id' => $bloodRequest->details->id,
            'updated_at' => Carbon::now()->toDateTimeString()
            ]);

        if(count($bloodRequest->releasedBlood) != $bloodRequest->details->units)
        {
            $cnt = count($request->input('serial'));
            $updates = $bloodRequest->updates;              
            $updates[] = 'You have just given '.$cnt.' blood bags';   
             
            $bloodRequest->update([
                'updates' => $updates,
                'updated_at' => Carbon::now()->toDateTimeString()
                ]);
            $bloodRequest->details()->update([
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

            Log::create([
                'initiated_id' => Auth::guard('web_admin')->user()->id,
                'initiated_type' => 'App\InstitutionAdmin',
                'reference_type' => 'App\BloodRequest',
                'reference_id' => $bloodRequest->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You just succesfully gave '.$cnt.' blood bags to a requester'
                ]);

            Log::create([
                'initiated_id' => $bloodRequest->user->id,
                'initiated_type' => 'App\User',
                'reference_type' => 'App\BloodRequest',
                'reference_id' => $bloodRequest->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You just succesfully received '.$cnt.' blood bags from the blood bank'
                ]);

            $user = $bloodRequest->user;
            $class = array("class" => "App\BloodRequest",
                "id" => $bloodRequest->id,
                "time" => $bloodRequest->created_at->toDateTimeString());
            $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                    "picture" => Auth::guard('web_admin')->user()->institute->picture());

            $user->notify(new BloodRequestNotification($class,$usersent,'We have given you '.$cnt.' blood bags for your request'));
        }
        else
        {
            $updates = $bloodRequest->updates;              
            $updates[] = 'The blood request is completed and finished';   
             
            $bloodRequest->update([
                'status' => 'Done',
                'updates' => $updates,
                'updated_at' => Carbon::now()->toDateTimeString()
                ]);
            $bloodRequest->details()->update([
                'status' => 'Done',
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

            Log::create([
                'initiated_id' => Auth::guard('web_admin')->user()->id,
                'initiated_type' => 'App\InstitutionAdmin',
                'reference_type' => 'App\BloodRequest',
                'reference_id' => $bloodRequest->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You just succesfully completed a blood request transaction!'
                ]);

            Log::create([
                'initiated_id' => $bloodRequest->user->id,
                'initiated_type' => 'App\User',
                'reference_type' => 'App\BloodRequest',
                'reference_id' => $bloodRequest->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You just succesfully completed a blood request transaction!'
                ]);

            $user = $bloodRequest->user;
            $class = array("class" => "App\BloodRequest",
                "id" => $bloodRequest->id,
                "time" => $bloodRequest->created_at->toDateTimeString());
            $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                    "picture" => Auth::guard('web_admin')->user()->institute->picture());

            $user->notify(new BloodRequestNotification($class,$usersent,'We have completed your blood request.'));
        }
    

        // $hero = $bloodRequest->user;
        // dispatch(new SendTextBlast($hero,$message)); 

        return redirect('/admin/request')->with('status', 'Request successfully completed!');
    }

    public function deleteRequest(Request $request)
    {
        $bloodRequest = BloodRequest::find($request->input('id'));
        // dd($request->input());
        $updates = $bloodRequest->updates;
        $updates[] = $bloodRequest->institute->name().' has declined your blood request';
        // dd($updates);
        $bloodRequest->update([
            'status' => 'Declined',
            'reason' => $request->input('message'),
            'updates' => $updates,
            'updated_at' => Carbon::now()->toDateTimeString()
            ]);

        Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\BloodRequest',
            'reference_id' => $bloodRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You just declined a blood request!'
            ]);
        $hero = $bloodRequest->user;
        $message="Hi! Your blood request has been declined. For the reason of: ".$request->input('message');
        $class = array("class" => "App\BloodRequest",
            "id" => $bloodRequest->id,
            "time" => $bloodRequest->created_at->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $hero->notify(new BloodRequestNotification($class,$usersent,'We have declined your blood request.'));
        // dispatch(new SendTextBlast($hero,$message));
        return redirect('/admin/request')->with('status', 'Request successfully declined!');
    }

    public function sendTextBlast(Bloodrequest $request)
    {      
        $message = "Hi Hero! Somebody is in need of your ".$request->details->blood_type." blood. If you are willing and able to donate, please go to the Philippines Red Cross in Fuente and donate there."."Your blood will be donated to request id ".$request->uuid.". Thank you.";

        //retrieve donors with same blood type
        $heroes= User::where('bloodType',$request->details->blood_type)->get();
        //sort donors to those na maka donate lang(if previous donor d sa pwde);
        if($heroes->isEmpty())
            return redirect('/admin/request')->with('status', 'No available donors!');
        else{
            
        foreach($heroes as $hero)
        {
            dispatch(new SendTextBlast($hero,$message));
            // event(new DonorsTextBlasted($hero,$message));
        }
        }
    }
    public function sendMessage(Request $request)
    {
        //claim na ta ni.
        $bloodRequest = BloodRequest::find($request->input('id'));

        // $hero = $bloodRequest->user;
        // $message = $request->input('message');
        // dispatch(new SendTextBlast($hero,$message));
        $updates = $bloodRequest->updates;
        $updates[] = 'The request has been accepted and your blood is ready to be claimed';
        $bloodRequest->update([
            'status' => 'Done',
            'updates' => $updates
            ]);

        // if($bloodRequest->details->blood_type == 'AB+')
        //     $picture = asset('assets/img/bloodtype/ab+.jpg');
        // else if($bloodRequest->details->blood_type == 'AB-')
        //     $picture = asset('assets/img/bloodtype/ab-.jpg');
        // else if($bloodRequest->details->blood_type == 'A-')
        //     $picture = asset('assets/img/bloodtype/a-.jpg');
        // else if($bloodRequest->details->blood_type == 'A+')
        //     $picture = asset('assets/img/bloodtype/a+.jpg');
        // else if($bloodRequest->details->blood_type == 'B-')
        //     $picture = asset('assets/img/bloodtype/b-.jpg');
        // else if($bloodRequest->details->blood_type == 'B+')
        //     $picture = asset('assets/img/bloodtype/b+.jpg');
        // else if($bloodRequest->details->blood_type == 'O-')
        //     $picture = asset('assets/img/bloodtype/o-.jpg');
        // else if($bloodRequest->details->blood_type == 'O+')
        //     $picture = asset('assets/img/bloodtype/o+.jpg');
        // ??? if naa bay dugo, required ba mu post.
        // $post = Post::create([
        //     'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
        //     'message' => 'Someone is in need of blood my lords and ladies.',
        //     'picture' => $picture,
        //     'initiated_id' => Auth::guard('web_admin')->user()->id,
        //     'initiated_type' => 'App\InstitutionAdmin',
        //     'reference_type' => 'App\BloodRequest',
        //     'reference_id' => $bloodRequest->id
        //     ]);
        // if($bloodRequest->details->units >= $bloodRequest->details->bloodBag->qty)
        // {
        //     $bloodBag = $bloodRequest->details->bloodBag;
        //     $bloodBag->update(['qty' => $bloodBag->qty - $bloodRequest->details->units]);
        // }


        Log::create([   
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\BloodRequest',
            'reference_id' => $bloodRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You just accepted and completed a blood request!'
            ]);

        $user = $bloodRequest->user;
        Log::create([   
            'initiated_id' => $user->id,
            'initiated_type' => 'App\User',
            'reference_type' => 'App\BloodRequest',
            'reference_id' => $bloodRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'Your blood bag is ready to be claimed!'
            ]);

        $class = array("class" => "App\BloodRequest",
            "id" => $bloodRequest->id,
            "time" => $bloodRequest->created_at->toDateTimeString());

        $usersent = array(
            "name" => Auth::guard('web_admin')->user()->institute->name(),
            "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $user->notify(new BloodRequestNotification($class,$usersent,'We have just accepted your blood request. The blood is ready to be claimed'));

        return redirect('/admin/request')->with('status', 'Successfully sent message!');
    }
    public function notifyViaText(Request $request)
    {
        $var = $request->input('donorsArray');
        $users = User::whereIn('id',$var)->get();
        $message = $request->input('msg');
        foreach($users as $user)
        {
            dispatch(new SendTextBlast($user,$message));
        }
        return response()->json(Array('status' => 'OK'));
    }

    public function settings()
    {
        $institution = Auth::guard('web_admin')->user()->institute;
        return view('admin.settings',compact('institution'));
    }

    public function edit(Request $request, Institution $institution)
    {
      $institution_name = $request->input('institution_name');
      $address = array('place' => $request->input('exactcity'),
            'longitude' => $request->input('cityLng'),
            'latitude' => $request->input('cityLat'));
      $email_address = $request->input('email_address');
      $contact_information = $request->input('contact');

      $bloodbags = array();
      foreach($request->input('bag_brand') as $key => $value)
      {
        $brand = [
          $value => $request->input('bag_qty')[$key]
        ];
        $bloodbags += $brand;
      }
      $settings = [
        'patient-directed' => $request->input('reactive'),
        'bloodbags' => 
            $bloodbags,
        'bloodtype_available' => 
            $request->input('blood_bag_categories')
        ];
      $about_us = $request->input('about_us');
      $links = [
        'facebook' => $request->input('facebook'),
        'twitter' =>  $request->input('twitter'),
        'website' =>  $request->input('website'),
      ];

      $institution = Institution::update([
        'institution' => $institution_name,
        'address' => $address,
        'email_address' => $email_address,
        'contact_number' => $contact_information,
        'about_us' => $about_us,    
        'logo' => null,
        'links' => $links,
        'banner' => null, 
        'settings' => $settings,
        'status' => 'Pending'   
        ]);
    }
    
}

// $bloodbags = [
//     'Karmi' => [
//         'single' => [
//         ],
//         'dual' => [
//         ]]
// ]