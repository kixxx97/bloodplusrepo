<?php

namespace App\Http\Controllers\AdminAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Auth;
use App\InstitutionAdmin;
use App\Institution;


class RegisterController extends Controller
{
	protected $redirectPath = '/admin/';


    public function show() {
    	return view('admin.register');
    }

    public function register(Request $request) {

      // dd($request->input());
    	// $this->validator($request->all())->validate();

      // $institution = Institution::create([
      //   ''
      //   ]);
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
      $institution = Institution::create([
        'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
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
    	$admin = $this->create($request->all(),$institution);
      // dd($institution);

    	Auth::guard('web_admin')->login($admin);

      
      //fire notification to bpadmin
    	return redirect($this->redirectPath);
    }

    protected function guard()
   {
       return Auth::guard('web_admin');
   }

   protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:institution_admins',
            'password' => 'required|min:6|confirmed',
        ]);
    }


   protected function create(array $data, Institution $institution) {
    
    
   		return InstitutionAdmin::create([
          'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
   			  'institution_id' => $institution->id,
   			  'name' => $data['username'],
          'password' => bcrypt($data['password']),
          'status' => 'active'
   			]);

   }
}
