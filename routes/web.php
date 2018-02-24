<?php

use App\Log;
use App\Institution;
use App\InstitutionAdmin;
use App\Mail\EmailVerification;
use Illuminate\Http\Request;
use App\User;
use App\BloodRequest;
use App\Campaign;
use App\BloodRequestDetail;
use App\Post;
use App\Notifications\BloodRequestNotification;
use Carbon\Carbon;
use App\Notifications\CampaignNotification;
use App\BloodType;
use App\BloodCategory;
use App\BloodInventory;
// use Borla\Chikka\Chikka;
use App\DonateRequest;
use App\Notifications\GeneralNotification;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test', function() {

// $success = Storage::allDirectories();
// dd($success);
    // $campaign = Campaign::find('BC2A0D7');
    // $id = '04300BD';
    // $campaign->load(['attendance' => function($query) use ($id){
    //         $query->where('user_id', $id)->first();
    //     }]);
    // $campaign->attendance->first()->update([
    //     'remarks' => 'Attended'
    // ]);

    // $id = $campaign->id;
    // $attendance = $campaign->attendance;
    // $attendanceIds = array();
    // foreach($attendance as $attendance)
    // {
    //         $attendanceIds[] = $attendance->user_id;
    // }
    // $data = json_encode([
    //     'campaignId' => $id,
    //     'attendanceIds' => $attendanceIds]); 
    // echo "<img src=\"data:image/png;base64,". base64_encode(QrCode::format('png')->size(100)->generate($data))  ."\">";

    // dd(Carbon::now()->toDateTimeStrinwg());
    // dd(Carbon::now()->format('h:i'));
    dd(User::first()->ageEligibility());
    $campaign = Campaign::first();
            $testCampaign = [
                'class' => 'App\Campaign',
                'id' => $campaign->id,
                'time' => Carbon::now()->toDateTimeString()
            ];
            $userSent = [
                'name' => $campaign->initiated->name(),
                'picture' => $campaign->initiated->picture()
            ];
            $message = $campaign->name." is happening today at ".$campaign->date_start->format('h:i A').". See you there!";
    $user = User::find('4101D4B');
    $resp = $user->notify(new CampaignNotification(
        $testCampaign,$userSent,$message,'BloodPlusNotification'));
    dd($resp);


    // $ongoingCampaigns = Campaign::where('status','Ongoing')->whereDate('date_end','<=',Carbon::yesterday())->update([
    //     'status' => 'Done',
    //     'updated_at' => Carbon::now()->toDateTimeString()
    //     ]);

    // $ongoingCampaigns = Campaign::where('status','Ongoing')->whereDate('date_end','<=',Carbon::yesterday())->get();
    //     // Campaign::where('status','Ongoing')->whereDate('date_end','<=',Carbon::yesterday())->update([
    //     // 'status' => 'Done',
    //     // 'updated_at' => Carbon::now()->toDateTimeString()
    //     // ]);

    //     foreach($ongoingCampaigns as $campaign)
    //     {
    //         $testCampaign = [
    //             'class' => 'App\Campaign',
    //             'id' => $campaign->id,
    //             'time' => Carbon::now()->toDateTimeString()
    //         ];
    //         $user = [
    //             'name' => $campaign->initiated->name(),
    //             'picture' => $campaign->initiated->picture()
    //         ];
    //         $message = "Our campaign is officialy done!";
    //         $attendance = $campaign->attendance;
    //         foreach($attendance as $attendee)
    //         {
    //             // dd($attendee);
    //             $attendee->user->notify(new CampaignNotificataion($testCampaign,$user,$message));
    //         }
    //         $institute = $campaign->initiated->institute;
    //         $admins = $institute->admins;
    //         foreach($admins as $admin)
    //         {
    //             $admin->notify(new CampaignNotification($testCampaign,$user,$message));
    //         }
    //     }

        // $campaign = Campaign::find('23DAD09');
        // $followers = $campaign->initiated->institute->followers;
        // $attending = $campaign->attendanceUserModel;
        // $leftBubble = $followers->diff($attending);
        // $intersection = $followers->intersect($attending);

        // foreach($leftBubble as $attendee)
        // {
        //     // $user = [
        //     // "name" => $attendee->user->name(),
        //     // "picture" => $attendee->user->picture()
        //     // ];
        //     echo "<pre>";
        //     print_r($attendee->id);
        //     echo "</pre>";
        // }
        // foreach($intersection as $potentialAttendee)
        // {
        //     echo "<pre>";
        //     print_r($potentialAttendee->name());
        //     echo "</pre>";
        // }
        // dd($intersection);

    // $config = [
    // 'shortcode'=> '29290 7547',
    // 'client_id'=> '0bcb983964a12761e452349222f8f8182c68ba464c55566acd5287965daa1976',
    // 'secret_key'=> 'dfa0555634d4525d05bd72b08ab58fce42cb9b01bb6a3b0456e6b3733df45558',
    // ];

    // // Create Chikka object
    // // $chikka = new Chikka($config);
    // $mobile = '09254649699';

    // // Send SMS
    // dd($response);
});
Route::get('/abcdefg',function (){
    // dd(Auth::user()->super);
    // dd(asset("assets/img/321.png"));

    // dd(Auth::guard('web_admin')->user()->institute->settings['bloodtype_available']);
    // $settings = [
    //     'patient-directed' => 'false',
    //     'bloodbags' => 
    //         [
    //     'Karmi' => [ '450s','450d','450t','450q'],
    //     'Terumo' => [ '450s','450d','450t']
    //         ],
    //     'bloodtype_available' => 
    //         [
    //         'Whole Blood','Packed RBC','Platelets','Fresh Frozen Plasma','Cryoprecipitate'
    //         ]
    //     ];
    //     //'Whole Blood','Packed RBC','Washed RBC','Platelets','Fresh Frozen Plasma','Cryoprecipitate';
    // echo "<pre>";
    // echo json_encode($settings);
    // echo "</pre>";
    // Campaign::where('status','Pending')->whereDate('date_start',Carbon::today())->update([
    //     'status' => 'Ongoing']);

    // Campaign::where('status','Ongoing')->whereDate('date_end',Carbon::yesterday())->update([
    //     'status' => 'Done']);
    // dd(Auth::user()->with(['followedInstitutions' => function ($query) {
        // $query->where('created_at')
    // }]));
    // $collection = BloodType::find('5B782AA')->nonReactive();
    // dd($collection);
    // dd(Auth::guard('web_admin')->user());
    
    
    // $sameBloodTypeUsers = User::with(['donations' => function ($query) {
    //     //latest niyang donation that is not cancelled
    //         $query->where('status','!=','Cancelled')->orderBy('created_at','desc')->first();
    //     }])->whereHas('followedInstitutions', function($query) {
    //         $query->where('id',Auth::guard('web_admin')->user()->institution_id);
    //     })->where('bloodType','B+')->get();

    // dd($sameBloodTypeUsers);
    // $exitCode = Artisan::call('queue:work');
    // $bloodType = BloodType::first();
    // dd($bloodType->bloodCategory);
    // dd(Carbon::now()->timezone('Asia/Manila')->toDateTimeString());
    // $optionBuilder = new OptionsBuilder();
    // $optionBuilder->setTimeToLive(60*20);
    // $notificationBuilder = new PayloadNotificationBuilder('my title');
    // $notificationBuilder->setBody('Hello world')
    //                     ->setSound('default');
    // $dataBuilder = new PayloadDataBuilder();
    // $dataBuilder->addData(['a_data' => 'my_data']);

    // $option = $optionBuilder->build();
    // $notification = $notificationBuilder->build();
    // $data = $dataBuilder->build();
    // $token = "123456789";

    // $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
    // dd($downstreamResponse);
    // return response()->json($downstreamResponse);
    // $downstreamResponse->numberSuccess();
    // $downstreamResponse->numberFailure();
    // $downstreamResponse->numberModification();

    // //return Array - you must remove all this tokens in your database
    // $downstreamResponse->tokensToDelete(); 

    // //return Array (key : oldToken, value : new token - you must change the token in your database )
    // $downstreamResponse->tokensToModify(); 

    // //return Array - you should try to resend the message to the tokens in the array
    // $downstreamResponse->tokensToRetry();

    // $exitCode = Artisan::call('db:seed');
    // $exitCode = Artisan::call('config:cache');
    // $exitCode = Artisan::call('queue:work');
    // $exitCode = Artisan::call('migrate');
    // $exitCode = Artisan::call('storage:link');
    // $exitCode = Artisan::call('initiateMedicalForm');
    // $exitCode = Artisan::call('campaignToOngoing');
    // $exitCode = Artisan::call('campaignToDone');

    // $donateRequestTime = DonateRequest::find('64A7B2B')->appointment_time;
    // dd($donateRequestTime->diffInHours(Carbon::now(),false));
});
Route::get('command/campaignToDone', function() {
    $exitCode = Artisan::call('campaignToDone');
});
Route::get('command/campaignToOngoing', function() {
    $exitCode = Artisan::call('campaignToOngoing');
});
Route::get('command/flushBlood', function() {
    $exitCode = Artisan::call('flushBlood');
});
Route::get('command/initiateMedicalForm', function() {
    $exitCode = Artisan::call('initiateMedicalForm');
});
Route::get('/', function () {
    //return all blooddrequest na ongoing. orderby priority paginate 5
    $requests = BloodRequest::where('status','Ongoing')->get();
    $counter = 0;

    return view('landing.index',compact('requests','counter'));
});

Route::get('/whoweare', function () {

    return view('landing.whoweare');
});

Route::get('/whatwedo', function () {
    return view('landing.whatwedo');
});

Route::get('/contact', function () {
    return view('landing.contact');
});
Route::post('/sendinquiry', 'UserController@sendInquiry');

Auth::routes();

Route::get('/inventory', 'UserController@inventory');
Route::get('/verifyaccountscreen', function() {
    if(Auth::user()->verified == 1)
        return redirect('home');
    else
    return view('auth.verifyaccount');
});
Route::get('/unauthorize', function() {
    return view('auth.unauthorize');
});
Route::post('/resendtoken', function() {
        $user = Auth::user();
        $email = new EmailVerification($user);
        Mail::to($user->email)->send($email);
        return Redirect::back()->with('status','Successfully resend verification link.');
    });
Route::get('notifications','BloodPlusController@getNotifications');
Route::get('notifications/unread','BloodPlusController@unreadNotifications');
Route::get('/notifications/{notification}','NotificationController@getNotification');


Route::group(['middleware' => ['auth']], function() {
    Route::group(['middleware' => 'verified'], function() {
    // Route::get('notifications','BloodPlusController@getNotifications');


    Route::post('/request', 'BloodPlusController@makeBloodRequest');
    Route::post('/request/{request}/donate','BloodPlusController@donateToBloodRequest');
    Route::get('/request/{request}','BloodPlusController@getRequest');
    Route::get('/home', 'BloodPlusController@index');
    Route::get('/request', 'BloodPlusController@request');
    Route::post('/request', 'BloodPlusController@makeBloodRequest');
    Route::get('/profile', 'BloodPlusController@showProfile');
    Route::get('/donate', 'UserDonateController@donate'); 
    Route::get('/donate/{donate}', 'UserDonateController@getDonateRequest'); 
    Route::post('/donate', 'UserDonateController@makeDonationRequest');
    Route::get('/campaign/{campaign}', 'BloodPlusController@getCampaign');
    Route::post('/request/{request}/cancel','BloodPlusController@cancelBloodRequest');
    Route::post('/donate/{request}/cancel','UserDonateController@cancelDonateRequest');
    Route::post('/user/{user}/changebanner','BloodPlusController@changeBanner');
    Route::post('/user/{user}/changepp','BloodPlusController@changePicture');
    
    //search users like name%
    Route::get('/user/searchajax','SocialController@searchUsersAjax');
    Route::get('/user/search','SocialController@searchUsers');


    //get user na iya gi clickan(view profile) 
    Route::get('/user/search/{user}','SocialController@getUser');
    
    Route::get('/user/{user}','SocialController@getUser');

    Route::post('/user/{user}/follow','SocialController@followUser');
    Route::post('/user/{user}/unfollow','SocialController@unFollowUser');
    Route::post('/post/{post}/react','SocialController@react');
    Route::post('/post/{post}/unreact','SocialController@unReact');



    });
});

Route::get('/upload', function() {
    return view('upload');
}); 
Route::post('/upload', function(Request $request) {
    // dd($request);
    $name = $request->file('avatar')->getClientOriginalName();
    $path = $request->file('avatar')->storeAs('avatars/profile',$name);
    $contents = Storage::url('avatars/'.$name);
    dd(asset(''));
    return view('/showupload',compact('path'));
});

Route::get('/verifyemail/{token}', 'BloodPlusController@verify');

Route::prefix('admin')->group(function () {
    Route::get('/waitingscreen', function() {
        return view('admin.waitingscreen');
    });
	Route::group(['middleware' => 'admin_guest'], function() {
    Route::get('/register', 'AdminAuth\RegisterController@show');
    Route::post('/register', 'AdminAuth\RegisterController@register');
    Route::get('/reset','AdminAuth\ForgotPasswordController@showLinkRequestForm');
    Route::post('/sendreset','AdminAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::get('/reset/{token}','AdminAuth\ResetPasswordController@showResetForm');
    Route::post('/reset','AdminAuth\ResetPasswordController@reset');
    Route::get('/login', 'AdminAuth\LoginController@show');
    Route::post('/login', 'AdminAuth\LoginController@login');

    //todos
    Route::get('/password/reset','AdminAuth\ForgotPasswordController@showLinkRequestForm');
    Route::post('/password/email',function(Request $request) {
        dd($request->input());
    });
    Route::get('/password/reset/{token}','AdminAuth\ResetPasswordController@showResetForm');
    Route::post('/password/reset','AdminAuth\ResetPasswordController@reset');
	});
	
    Route::group(['middleware' => 'admin_auth'], function() {
        Route::post('/logout', 'AdminAuth\LoginController@logout');
    Route::group(['middleware' => 'accepted'], function() {

    Route::get('/settings','AdminController@settings');
    Route::get('/', 'AdminController@index');
    Route::get('/request','AdminController@request');
    Route::get('/donors','AdminController@donors');
    Route::get('/pendingrequests','AdminController@pendingRequests');
    Route::post('/request/view','AdminController@viewRequest');
    //send textblast to tanan available(meaning walay blood);
    Route::post('/request/accept','AdminController@updateToActive');
    
    Route::post('/request/delete','AdminController@deleteRequest');
    Route::post('/request/claim','AdminController@claimRequest');
    //reply to user for update dayon;
    Route::post('/request/reply','AdminController@sendMessage');
    Route::get('/request/{bloodRequest}/accept','AdminController@showCompleteRequest');
    Route::get('/request/{bloodRequest}/complete','AdminController@showCompleteRequest');
    Route::post('/request/{bloodRequest}/complete','AdminController@updateToDone');

    // Route::get('/sendTextBlast','AdminController@sendTextBlast');
    Route::post('/donors/notify','AdminController@notifyViaText');
    Route::get('/donate','AdminDonateController@donate');
    Route::post('/donate/accept','AdminDonateController@acceptRequest');
    Route::post('/donate/delete','AdminDonateController@declineRequest');
    Route::post('/donate/settime','AdminDonateController@setAppointment');  
    Route::get('/donate/{donate}/complete','AdminDonateController@completeDonateRequestView');
    Route::get('/donate/{donate}/medical_history/retrieve','AdminDonateController@retrieveMedicalHistory');
    Route::post('/donate/{donate}/{medicalHistory}/remark','AdminDonateController@remarkOnMedicalHistory');
    Route::post('/donate/{donate}/complete','AdminDonateController@completeDonateRequest');
    Route::get('/donate/{donate}/view','AdminDonateController@getDonationRequest');
    Route::get('/donate/{donate}/view/pdf','AdminDonateController@showPdf');
    Route::post('/donate/{donate}/view/pdf','AdminDonateController@downloadPdf');
    // Route::get('/donate/{donate}/accept','AdminDonateController@acceptDonationRequestView');
    // Route::post('/donate/{donate}/accept','AdminDonateController@acceptRequest');
     Route::get('/campaign','AdminCampaignController@campaign');
    Route::post('/campaign/create','AdminCampaignController@createCampaign');
    Route::get('/campaign/create','AdminCampaignController@showCreate');
    Route::get('/campaign/{campaign}','AdminCampaignController@viewCampaign');

    Route::post('/request/{request}/bloodbag','AdminInventoryController@getBloodBagStatus');
    Route::get('/inventory','AdminInventoryController@index');

    Route::get('/bloodbags','AdminInventoryController@showBloodbags');

    Route::post('/bloodbags/components','AdminInventoryController@getBloodBagComponents');

    Route::get('/bloodbags/{bloodbag}/screen','AdminInventoryController@showSingleStatustoStagedView');
    Route::post('/bloodbags/{bloodbag}/screen','AdminInventoryController@setSingleStatusToStaged');

    Route::get('/bloodbags/screen','AdminInventoryController@showStatustoStagedView');
    Route::post('/bloodbags/screen','AdminInventoryController@setStatusToStaged');

    Route::get('/bloodbags/stagedbloodbag','AdminInventoryController@showStagedBloodbags');
    //view sa staged single blood bag nga pending gipili
    Route::get('/bloodbags/{staged}/stage','AdminInventoryController@showSingleCompleteScreenedBlood');
    Route::post('/bloodbags/{staged}/stage','AdminInventoryController@completeSingleScreenedBlood');

    Route::get('/bloodbags/stage','AdminInventoryController@showCompleteScreenedBlood');
    Route::post('/bloodbags/stage','AdminInventoryController@completeScreenedBlood');

    Route::get('/inventory/bloodtype/{bloodType}','AdminInventoryController@showBloodType');
    Route::get('/inventory/bloodcategory/{bloodCategory}','AdminInventoryController@showBloodCategory');
    });
    });
});


Route::group(['prefix' => 'bpadmin', 'middleware' => 'bpadmin'], function () 
{
    Route::get('/', 'Super\SuperAdminController@index');
    Route::get('/institutions','Super\InstitutionsController@getInstitutions');

    Route::post('/institution/accept','Super\InstitutionsController@acceptInstitution');
    Route::post('/institution/delete','Super\InstitutionsController@declineInstitution');
    Route::get('/institution/{institution}','Super\InstitutionsController@getInstitution');
    //connected bloodbanks/status etc
    // Route::get('/','Super\SuperAdminController@index');
    //add bloodbank (with their credentials and then add 1 account)
       // Route::post('/bbank/create','Super\SuperAdminController@addBloodBank');
       // Route::post('/bbank/admin/create','Super\SuperAdminController@addInstitutionAdmin');

    //add another admin
        // Route::post('/sadmin/create','Super\SuperAdminController@addSuperAdmin');
});
