<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BloodRequest;
use App\DonateRequest;
use App\Follower;
use App\Institution;
use Auth;
use \Carbon\Carbon;
use App\Campaign;
use App\Log;
use App\User;
use App\Post;	
use App\Notifications\BloodRequestNotification;


class SocialController extends Controller
{
    
    public function profile()
    {
    	$posts = Auth::user()->posts;
    	// $posts = Post
        $donateCount = count(DonateRequest::where('status','Done')->where('initiated_by',Auth::user()->id)->get());
        $requestCount = count(BloodRequest::where('status','Done')->where('initiated_by',Auth::user()->id)->get());
    	$followers = Auth::user()->followers;
    	$tmpFollowing = Auth::user()->followedUsers->merge(Auth::user()->followedInstitutions);
        $following = null;
        $countFollowing = count($tmpFollowing);
        $countFollowers = 0;
		if(count($posts) == 0)
			$posts = null;
		if(count($followers) == 0)
        {
			$mutuals = null;
            $notMutuals = null;
        }
        else
        {
        $tmpMutuals = $followers->intersect(Auth::user()->followedUsers);
        $tmpNotMutuals = $followers->diff($tmpMutuals);
        $mutuals = null;
        $notMutuals = null;
            if(count($tmpMutuals) != 0)
            {
                $countFollowers = count($tmpMutuals);

            } 
            if(count($tmpNotMutuals) != 0)
            {
                $countFollowers = $countFollowers + count($tmpNotMutuals);
            }
        }
        $lastRequest = DonateRequest::where('status','Done')->where('initiated_by',Auth::user()->id)->orderBy('appointment_time','desc')->first();

        if($lastRequest == null)
        {
            $lastRequest = null;
            $nextDateDonation = 'Yes';
            $lastDayDonated = null;
        }
        else
        {
            $date = $lastRequest->appointment_time;
            $nextDateDonation = $date->addDays(90)->toDateTimeString();
            $lastDayDonated = $lastRequest->appointment_time->toDateTimeString();
        }
    	return response()->json([
    		'posts' => $posts,
    		'following' => $countFollowing,
    		'followers' => $countFollowers,
    		'status' => 'Successful',
            'bloodRequestCount' => $requestCount,
            'bloodDonateCount' => $donateCount,
            'lastDayDonated' => $lastDayDonated,
            'nextDayDonation' => $nextDateDonation,
    		'message' => 'Retrieved posts and followers']);
    }
    
    public function editProfile(Request $request) {
        //

    }

    public function followUser(Request $request, $user)
    {
    	$followers = Auth::user()->followers;
        try{
        // dd($user);
            $tmpUser = User::findOrFail($user);
            $class = 'User';
        }catch(\Exception $e)
        {
            try{
            $tmpUser = Institution::findOrFail($user);
            $class = 'Institution';
            }
            catch(\Exception $e)
            {
                return response()->json([
            'status' => 'Error Error',
            'message' => 'User does not exist']);
            }
        }
        if($class == 'User')
        {
            if($tmpUser->id == Auth::user()->id)
        {
            return response()->json(['status' => 'Error Error',
                'message' => 'Cannot follow yourself']);
        }
        // dd(count($followers));
        if(!Auth::user()->followedUsers->contains($tmpUser))
        {
            Auth::user()->followedUsers()->attach($tmpUser->id);
            Log::create([
                'initiated_id' => Auth::user()->id,
                'initiated_type' => 'App\User',
                'reference_type' => 'App\User',
                'reference_id' => $tmpUser->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You have successfully followed .'.$tmpUser->name()
                ]);
            $class = array("class" => "App\User",
                "id" => $tmpUser->id,
                "time" => Carbon::now()->toDateTimeString());
            $usersent = array(
                "name" => Auth::user()->name(),
                "picture" => Auth::user()->picture());

            $tmpUser->notify(new BloodRequestNotification($class,$usersent,'You have been followed by'.$tmpUser->name()));

            return response()->json(['status' => 'Successful', 'message' => 'Successfully followed the user']);
        }
        else
            return response()->json(['status' => 'Error', 'message' => 'Couldn\'t follow the user']);
        }
        if($class == 'Institution')
        {
            if($tmpUser->id == Auth::user()->id)
        {
            return response()->json(['status' => 'Error Error',
                'message' => 'Cannot follow yourself']);
        }
        // dd(count($followers));
        if(!Auth::user()->followedUsers->contains($tmpUser))
        {
            Auth::user()->followedInstitutions()->attach($tmpUser->id);
            Log::create([
                'initiated_id' => Auth::user()->id,
                'initiated_type' => 'App\User',
                'reference_type' => 'App\Institution',
                'reference_id' => $tmpUser->id,
                'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
                'message' => 'You have successfully followed .'.$tmpUser->name()
                ]);
            $class = array("class" => "App\User",
                "id" => $tmpUser->id,
                "time" => Carbon::now()->toDateTimeString());
            $usersent = array(
                "name" => Auth::user()->name(),
                "picture" => Auth::user()->picture());
            foreach($tmpUser->admins as $admin)
            {
                $admin->notify(new BloodRequestNotification($class,$usersent,'You have been followed by.'.$tmpUser->name()));
            }

            return response()->json(['status' => 'Successful', 'message' => 'Successfully followed the institution']);
        }
        else
            return response()->json(['status' => 'Error', 'message' => 'Couldn\'t follow the user']);
        }

    }

    public function getFollowers()
    {
        $followers = Auth::user()->followers;
        if(count($followers) == 0)
        {
            $mutuals = null;
            $notMutuals = null;
        }
        else
        {
        $tmpMutuals = $followers->intersect(Auth::user()->followedUsers);
        $tmpNotMutuals = $followers->diff($tmpMutuals);
        $notMutuals = null;
        $countFollowers = null;
        $mutuals = array();
            if(count($tmpMutuals) != 0)
            {
                $count = 0;
                foreach($tmpMutuals as $mutual)
                {
                    $mutuals[$count]['id'] = $mutual->id;
                    $mutuals[$count]['name'] = $mutual->name();
                    $mutuals[$count]['picture'] = $mutual->picture();
                    $mutuals[$count]['banner'] = $mutual->banner();
                    $count++;
                }
            } 
            if(count($tmpNotMutuals) != 0)
            {
                foreach($tmpNotMutuals as $notMutual)   
                {
                    $mutuals[$count]['id'] = $notMutual->id;
                    $mutuals[$count]['name'] = $notMutual->name();
                    $mutuals[$count]['picture'] = $notMutual->picture();
                    $mutuals[$count]['banner'] = $notMutual->banner();
                    $count++;
                }
            }
        }
        return response()->json([
            'followers' => array(
                'mutuals' => $mutuals,
                'notMutuals' => $notMutuals),
            'status' => 'Successful',
            'message' => 'Successfully retrieved user\'s followers'
            ]);
    }

    public function getFollowing()
    {
        $tmpFollowing = Auth::user()->followedUsers->merge(Auth::user()->followedInstitutions);
        $following = null;
        if(count($tmpFollowing) != 0)
        {
            $following = array();
            $count = 0;
            foreach($tmpFollowing as $followings)
            {
                $following[$count]['id'] = $followings->id;
                $following[$count]['name'] = $followings->name();
                $following[$count]['picture'] = $followings->picture();
                $following[$count]['banner'] = $followings->banner();
                $count++;
            }
        }
        return response()->json([
            'following' => $following,
            'status' => 'Successful',
            'message' => 'Succesffully retrieved user\'s followings'
            ]);

    }
    public function unFollowUser($user)
    {
        try{
        // dd($user);
            $tmpUser = User::findOrFail($user);
            $class = 'User';
        }catch(\Exception $e)
        {
            try{
            $tmpUser = Institution::findOrFail($user);
            $class = 'Institution';
            }
            catch(\Exception $e)
            {
                return response()->json([
            'status' => 'Error Error',
            'message' => 'User does not exist']);
            }
        }
    	
        if($tmpUser->id == Auth::user()->id)
        {
            return response()->json(['status' => 'Error Error',
                'message' => 'Cannot unfollow yourself']);
        }
        $bool = $tmpUser->followers()->detach(Auth::user()->id);
    	if($bool)
    	{
    		return response()->json(['status' => 'Successful', 'message' => 'Successfully unfollowed the user']);
    	}
    	else
    		return response()->json(['status' => 'Error', 'message' => 'Couldn\'t unfollow the user']);
    }
    public function getAllUsers()
    {
        $users = User::where('id','!=',Auth::user()->id)->get();
        if($users)
        {
            return response()->json([
                'users' => $users,
                'status' => 'Successful',
                'message' => 'Successfully retrieved all users']);
        }
        else
        {
            return response()->json([
                'users' => $users,
                'status' => 'Error',
                'message' => 'Error retrieving users']);
        }
    }

    public function getUser($user)
    {
        // dd($user);
        try{
        // dd($user);
            $tmpModel = User::findOrFail($user);
            $class = 'User';
        }catch(\Exception $e)
        {
            try{
            $tmpModel = Institution::findOrFail($user);
            $class = 'Institution';
            }
            catch(\Exception $e)
            {
                return response()->json([
            'status' => 'Error Error',
            'message' => 'User does not exist']);
            }
        }
        // dd($tmpModel);

        $picture =  $tmpModel->picture();
        $banner = $tmpModel->banner();

        if($class == 'User')
        {
        $donateCount = count(DonateRequest::where('status','Done')->where('initiated_by',$tmpModel->id)->get());
        $requestCount = count(BloodRequest::where('status','Done')->where('initiated_by',$tmpModel->id)->get());
        $lastRequest = DonateRequest::where('status','Done')->where('initiated_by',$tmpModel->id)->orderBy('appointment_time','desc')->first();

        if($lastRequest == null)
        {
            $lastRequest = null;
            $nextDateDonation = 'Yes';
            $lastDayDonated = null;
        }
        else
        {
            $date = $lastRequest->appointment_time;
            $nextDateDonation = $date->addDays(90)->toDateTimeString();
            $lastDayDonated = $lastRequest->appointment_time->toDateTimeString();
        }

        $following = Auth::user()->followedUsers->merge(Auth::user()->followedInstitutions);
        if($following->contains('id',$tmpModel->id))
        {
            $followed =true;
        }
        else
        {
            $followed =false;
        }
        $model['id'] = $tmpModel->id;
        $model['email'] = $tmpModel->email;
        $model['picture'] = $picture;
        $model['banner'] = $banner;
        $model['name'] = $tmpModel->name();
        $model['gender'] = $tmpModel->gender;
        $model['dob'] = $tmpModel->dob;
        $model['bloodType'] = $tmpModel->bloodType;
        $model['address'] = $tmpModel->address['place'];
        $model['donateCount'] = $donateCount;
        $model['requestCount'] = $requestCount;
        $model['lastDayDonated'] = $lastDayDonated;
        $model['nextDateDonation'] = $nextDateDonation;
        $model['followed'] = $followed;
        $model['followers'] = count($tmpModel->followers);
        $model['following'] = count($tmpModel->followedUsers) + count($tmpModel->followedInstitutions);
        }
        elseif($class ='Institution')
        {
        $model['id'] = $tmpModel->id;
        $model['email'] = $tmpModel->email;
        $model['contact'] = $tmpModel->contact;
        $model['name'] = $tmpModel->name();
        $model['picture'] = $picture;
        $model['banner'] = $banner;
        $model['address'] =  $tmpModel->address['place'];
        $model['about_us'] = $tmpModel->about_us;
        $model['facebook'] = $tmpModel->links['facebook'];
        $model['twitter'] = $tmpModel->links['twitter'];
        $model['website'] = $tmpModel->links['website'];
        }
        return response()->json(['user' => $model,
            'class' => $class,
            'status' => 'Succcessful',
            'message' => 'Successfully retrieved user']);
    }
    public function getUsers(Request $request)
    {
        $wildcard = $request->input('name');
        // dd($wildcard)
        $tmpUsers = User::where('fname', 'like', $wildcard."%")->orWhere('lname','like', $wildcard."%")->get();
        $tmpUsers = $tmpUsers->merge(Institution::where('institution','like',$wildcard."%")->get());
        $tmpUsers = $tmpUsers->merge (Campaign::where('name','like',$wildcard."%")->get());
        $users = null;
        if(count($tmpUsers) != 0)
        {  
            $users = array();
            $count = 0;
            foreach($tmpUsers as $tmpUser)
            {
                $users[$count]['name'] = $tmpUser->name();
                $users[$count]['id'] = $tmpUser->id;
                $tmpModel = substr(get_class($tmpUser),4);
                $users[$count]['type'] = $tmpModel;
                $count++;
            }
        }
        return response()->json(['users' => $users]);
    }
    // public function searchUsers(Request $request)
    // {

    // }

    public function react(Post $post) {
        // $posts->first()->likes()->attach('170E0F5',['initiated_by' => Auth::user()->id]);
        $bool = $post->likes->contains(function ($value, $key) {
            return $value->pivot->initiated_by == Auth::user()->id;
        });
        if($bool)
        {
            return response()->json([
                'status' => 'Error',
                'message' => 'Couldn\'t like the post']);
        }
        else
        {
            $bool = $post->likes()->attach('170E0F5',['initiated_by' => Auth::user()->id]);
            return response()->json([
                'status' => 'Successful',
                'message' => 'Successfully liked the post']);
        }
    }
    public function getUserFollowers(User $user)
    {
        $tmpFollowers = $user->followers; 
        if(count($tmpFollowers) == 0)
        {
            return response()->json(null);
        }
        $count = 0;
        $followers = array();
        foreach($tmpFollowers as $follower)
        {
            $followers[$count]['id'] = $follower->id;
            $followers[$count]['picture'] = $follower->picture();
            $followers[$count]['name'] = $follower->name();
            $count++;
        }
        return response()->json($followers);
    }
    public function getUserFollowings(User $user)
    {
        $tmpFollowingInstitutions = $user->followedInstitutions;
        $tmpFollowingUsers = $user->followedUsers;
        if($tmpFollowingUsers == null && $tmpFollowingInstitutions == null)
            return response()->json(null);
        if(count($tmpFollowingInstitutions) == 0)
        {
            $count = 0;
            $followingUsers = array();
            foreach($tmpFollowingUsers as $user)
            {
                $followingUsers[$count]['id'] = $user->id;
                $followingUsers[$count]['picture'] = $user->picture();
                $followingUsers[$count]['name'] = $institution->name();
                $count++;
            }
            return response()->json($followingUsers);
        }
        if(count($tmpFollowingUsers) == 0)
        {
            $followingInstitutions = array();
            $count = 0;
            foreach($tmpFollowingInstitutions as $institution)
            {
                $followingInstitutions[$count]['id'] = $institution->id;
                $followingInstitutions[$count]['picture'] = $institution->picture();
                $followingInstitutions[$count]['name'] = $institution->name();
                $count++;
            }
            return response()->json($followingInstitutions);
        }
        $tmpTotalFollowings = $tmpFollowingInstitutions->merge($tmpFollowingUsers);
        $totalFollowings = array();
        $count = 0;
        foreach($tmpTotalFollowings as $followings)
        {
            $totalFollowings[$count]['id'] = $followings->id;
            $totalFollowings[$count]['picture'] = $followings->picture();
            $totalFollowings[$count]['name'] = $followings->name();
            $count++;
        }
        return response()->json($totalFollowings);
    }
    public function unReact(Post $post) {
        $bool = $post->likes->contains(function ($value, $key) {
            return $value->pivot->initiated_by == Auth::user()->id;
        });
        if($bool)
        {
            // $smth = $post->load(['likes' => function($query ) {
            //     $query->where('initiated_by',Auth::user()->id)->first();
            // }]);
            // dd($smth->likes->first()->pivot->initiated_by);
            $bool = $post->specificLike()->detach(Auth::user()->id);
            // $bool = $post->likes()->detach('170E0F5',['initiated_by' => Auth::user()->id]);
            return response()->json([
                'status' => 'Successful',
                'message' => 'Successfully unliked the post']);
        }
        else
            return response()->json([
                'status' => 'Error',
                'message' => 'Couldn\'t unlike the post']);
    }


}
