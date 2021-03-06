<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Log;
use App\User;
use App\Institution;
use App\Unsubscribe;
use Carbon\Carbon;
use App\Follower;
use App\BloodRequest;
use App\BloodRequestDetail;

class AuthenticationController extends Controller
{
    //login

    public function register(Request $request)
    {       
        $log = json_encode($request->input());

        $validator = $this->validator($request->all());
    	if($validator->fails()) {
            $message = $validator->messages();
            return response()->json($message);
        }
        // $user='abcdefg';
        $user = $this->create($request->all());
        if($user->gender == 'Male;')
        {
            $user->update([
                'picture' => asset('storage/avatars/profile/man.png')
                ]);
        }
        else
        {
            $user->update([
                'picture' => asset('storage/avatars/profile/woman.png')
                ]);
        }   

    	// Auth::guard('web')->attempt($user);
        if($user)
        {
        Log::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'message' => 'You just joined BloodPlus',
            'reference_type' => 'App\User',
            'reference_id' => $user->id,
            'initiated_type' => 'App\User',
            'initiated_id' => $user->id
        ]);
        //follow red cross.
        $user->followedInstitutions()->attach('51B64E1');

    	$message = array('user' => $user, 'status' => 200, 'message' => 'Successfully Registered');
        }   
        else
        $message = array('status' => 200, 'message' => 'Invalid Error');

        return response()->json($message);
    }
    public function login(Request $request)
    {
        $log = json_encode($request->input());
    	$email = $request->input('email');
    	$password = $request->input('password');
    	if(Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            
            $message = array('user' => Auth::user(), 
                'status' => 200, 
                'message' => 'Successful Login');

            return response()->json($message);
        }
        else
        	return response()->json(['error'=>'Invalid Credentials']);
    }
    //campaigns
    protected function validator(array $data)
    {
    	return Validator::make($data, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'dob' => 'required|date',
            'bloodType' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'contact' => 'nullable|numeric',
            'password' => 'required|string|min:6'
        ]);
    }
    protected function create(array $data)
   	{
   		$address = 
            array('place' => ucwords($data['exactcity']),
                'longitude' => $data['cityLng'], 
                'latitude' => $data['cityLat']);
        // dd($address);

         return User::create([ 
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'fname' => ucwords($data['fname']),
            'lname' => ucwords($data['lname']),
            'mi' => ucwords($data['mi']),
            'gender' => $data['gender'],
            'bloodType' => $data['bloodType'],
            'email' => $data['email'],
            'contactinfo' => $data['contact'],
            'dob' => new Carbon($data['dob']),
            'status' => 'active',
            'password' => bcrypt($data['password']),
            'api_token' => base64_encode($data['email'].'kixgwapo'),
            'email_token' => base64_encode($data['email']),
            'address' => $address,
            'verified' => 1,
        ]);
   	}
    public function refreshToken(Request $request)
    {
        Auth::user()->update([
            'device_token' => $request->input('device_token')
        ]);
        return response()->json(['device_id' => Auth::user()->device_token]);
    }


    public function refreshLocation(Request $request)
    {
        $location = Auth::user()->location;
        $location['longitude'] = $request->input('longitude');
        $location['latitude'] = $request->input('latitude'); 
        Auth::user()->update([
            'location' => $location]);
    }
    public function getUnreadNotifications()
    {
        $count = count(Auth::user()->unReadNotifications);
        return response()->json(array(
            'unreadNotif' => $count,
            'status' => '200',
            'message' => 'Successfully retrieved unread notifications'));
    }

    public function unsubscribeUser(Request $request) {

        try{
        $user = Auth::user();
        $attendances = $user->attendances()->delete();
        $donations = $user->donations;
        foreach($donations as $donation)
        {
            if($donation->screenedBlood)
            $inventory = $donation->screenedBlood->components()->delete();
            if($donation->screenedBlood)
            $screenedBlood = $donation->screenedBlood()->delete();
        }
        $donations = $user->donations()->delete();
        $requests = $user->requests()->delete();;
        $followers = $user->followers()->detach($user->id);
        $followedUsers = $user->followedUsers()->detach($user->id);
        $followedInstitutions = $user->followedInstitutions($user->id)->detach();
        $posts = $user->posts()->delete();
        $user->delete();
        $unsubscribe = Unsubscribe::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'reason' => $request->input('reason')]);
        return response()->json([
            'status' => 'Successful',
            'message' => 'Succesfully deleted user records'
        ]);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            return response()->json([
            'status' => 'Error',
            'message' => 'Couldn\'t deleted user records'
            ]);
        }
        //attendance
        //donate
        //screenedblood
        //inventory
        //follows
        //bloodrequestdonor
        //requests

    }
}           
