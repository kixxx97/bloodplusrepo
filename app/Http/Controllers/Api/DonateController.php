<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DonateRequest;
use Auth;
use App\MedicalHistory;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Log as Log;
use Illuminate\Support\Facades\Log as SysLog;
use App\Notifications\BloodRequestNotification;

class DonateController extends Controller
{
	    //create donate request
	public function createDonateRequest(Request $request)
	{
		// dd(Auth::user()->id);
		if(!DonateRequest::where('initiated_by',Auth::user()->id)->where(function ($query) {
            $query->where('status','Pending')->orWhere('status','Ongoing');
        })->with('institute')->first())
    	{
            // $now=Carbon::now()->format('H:i');
	    	$validator = Validator::make($request->all(), [
	            'institution_id' => 'required',	
	            'donatedate' => 'required|date|after:yesterday',
            	'donatetime' => 'required|date_format:"H:i"'
	            
	        ]);

	        if($validator->fails()) {
            $message = $validator->messages();
            return response()->json($message);
        	}
	        $appointment_time = new Carbon($request->input('donatedate').' '.$request->input('donatetime'));
	        // dd(Auth::user()->id);
			// dd($appointment_time->format(' jS \\of F Y')); 
	        $donateRequest = DonateRequest::create([
	            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
	            'institution_id' => $request->input('institution_id'),
	            'initiated_by' => Auth::user()->id,
	            'appointment_time' => $appointment_time,
	            'status' => 'Pending'
	        ]);

	        if($donateRequest)
	        {
		        Log::create([
		            'initiated_id' => Auth::user()->id,
		            'initiated_type' => 'App\User',
		            'reference_id' => $donateRequest->id,
		            'reference_type' => 'App\DonateRequest',
		            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
		            'message' => 'You initiated a voluntary blood donation'
	            ]);
		        //notify
		        $admins = $donateRequest->institute->admins;
		        $class = array("class" => "App\BloodRequest",
		            "id" => $donateRequest->id,
		            "time" => $donateRequest->created_at->toDateTimeString());
		        $user = array("name" => Auth::user()->name(),
		                "picture" => Auth::user()->picture());
		        $message = Auth::user()->name().' initiated a voluntary blood donation!';
		        foreach($admins as $admin)  
		        {
		            $admin->notify(new BloodRequestNotification($class,$user,$message));
		        
		        }
		        return response()->json(array('donateRequest' => $donateRequest,
		        	'status' => 'Successful',
		        	'message' => 'You have initiated a voluntary donation!'));

	        }
        	else  
        	{  	
        		return response()->json(array('status' => 'Error','message' => 'Error Error Error'));
			}
		}
		else
		{
			return response()->json(array('status' => 'Error','message' => 'Ongoing Request'));
		}		
	}
    //retrieve ongoing voluntary donation
	public function getDonateRequest()
	{
		$donateRequest = DonateRequest::with('institute')->where('initiated_by',Auth::user()->id)->where(function ($query) {
			$query->where('status','Pending')->orWhere('status','Ongoing');
		})->with('institute')->first();
		if($donateRequest)
   			return response()->json([
   			'donateRequest' => $donateRequest,
            'status' => 'Successful',
   			'message' => 'Successfully retrieved ongoing volluntary donation request',
   			'nextDonation' => null	
   			]);
   		else
   		{
   			$lastDonation = DonateRequest::where('status','Done')->where('initiated_by',Auth::user()->id)->orderBy('appointment_time','desc')->first();
   			$now = Carbon::now();
   			if($lastDonation)
   			{
   			$date = $lastDonation->appointment_time;
   			if($date->addDays(90) >= $now)
   			{
   				$nextDonation = clone $date;
   				$monthLeft = $date->diffInMonths($now);
                $daysLeft = $date->subMonth($monthLeft)->diffInDays();
	   			return response()->json([
   				'donateRequest' => null,
   				'status' => 'Successful',
   				'nextDonation' => $nextDonation->toDateTimeString(),
   				'message' => 'Successsfully retrieved next donation date']);
   			}
   			}
   			return response()->json([
   			'donateRequest' => null,
            'status' => 'Error Error',
   			'message' => 'You have no ongoing blood request',
   			'nextDonation' => null
   			]);
   		}
	}

	public function createMedicalHistory(Request $request,DonateRequest $donateRequest 	)	
	{	
		//kuha.on request input answers
		// dd($request->input('answers');
		$answers = $request->input('answers');
		$medical_history = [
			[
			'Do you feel healthy today?' => [
			'answers' => $answers[0],
			'remarks' => '',
				],
			'In the past 4 weeks have you taken any medications and/or vaccinations?' => [
			'answers' => $answers[1],
			'remarks' => '',
				],
			'In the last 3 days have you taken aspirin?' => [
			'answers' => $answers[2],
			'remarks' => '',
				]
			],
		'In the past 3 months, have you: ' => [
			'Donated whole blood, platelets or plasma?' => [
			'answers' => $answers[3],
			'remarks' => '',
				]
			],
		'In the past 12 months, have you: ' => [
			'Received blood, blood products and/or had tissue/organ transplant or graft?' => [
			'answers' => $answers[4],
			'remarks' => '',
				],
			'Had surgical operation or dental extraction?' => [
			'answers' => $answers[5],
			'remarks' => '',
				],
			'Have you had a tattoo applied, ear piercing, acupuncture, accidental needle stick injury or accidental contact with blood?' => [
			'answers' => $answers[6],
			'remarks' => '',
				],
			'Had sexual contact with high risk individuals or in exchange for material or monetary gain?' => [
			'answers' => $answers[7],
			'remarks' => '',
				],
			'Engaged in unprotected, unsafe or casual sex' => [
			'answers' => $answers[8],
			'remarks' => '',
				],
			'Have you had jaundice/hepatitis/ personal contact with person who had hepatitis?' => [
			'answers' => $answers[9],
			'remarks' => '',
				],
			'Have you been incarcerated, jailed or imprisoned?' => [
			'answers' => $answers[10],
			'remarks' => '',
				],
			'Spent time or have relatives in the United Kingdoms or Europe' => [
			'answers' => $answers[11],
			'remarks' => '',
				]
			],
		'Have you ever: ' => [
			'Travelled or lived in outside of your place of residence in the Philippines' => [
			'answers' => $answers[12],
			'remarks' => '',
				],
			'Have you taken prohibited drugs (orally, by nose, or by injection)?' => [
			'answers' => $answers[13],
			'remarks' => '',
				],
			'Used clotting factor concentrates?' => [
			'answers' => $answers[14],
			'remarks' => '',
				],
			'Had a positive test for the HIV virus, Hepatities virus, Syphilis or Malaria?' => [
			'answers' => $answers[15],
			'remarks' => '',
				],
			'Had malaria or hepatitis in the past?' => [
			'answers' => $answers[16],
			'remarks' => '',
				],
			'Had or was treated for genital wart, syphilis, gonorrhea or other sexually transmitted diseases?' => [
			'answers' => $answers[17],
			'remarks' => '',
				]
			],
		'Had any of the following:' => [	
			'Cancer, Blood disease or bleeding disorder ( haemophilia )?' => [
			'answers' => $answers[18],
			'remarks' => '',
				],
			'Heart disease/surgery, rheumatic fever or chest pains?' => [
			'answers' => $answers[19],
			'remarks' => '',
				],
			'Lung disease, tuberculosis or asthma?' => [
			'answers' => $answers[20],
			'remarks' => '',
				],
			'Kidney disease, thyroid disease, diabetes, epilepsy?' => [
			'answers' => $answers[21],
			'remarks' => '',
				],
			'Chicken pox and/or cold sores?' => [
			'answers' => $answers[22],
			'remarks' => '',
				],
			'Any other chronic medical condition or  operations?' => [
			'answers' => $answers[23],
			'remarks' => '',
				]
		],
		[
			'Have you recently had rash and/or fever? Was/ Were this/these also associated with arthralgia or arthritis or conjunctivitis?' => [
			'answers' => $answers[24],
			'remarks' => '',
				]
		],
		'In the past 6 months have you: ' => [
			'Been to any places in the Philippines or countries infected with ZIKA virus?' => [
			'answers' => $answers[25],
			'remarks' => '',
				],
			'Had sexual contact with a person who was confirmed to have ZIKA Virus infection?' => [
			'answers' => $answers[26],
			'remarks' => '',
				],
			'Had sexual contact with a person who has been to any places in the Philippines or countries affected with ZIKA Virus?' => [
			'answers' => $answers[27],
			'remarks' => '',
				],
			'Are you giving blood only because you want to be tested for HIV / AIDS virus or Hepatitis virus?' => [
			'answers' => $answers[28],
			'remarks' => '',
				],
			'Are you aware that an HIV / Hepatitis infected person can still  transmit the virus despite a negative HIV / Hepatitis test?' => [
			'answers' => $answers[29],
			'remarks' => '',
				],
			'Have you within the last 12 hours had taken liquor, beer or any drinks with alcohol?' => [
			'answers' => $answers[30],
			'remarks' => '',
				],
			'Have you ever been refused as a blood donor or told not to donate blood for any reasons?' => [
			'answers' => $answers[31],
			'remarks' => '',
				]
		],
		'FOR FEMALE DONORS ONLY' => [
			'Are you currently pregnant?' => [
			'answers' => $answers[32],
			'remarks' => '',
				],
			'Have you ever been pregnant?' => [
			'answers' => $answers[33],
			'remarks' => '',
				],
			'When was your last delivery?' => [
			'answers' => $answers[34],
			'remarks' => '',
				],
			'Do you have an abortion in the past 1 year?' => [
			'answers' => $answers[35],
			'remarks' => '',
				],
			'When was your last menstrual period? Date: ' => [
			'answers' => $answers[36],
			'remarks' => '',
				],
			'Are you currently breastfeeding?' => [
			'answers' => $answers[37],
			'remarks' => '',
				]
		]
		];
		$donateRequest->medicalHistory()->update([
			'medical_history' => json_encode($medical_history),
			'status' => 'Ongoing'
			]);
		return response()->json([
			'medical_history' => json_encode($medical_history),
			'status' => 'Successful',
			'message' => 'Successfully sent user\'s medical history'
			]);
		
		// dd($question);
	}	

	public function retrieveMedicalHistory(DonateRequest $donateRequest)
	{
		if($donateRequest->initiated_by == Auth::user()->id)
		{
		if($donateRequest->medicalHistory)
		{
		return response()->json([
			'medical_history' => $donateRequest->medicalHistory,
			'status' => 'Successful',
			'message' => 'Successfully retrieved user\'s medical history'
			]);
		}
		else
		{
		return response()->json([
			'medical_history' => null,
			'status' => 'Error Error',
			'message' => 'User has no medical history for this donation'
			]);	
		}
		}
		else
		return response()->json([
			'medical_history' => null,
			'status' => 'Error Error',
			'message' => 'Restricted access'
			]);
	}

	public function acceptAppointment(Request $request,DonateRequest $donateRequest)
	{
		if($request->input('remarks') == 'true'){
			$donateRequest->update([
			'status' => 'Ongoing',
            'updated_at' => Carbon::now()->toDateTimeString()
        	]);
		}
		elseif ($request->input('remarks') == 'cancel') {
			$donateRequest->update([
			'status' => 'Declined', 	
            'updated_at' => Carbon::now()->toDateTimeString()
        	]);
		}
		// elseif ($request->input('remarks') == 'change') 
		// {
		// 	$appointmentTime = new Carbon($request->input('donatedate').' '.$request->input('donatetime'));
		// 	$updates = $donateRequest->updates;
	 //        //change to carbon time 09:00 AM/PM;
	 //        $updates[] = Auth::guard()->user->name(). " blood donation appointment time to ".$appointmentTime->format('h:i A').".";
		// 	$donateRequest->update([
  //           'appointment_time' => $appointmentTime,
  //           'status' => 'Pending',
  //           'updates' => $updates,
  //           'updated_at' => Carbon::now()->toDateTimeString(),
  //           'flag' => 1,
  //       ]);
		return respone()->json('succesful');
	}
}
// $medical_history = [
// 			[
// 			'Do you feel healthy today?' => [
// 			'answers' => $answers[0],
// 			'remarks' => '',
// 				],
// 			'In the past 4 weeks have you taken any medications and/or vaccinations?' => [
// 			'answers' => $answers[1],
// 			'remarks' => '',
// 				],
// 			'In the last 3 days have you taken aspirin?' => [
// 			'answers' => $answers[2],
// 			'remarks' => '',
// 				]
// 			],
// 		'In the past 3 months, have you: ' => [
// 			'Donated whole blood, platelets or plasma?' => [
// 			'answers' => $answers[3],
// 			'remarks' => '',
// 				]
// 			],
// 		'In the past 12 months, have you: ' => [
// 			'Received blood, blood products and/or had tissue/organ transplant or graft?' => [
// 			'answers' => $answers[4],
// 			'remarks' => '',
// 				],
// 			'Had surgical operation or dental extraction?' => [
// 			'answers' => $answers[5],
// 			'remarks' => '',
// 				],
// 			'Have you had a tattoo applied, ear piercing, acupuncture, accidental needle stick injury or accidental contact with blood?' => [
// 			'answers' => $answers[6],
// 			'remarks' => '',
// 				],
// 			'Had sexual contact with high risk individuals or in exchange for material or monetary gain?' => [
// 			'answers' => $answers[7],
// 			'remarks' => '',
// 				],
// 			'Engaged in unprotected, unsafe or casual sex' => [
// 			'answers' => $answers[8],
// 			'remarks' => '',
// 				],
// 			'Have you had jaundice/hepatitis/ personal contact with person who had hepatitis?' => [
// 			'answers' => $answers[9],
// 			'remarks' => '',
// 				],
// 			'Have you been incarcerated, jailed or imprisoned?' => [
// 			'answers' => $answers[10],
// 			'remarks' => '',
// 				],
// 			'Spent time or have relatives in the United Kingdoms or Europe' => [
// 			'answers' => $answers[11],
// 			'remarks' => '',
// 				]
// 			],
// 		'Have you ever: ' => [
// 			'Travelled or lived in outside of your place of residence in the Philippines' => [
// 			'answers' => $answers[12],
// 			'remarks' => '',
// 				],
// 			'Have you taken prohibited drugs (orally, by nose, or by injection)?' => [
// 			'answers' => $answers[13],
// 			'remarks' => '',
// 				],
// 			'Used clotting factor concentrates?' => [
// 			'answers' => $answers[14],
// 			'remarks' => '',
// 				],
// 			'Had a positive test for the HIV virus, Hepatities virus, Syphilis or Malaria?' => [
// 			'answers' => $answers[15],
// 			'remarks' => '',
// 				],
// 			'Had malaria or hepatitis in the past?' => [
// 			'answers' => $answers[16],
// 			'remarks' => '',
// 				],
// 			'Had or was treated for genital wart, syphilis, gonorrhea or other sexually transmitted diseases?' => [
// 			'answers' => $answers[17],
// 			'remarks' => '',
// 				]
// 			],
// 		'Had any of the following' => [	
// 			'Cancer, Blood disease or bleeding disorder ( haemophilia )?' => [
// 			'answers' => $answers[18],
// 			'remarks' => '',
// 				],
// 			'Heart disease/surgery, rheumatic fever or chest pains?' => [
// 			'answers' => $answers[19],
// 			'remarks' => '',
// 				],
// 			'Lung disease, tuberculosis or asthma?' => [
// 			'answers' => $answers[20],
// 			'remarks' => '',
// 				],
// 			'Kidney disease, thyroid disease, diabetes, epilepsy?' => [
// 			'answers' => $answers[21],
// 			'remarks' => '',
// 				],
// 			'Chicken pox and/or cold sores?' => [
// 			'answers' => $answers[22],
// 			'remarks' => '',
// 				],
// 			'Any other chronic medical condition or  operations?' => [
// 			'answers' => $answers[23],
// 			'remarks' => '',
// 				]
// 		],
// 		[
// 			'Have you recently had rash and/or fever? Was/ Were this/these also associated with arthralgia or arthritis or conjunctivitis?' => [
// 			'answers' => $answers[24],
// 			'remarks' => '',
// 				]
// 		],
// 		'In the past 6 months have you: ' => [
// 			'Been to any places in the Philippines or countries infected with ZIKA virus?' => [
// 			'answers' => $answers[25],
// 			'remarks' => '',
// 				],
// 			'Had sexual contact with a person who was confirmed to have ZIKA Virus infection?' => [
// 			'answers' => $answers[26],
// 			'remarks' => '',
// 				],
// 			'Had sexual contact with a person who has been to any places in the Philippines or countries affected with ZIKA Virus?' => [
// 			'answers' => $answers[27],
// 			'remarks' => '',
// 				],
// 			'Are you giving blood only because you want to be tested for HIV / AIDS virus or Hepatitis virus?' => [
// 			'answers' => $answers[28],
// 			'remarks' => '',
// 				],
// 			'Are you aware that an HIV / Hepatitis infected person can still  transmit the virus despite a negative HIV / Hepatitis test?' => [
// 			'answers' => $answers[29],
// 			'remarks' => '',
// 				],
// 			'Have you within the last 12 hours had taken liquor, beer or any drinks with alcohol?' => [
// 			'answers' => $answers[30],
// 			'remarks' => '',
// 				],
// 			'Have you ever been refused as a blood donor or told not to donate blood for any reasons?' => [
// 			'answers' => $answers[31],
// 			'remarks' => '',
// 				]
// 		],
// 		'FOR FEMALE DONORS ONLY' => [
// 			'Are you currently pregnant?' => [
// 			'answers' => $answers[32],
// 			'remarks' => '',
// 				],
// 			'Have you ever been pregnant?' => [
// 			'answers' => $answers[33],
// 			'remarks' => '',
// 				],
// 			'When was your last delivery?' => [
// 			'answers' => $answers[34],
// 			'remarks' => '',
// 				],
// 			'Do you have an abortion in the past 1 year?' => [
// 			'answers' => $answers[35],
// 			'remarks' => '',
// 				],
// 			'When was your last menstrual period? Date: ' => [
// 			'answers' => $answers[36],
// 			'remarks' => '',
// 				],
// 			'Are you currently breastfeeding?' => [
// 			'answers' => $answers[37],
// 			'remarks' => '',
// 				]
// 		]];



