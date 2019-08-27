<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BloodInventory;
use App\BloodType;
use App\Institution;
use Auth;

class InstitutionController extends Controller
{
    

    protected function getInstitutions()
    {
        $institutions = Institution::where('status','active')->get();
        $message = array('institutions' => $institutions,'message' => '200','status' => 'Successful retrieval of institutions');
        return response()->json($message);
    }	

    public function getMatchingInstitutions(Request $request)
    {
        
        // $ids = $institutions->pluck('id');
        // $insti = Institution::whereIn('id',$ids)->get();
        // $finalInstitutions = $insti->map(function ($institution) use($institutions) {
        // 	$specificInstitution = $institutions->filter(function ($item) use ($institution) {
        // 		if($item['id'] == $institution->id)
        // 			return $item;
        // 	})->first();
        // 	$institution['count'] = $specificInstitution['count'];
        // 	return $institution;
        // })->values();
        // foreach($institutions as $institution)
        // {
        // 	$ids = 
        // }
        // $institutions = Institution::where('status','active')->get();
        return response()->json($institutions);
    } 
    public function searchUsersAjax(Request $request)
    {
    	$wildcard = $request->input('term');
        // dd($wildcard)
        //get orgs
        $tmpOrgs = Institution::where('institution','like','%'.$wildcard.'%')->where('id','!=',Auth::guard('web_admin')->user()->institute->id)->get();
        $users = array();
        $count = 0;
        foreach($tmpOrgs as $tmpOrgs)
        {
            $users[$count]['value'] = $tmpOrgs->id;
            $users[$count]['label'] = $tmpOrgs->name();
            $count++;
        }
        return response()->json($users);
    }
}
