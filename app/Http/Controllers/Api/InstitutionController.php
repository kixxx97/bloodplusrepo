<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institution;

class InstitutionController extends Controller
{
    

    protected function getInstitutions()
    {
        $institutions = Institution::where('status','active')->get();
        $message = array('institutions' => $institutions,'message' => '200','status' => 'Successful retrieval of institutions');
        return response()->json($message);
    }	

    
}
