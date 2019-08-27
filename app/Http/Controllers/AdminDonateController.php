<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\DonateRequest;
use App\Blacklist;
use Auth;
use App\Notifications\BloodRequestNotification;
use App\Post;
use App\Log;
use App\BloodType;
use App\BloodRequestDetail;
use \DB;
use App\BloodInventory;
use App\ScreenedBlood;
use PDF;
use App\MedicalHistory;



class AdminDonateController extends Controller
{

    public function donate()
    {
   		$id = Auth::guard('web_admin')->user()->institute->id;
        $donor_requests = DonateRequest::where('institution_id',$id)->where(function($query) 
        {
            $query->whereIn('status',['Ongoing','Pending'])->whereDate('appointment_time', '>',DB::raw('CURDATE()'));
        })->orderBy('appointment_time')->get();
        // dd($donor_requests);
        $todayRequests = DonateRequest::where('institution_id',$id)->where(function ($query) {
            $query->whereIn('status',['Pending','Ongoing'])->whereNull('appointment_time');
        })->orWhere(function ($query) use ($id) {
            $query->where('institution_id',$id);
            $query->whereIn('status',['Pending','Ongoing'])->whereDate('appointment_time','<=',DB::raw('CURDATE()'));
        })->orderBy('appointment_time')->get();
        $doneRequests = DonateRequest::where('institution_id',$id)->where('status','Done')->get();
        $cancelledRequests = DonateRequest::where('institution_id',$id)->where('status','Declined')->get();

        // dd($donor_requests);
    	return view('admin.donate',compact('donor_requests','todayRequests','doneRequests','cancelledRequests','donor_requests'));
    }

    public function setAppointment(Request $request)
    {
        // dd($request->input());
        // $hourmin = explode(':' ,$request->input('donatetime'));
        // $hour = $hourmin[0];
        // $min = $hourmin[1];
        // dd($hourmin);
        $appointmentTime = new Carbon($request->input('donatedate').' '.$request->input('donatetime'));
        // dd($appointment_time);
        $donateRequest = DonateRequest::find($request->input('id'));
    	// $donateRequest = DonateRequest::firstOrFail();

        $updates = $donateRequest->updates;
        //change to carbon time 09:00 AM/PM;
        $updates[] = Auth::guard('web_admin')->user()->name()." blood donation appointment time to ".$appointmentTime->format('h:i A').".";
        // change status to ongoing
        $donateRequest->update([
            'appointment_time' => $appointmentTime,
            'status' => 'Ongoing',
            'updates' => $updates,
            'updated_at' => Carbon::now()->toDateTimeString(),
            'flag' => 1,
        ]);
        // dd($donateRequest);
        MedicalHistory::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'donate_request_id' => $donateRequest->id,
            'status' => 'Pending' ,
        ]);
        Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\DonateRequest',
            'reference_id' => $donateRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'Changed donation appointment time of donor'
        ]);

        $user = $donateRequest->user;
        $class = array("class" => "App\DonateRequest",
            "id" => $donateRequest->id,
            "time" => Carbon::now()->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $user->notify(new BloodRequestNotification($class,$usersent,'We have changed your appointment time to '.$appointmentTime->format('h:i A')));

    	return redirect('/admin/donate')->with('status','Sent notification to user for the change of time');
    }

    public function acceptRequest(Request $request, DonateRequest $donateRequest)
    {
        $donateRequest = DonateRequest::find($request->input('id'));
        // dd($donateRequest);
        $updates = $donateRequest->updates;
        // dd($updates);
        $updates[] = "Philippine Red Cross accepted your blood donation request.";
        // dd($updates);
        $donateRequest->update([
            'status' => 'Ongoing',
            'updates' => $updates,
            'updated_at' => Carbon::now()->toDateTimeString()]);
        $donateRequest->bloodrequest()->update([
        'status' => 'Ongoing',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        MedicalHistory::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'donate_request_id' => $donateRequest->id,
            'status' => 'Pending' ,
        ]);
        Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\DonateRequest',
            'reference_id' => $donateRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You have accepted a voluntary blood donation'
            ]);
        $user = $donateRequest->user;
        $class = array("class" => "App\DonateRequest",
            "id" => $donateRequest->id,
            "time" => $donateRequest->created_at->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        //message
        $user->notify(new BloodRequestNotification($class,$usersent,'We have just accepted your donation request. See you at !'.$donateRequest->created_at->format('h:i A')));

        // dd($donateRequest->updates);
        return redirect('/admin/donate')->with('status','Successfully accepted request');
    }

    public function declineRequest(Request $request)
    {
        // dd($request->input());
        $donateRequest = DonateRequest::find($request->input('id'));
        if($donateRequest)
        {
            // dd($donateRequest);

            $updates = $donateRequest->updates;
            $updates[] = "Declined the donation request.";
            $donateRequest->update([
                'reason' => $request->input('message'),
                'status' => 'Declined',
                'updates' => $updates,
                'updated_at' => Carbon::now()->toDateTimeString()]);
            if($request->input('blacklist') == 'true')
            {
                $blacklist = Blacklist::create([
                    'user_id' => $donateRequest->user->id,
                    'reason' => $request->input('message'),
                    'status' => 'Active'
                    ]);
                // dd($blacklist);
            }
            // dd('12345');
            //log
            Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\DonateRequest',
            'reference_id' => $donateRequest->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You have declined a donate request'
            ]);
            $user = $donateRequest->user;
            $class = array("class" => "App\DonateRequest",
                "id" => $donateRequest->id,
                "time" => $donateRequest->created_at->toDateTimeString());
            $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                    "picture" => Auth::guard('web_admin')->user()->institute->picture());

            $user->notify(new BloodRequestNotification($class,$usersent,'We have declined your donation request.'));
        }

        return redirect('/admin/donate')->with('status','Successfully declined the donation request');

    }
    public function getDonationRequest(DonateRequest $donate)
    {
        //show interview questions

        //terms and agreement

        return view('admin.showdonation',compact('donate'));
    }

    public function retrieveMedicalHistory(DonateRequest $donate)
    {
        $medicalHistory = $donate->medicalHistory;
        $count=1;
        return view('admin.showMedicalHistory',compact('donate','medicalHistory','count'));
    }

    public function remarkOnMedicalHistory(Request $request, DonateRequest $donate, MedicalHistory $medicalHistory)
    {
        $remark = $request->input('remark');
        // dd($remark);
        if($remark == 'true')
        {
            $medicalHistory->update([
                'remarks' => 'Passed',
                'status' => 'Done',
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
            return redirect('/admin/donate/'.$donate->id.'/complete')->with('status','Succesffuly passed the donor\'s medical form');
        }
        else
        {
            $medicalHistory->update([
                'remarks' => 'Failed',
                'status' => 'Done',
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
            return redirect('/admin/donate/')->with('status','Successfully remarked the donor\'s medical form');    
        }
    }
    public function completeDonateRequestView(Request $request, DonateRequest $donate)
    {
        if($donate->medicalHistory->remarks == 'Passed')
        {
            // dd($donate->bloodrequest);
            $updates = array();
            if($donate->bloodRequest)
            {
                $details = $donate->bloodRequest->request->details;
                $units = $details->units;
                $cat = $details->blood_category;
                // dd($category);
                $updates[] = "A requester demand(s) ".$units." bag(s) of ".$cat; 
            }
            // dd($updates);
            $sameBloodTypes = BloodRequestDetail::select(DB::raw('blood_category, SUM(units) as total_units'))
            ->where('blood_type',$donate->user->bloodType)->where('status','Ongoing')->groupBy('blood_category')->get();

            // dd($sameBloodTypes);
            // $updates = null;
            if(count($sameBloodTypes) != 0)
            {

                foreach($sameBloodTypes as $type)
                {
                    if($type->total_units > 1)
                    {
                        $needed = " are demanded.";
                        $bags = " bags of ";
                    }
                    else
                    {
                        $bags = " bag of ";
                        $needed = " is demanded.";
                    }
                    $updates[] = $type->total_units.$bags.$type->blood_category.$needed;
                }
            }
            // dd($updates);
            return view('admin.completedonation',compact('donate','updates'));
        }
                else
        return redirect("/admin/donate/".$donate->id."/medical_history/retrieve");
    }
    public function completeDonateRequest(Request $request, DonateRequest $donate)
    {

        $donateRequest = $donate;

        $validation = Validator::make($request->all(), [
            'serial_number' => 'required|max:255|unique:screened_bloods',
            ]);

        $validation->validate();
        $updates = $donateRequest->updates;
        $updates[] = "Completed your blood donation.";
        $donateRequest->update([
            'status' => 'Done',
            'updates' => $updates,
            'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        $donateRequest->bloodrequest()->update([
        'status' => 'Done',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        $donate->user->update([
            'bloodType' => $request->input('bloodType')
        ]);

        Post::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'initiated_id' => $donateRequest->user->id,
            'initiated_type' =>  'App\User',
            'reference_type' => 'App\DonateRequest',
            'reference_id' => $donateRequest->id,
            'message' => 'I have just completed a voluntary blood donation. You should too!',
            'picture' => asset('assets/img/posts/blood-donation.jpg')
        ]);
        Log::create([
            'initiated_id' => $donateRequest->user->id,
            'initiated_type' => 'App\User',
            'reference_id' => $donateRequest->id,
            'reference_type' => 'App\DonateRequest',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You have succefully finished your blood donation'
            ]);

        Log::create([
            'initiated_id' => Auth::guard('web_admin')->user()->id,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_id' => $donateRequest->id,
            'reference_type' => 'App\DonateRequest',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You have completed a voluntary  blood donation'
            ]);
        
        $user = $donateRequest->user;
        $class = array("class" => "App\DonateRequest",
            "id" => $donateRequest->id,
            "time" => $donateRequest->created_at->toDateTimeString());
        $usersent = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());

        $user->notify(new BloodRequestNotification($class,$usersent,'We have completed your blood donation. Thank you for donating.'));
        
        // stage the bags

        $screenedBlood = ScreenedBlood::create([
        'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
        'donate_id' => $donateRequest->id,
        'serial_number' => $request->input('serial_number'),
        'bag_type' => $request->input('bag_type'),
        'bag_component' => $request->input('bag_component'),
        'status' => 'Pending'
        ]);

        return redirect('/admin/bloodbags')->with('status','You successfully completed the blood donation. You can now begin to screen the blood bag');

    }

    public function showPdf(DonateRequest $donate)
    {
        return view('pdf.donationpdf',compact('donate'));
    }

    public function downloadPdf(DonateRequest $donate)
    {
        // return view('pdf.donationpdf',compact('donate'));

        return PDF::loadview('pdf.donationpdf',compact('donate'))->setOptions(['dpi' => 96])->setPaper('folio','portrait')->stream('Donation Form.pdf');
    }
}

