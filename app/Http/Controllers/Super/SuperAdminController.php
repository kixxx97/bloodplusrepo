<?php

namespace App\Http\Controllers\Super;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;

class SuperAdminController extends Controller
{

	public function index()
	{
		$logs = Auth::user()->super->logs()->orderBy('created_at','desc')->paginate(15);
		return view('bpadmin.index',compact('logs'));
	}
    //institution admin accountod mode)(g
}
