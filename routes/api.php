<?php

use Illuminate\Http\Request;
use App\User;
use App\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 	
Route::get('testtt',function(Request $request) {
		
	});

Route::group(['middleware' => 'preAuth'], function () {
	Route::post('register', 'Api\AuthenticationController@register');
	Route::post('login', 'Api\AuthenticationController@login');
	Route::get('/institutions','Api\InstitutionController@getInstitutions');
	Route::get('/campaigns','Api\CampaignController@getCampaigns');
	Route::post('/institutions/match','Api\InstitutionController@getMatchingInstitutions');
	
	Route::get('/user/{user}/followers','Api\SocialController@getUserFollowers');
	Route::get('/user/{user}/followings','Api\SocialController@getUserFollowings');
	Route::get('/users','Api\SocialController@getUsers');
	Route::get('/post/{post}/comments','Api\CommentController@getComments');
	// Route::post('/user','Api\SocialController@searchUsers');
 	Route::group(['middleware' => 'auth:api'], function() {
 		Route::post('/campaign/{campaign}/remark','Api\CampaignController@remarkAttendee');
 		Route::post('/user/notification/unread','Api\AuthenticationController@getUnreadNotifications');
 		Route::post('/user/refreshtoken','Api\AuthenticationController@refreshToken');
 		Route::post('/user/refreshlocation','Api\AuthenticationController@refreshLocation');
 		Route::post('/users','Api\SocialController@getAllUsers');
 		Route::get('/following/retrieve','Api\SocialController@getFollowing');
 		Route::get('/followers/retrieve','Api\SocialController@getFollowers');
 		Route::post('/history','Api\HistoryController@getHistory');
 		Route::post('profile','Api\SocialController@profile');
 		Route::post('/follow/{user}','Api\SocialController@followUser');
 		Route::post('/unfollow/{user}','Api\SocialController@unFollowUser');
 		Route::post('/request/retrieve','Api\BloodRequestController@getOngoingBloodRequest');
 		Route::post('/request/{request}/donate','Api\BloodRequestController@donateToBloodRequest');
 		Route::post('/request/{request}/retrieve','Api\BloodRequestController@getSpecificBloodrequest');
 		Route::post('/donate/create','Api\DonateController@createDonateRequest');
 		Route::post('/request/create','Api\BloodRequestController@createBloodRequest');
 		Route::post('/donate/{donateRequest}/medical_history/create','Api\DonateController@createMedicalHistory');
 		Route::get('/donate/{donateRequest}/medical_history/retrieve','Api\DonateController@retrieveMedicalHistory');
 		Route::post('/donate/retrieve','Api\DonateController@getDonateRequest');
 		Route::post('/donate/{donateRequest}/acceptAppointment','Api\DonateController@acceptAppointment');

 		Route::post('/campaign/join/{campaign}','Api\CampaignController@joinCampaign');
 		Route::post('/campaigns/retrieve','Api\CampaignController@retrieveCampaigns');
		Route::post('/campaign/{campaign}','Api\CampaignController@getSpecificCampaign');
		
		Route::post('/user/{user}','Api\SocialController@getUser');
		Route::post('/user/{user}/posts','Api\PostController@getUserPosts');
		Route::post('/posts/retrieve','Api\PostController@getAllPost');
 		Route::post('/post/{post}/react','Api\SocialController@react');
    	Route::post('/post/{post}/unreact','Api\SocialController@unReact');
    	Route::post('/post/{post}/comment/create','Api\CommentController@createComment');
    	Route::post('/notifications','Api\NotificationsController@getNotifications'); 
 		Route::post('/unsubscribe','Api\AuthenticationController@unsubscribeUser');
 	});
 	Route::get('/institution/{institution}/campaigns','Api\CampaignController@getInstitutionCampaigns');
});