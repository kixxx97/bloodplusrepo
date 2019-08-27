<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Campaign;
use Auth;
use App\Post;
use App\Log;
use App\Institution;
use \QrCode;
use PDF;
use App\Notifications\CampaignNotification;
use App\Notifications\BloodRequestNotification;

class AdminCampaignController extends Controller
{
    public function campaign() {
        $pendingCampaigns = Campaign::with('initiated.institute')->whereHas('initiated.institute', function ($query) {
            $query->where('id',Auth::guard('web_admin')->user()->institute->id);
        })->where('status','Pending')->get();
        // dd($pendingCampaigns);
        
        $ongoingCampaigns = Campaign::with('initiated.institute')->whereHas('initiated.institute', function ($query) {
            $query->where('id',Auth::guard('web_admin')->user()->institute->id);
        })->where('status','Ongoing')->orderBy('date_start','asc')->get();

        $doneCampaigns = Campaign::with('initiated.institute')->whereHas('initiated.institute', function ($query) {
            $query->where('id',Auth::guard('web_admin')->user()->institute->id);
        })->where('status','Done')->orderBy('date_start','desc')->get();

    	return view('admin.campaign',compact('pendingCampaigns','ongoingCampaigns','doneCampaigns'));
    }

    public function createCampaign(Request $request)
    {
    	$validation = Validator::make($request->all(), [
            'campaign_name' => 'required|string|max:255',
            'campaign_description' => 'required|string|max:255',
            'exactcity' => 'required|string',
            'campaign_date' => 'nullable|date|after:yesterday',
            'start_time' => 'nullable|date_format:H:i',
            'campaign_avatar' => 'image',
            'end_time' => 'nullable|date_format:H:i',
            'quota' => 'nullable|integer'
            ]);

        // $validation->validate();
        $address = 
            array('place' => $request->input('exactcity'),
            'longitude' => $request->input('cityLng'),
            'latitude' => $request->input('cityLat'));
        // print_r($address);
    
        $id = $str = strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7));
        if($request->input('type') == 'Crowdfunding')
        {
            $type = 'Crowdfunding';
            $date_start = Carbon::parse($request->input('campaign_date'));
            $date_end = Carbon::parse($request->input('campaign_date'))->addDays(15);
            $quota = $request->input('quota');
        }
        else
        {
        $date_start = new Carbon($request->input('campaign_date').' '.$request->input('start_time'));
        $date_end = new Carbon($request->input('campaign_date').' '.$request->input('end_time'));
        $quota = null;
        $type = $request->input('type');
        }
        if($request->file('campaign_avatar'))
        {
        $ext = \File::extension($request->file('campaign_avatar')->getClientOriginalName()); 
        $path = $request->file('campaign_avatar')->storeAs('campaigns',$id.'.'.$ext);
        // dd($path);
        $picture = asset('/storage/'.$path);

        }
        else
            $picture = null;
        
        $initiated_by = Auth::guard('web_admin')->user()->id;
        $create = Campaign::create([
        	'id' => $id,
        	'name' => $request->input('campaign_name'),
        	'address' => $address,
        	'description' => $request->input('campaign_description'),
        	'date_start' => $date_start, 
        	'date_end' => $date_end,
        	'status' => 'Pending',
            'initiated_by' => $initiated_by,
            'picture' => $picture,
            'type'  => $type,
            'quota' => $quota
        	]);
        // dd($create->id);
        // dd($initiated_by);
        if($picture == null)
            $picture = asset('assets/img/posts/blood-donation.jpg');

        $post = Post::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'We have recently initiated a campaign. Come and join our cause!',
            'picture' => $picture,
            'initiated_id' => $initiated_by,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_type' => 'App\Campaign',
            'reference_id' => $create->id
            ]);
        Log::create([
            'initiated_id' => $initiated_by,
            'initiated_type' => 'App\InstitutionAdmin',
            'reference_id' => $create->id,
            'reference_type' => 'App\Campaign',
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You initiated a campaign!'
            ]);
        $institute = Institution::find(Auth::guard('web_admin')->user()->institute->id);
        $followers = $institute->followers;
        // dd('niagi dri');
        $campaign = $create;
        $class = array("class" => "App\Campaign",
            "id" => $campaign->id,
            "time" => $campaign->created_at->toDateTimeString());
        $user = array("name" => Auth::guard('web_admin')->user()->institute->name(),
                "picture" => Auth::guard('web_admin')->user()->institute->picture());
        $message = Auth::guard('web_admin')->user()->institute->name().' just initiated a campaign. Join Now!';
        foreach($followers as $follower)
        {
            $follower->notify(new CampaignNotification($class,$user,$message));
        }
        return redirect('/admin/campaign/'.$create->id)->with('status', 'Campaign successfully made!');
    	// $contents = Storage::url('avatars/'.$name);
    	// dd($request);
    }

    public function viewCampaign(Campaign $campaign)
    {
        
        return view('admin.showCampaign',compact('campaign'));
    }
    public function showCreate()
    {
        return view('admin.createcampaign');
    }

    public function generateQrCode(Campaign $campaign)
    {
        $id = $campaign->id;
    $attendance = $campaign->attendance;
    $attendanceIds = array();
    foreach($attendance as $attendance)
    {
            $attendanceIds[] = $attendance->user_id;
    }
    $data = json_encode([
            'campaignId' => $id]);
    $qr = base64_encode(QrCode::format('png')->size(400)->generate($data));

    return PDF::loadview('pdf.campaignpdf',compact('campaign','qr'))->setOptions(['dpi' => 96])->setPaper('folio','portrait')->stream('Donation Form.pdf');
    }

    public function attendance(Campaign $campaign)
    {
        $campaign->load(['attendance']);
            return view('pdf.attendancepdf',compact('campaign'));
        return PDF::loadview('pdf.attendancepdf',compact('campaign'))->setOptions(['dpi' => 96])->setPaper('folio','portrait')->stream('Donation Form.pdf');
        // return PDF::loadview('pdf.campaignpdf',compact('campaign','qr'))->setOptions(['dpi' => 96])->setPaper('folio','portrait')->stream('Donation Form.pdf');
    }


}
