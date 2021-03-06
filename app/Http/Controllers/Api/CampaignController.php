<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\InstitutionAdmin;
use App\Http\Controllers\Controller;
use App\Campaign;
use App\Attendance;
use Auth;
use Carbon\Carbon;
use App\Log;
use App\User;
use App\Institution;
use App\Notifications\BloodRequestNotification;


class CampaignController extends Controller
{
    
    public function getCampaigns()
 	{
 		$campaigns = Campaign::with('initiated.institute')->get();
 		$tmpCampaigns = $campaigns;
 		return response()->json($campaigns);
 	}

 	public function retrieveCampaigns()
 	{
 		$campaigns = Attendance::with('user')->where('user_id',Auth::user()->id)->get();
 		// dd($campaigns);
 		return response()->json(array('campaigns' => $campaigns,
 		'status' => 'Successful',
 		'message' => 'Retrieved all of the user\'s campaign'));	
 	}
 	public function joinCampaign(Campaign $campaign)
 	{	
 		//make unique campaign number nga increment.
 		$attended = Attendance::where('user_id', Auth::user()->id)->where('campaign_id',$campaign->id)->get();
 		if(count($attended) == 0)
 		{
		$attendance = Attendance::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
			'user_id' => Auth::user()->id,
			'campaign_id' => $campaign->id,
			'status' => 'Going'
			]);

		$i_id = $attendance->campaign->initiated->institution_id;
        
		$admins = InstitutionAdmin::where('institution_id',$i_id)->get();
		$class = array("class" => "App\Campaign",
            "id" => $campaign->id,
            "time" => Carbon::now()->toDateTimeString());
        $user = array("name" => Auth::user()->name(),
                "picture" => Auth::user()->picture());
        $message = Auth::user()->name().' is going to attend your campaign!';
        Log::create([   
            'initiated_id' => Auth::user()->id,
            'initiated_type' => 'App\User',
            'reference_type' => 'App\Campaign',
            'reference_id' => $campaign->id,
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
           'message' => 'You are going to attend '.$campaign->name."."
            ]);
        foreach($admins as $admin)  
        {
            $admin->notify(new BloodRequestNotification($class,$user,$message));
        }


		return response()->json(array('attendance' => $attendance,
			'status' => 'Successful',
			'message' => 'You have joined the campaign'));
		}
		else
		{
		return response()->json(array('attendance' => null,
			'status' => 'Error',
			'message' => 'You already have joined this campaign'));			
		}
 	}

 	public function getSpecificCampaign(Campaign $campaign)
 	{
 		$campaign->load(['initiated.institute','attendance.user']);
 		$user = array();
 		$count = 0;
 		$join = false;
 		foreach($campaign->attendance as $attendance)
 		{
 			$user[$count]['name'] = $attendance->user->name();
 			$user[$count]['id'] =  $attendance->user->id;
 			$user[$count]['picture'] = $attendance->user->picture();
 			$count++;
 			if($attendance->user->id == Auth::user()->id)
 			{
 				$join = true;
 			}
 		}

 		$json = array([
 			'id' => $campaign->id,
 			'name' => $campaign->name,
 			'description' => $campaign->description,
 			'picture' => $campaign->picture,
 			'date_start' => $campaign->date_start,
 			'date_end' => $campaign->date_end,
 			'address' => $campaign->address,
 			'status' => $campaign->status,
 			'initiated' => array([
 				'institute' => $campaign->initiated->name()
 				]),
 			'attendance' => $user,
 			'joined' => $join
 			]);
 		
 		return response()->json($json);
 	}
 	//history campaigns

    public function remarkAttendee(Request $request)
    {
        Log::create(['id' => '1', 'message' => $request->input()]);
        if($campaign->status == 'Ongoing')
        {
            $attendee = Attendance::where('user_id',Auth::user()->id)->where('campaign_id',$campaign->id)->first();

            if($attendee)
            {
                $attendee->update([
                    'remarks' => 'Attended',
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);
            }
            else
            {
               $attendance = Attendance::create([
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'user_id' => Auth::user()->id,
                'campaign_id' => $campaign->id,
                'status' => 'Going',
                'remarks' => 'Attended'
                ]);    
            }
            return response()->json(array(
            'status' => 'Successful',
            'message' => 'Successfully marked as present'));
        }
        else
            return response()->json(array(
                'status' => 'Error',
                'message' => 'Couldn\'t join finish campaigns'));
    }
    public function getInstitutionCampaigns(Institution $institution)
    {
        $campaigns = $institution->campaigns;
        return response()->json(array('campaigns' => $campaigns,
            'status' => 'Successful',
            'message' => 'Successfully retrieved campaigns initiated by the institution'));
    }
}
