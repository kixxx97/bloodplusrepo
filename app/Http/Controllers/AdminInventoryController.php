<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BloodBag;
use App\BloodCategory;
use App\BloodType;
use App\BloodRequest;
use App\BloodInventory;
use App\ScreenedBlood;
use Carbon\Carbon;
use \DB;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Log;

class AdminInventoryController extends Controller
{
    
    public function index(Request $request)
    {
      $institutionId = Auth::guard('web_admin')->user()->institution_id;
      if($request->input('sort') == 'true')
      {

          $validation = Validator::make($request->all(), [
              'type' => 'nullable',
              'dateTo' => 'nullable|required_with:dateFrom|after:dateFrom',
              'dateFrom' => 'nullable|required_with:dateTo|before:dateTo'
            ],[
              'dateTo.required_with' => 'Invalid range',
              'dateFrom.required_with' => 'Invalid range',
              'dateTo.after' => 'Invalid range',
              'dateFrom.before' => 'Invalid range']);

            $validation->validate();
        if($request->input('type') == 'non-reactive')
        {
          $type ='Available';
        }
        else if($request->input('type') == 'reactive')
        {
          $type='Unavailable';
        }
        else if($request->input('type') == 'expired')
        {
          $type ='Expired';
        }
        else if($request->input('type') == 'distributed')
        {
          $type ='Sold';
        }
        if($request->input('dateFrom') == null)
        {
        $bloodTypes = BloodCategory::with([
          'bloodType' => function($query) 
          {
            $query->whereIn('category',Auth::guard('web_admin')->user()->institute->settings['bloodtype_available']);
            $query->orderBy('category');
          },'bloodType.inventory' => function ($query) use ($type)
          {
            $query->where('status',$type);
          },'bloodType.inventory.screenedBlood.donation'
        ])->orderBy('name')->get();  
        }
        else
        {
        $dateFrom = Carbon::parse($request->input('dateFrom'));
        $dateTo = Carbon::parse($request->input('dateTo'))->addDays(1);
          if($type=='Available')
          {
            $bloodTypes = BloodCategory::with([
              'bloodType' => function($query) 
              {
                $query->whereIn('category',Auth::guard('web_admin')->user()->institute->settings['bloodtype_available']);
                $query->orderBy('category');
              },'bloodType.inventory' => function ($query) use ($type,$dateFrom,$dateTo)
              {
                $query->where(function($q) use($type) {
                  $q->where('status','Available')->orWhere('status','Sold')->orWhere('status','Expired');
                });
                $query->where(function($q) use($dateFrom,$dateTo) {
                  $q->orWhereBetween('updated_at', [$dateFrom,$dateTo]);
                });
              },'bloodType.inventory.screenedBlood.donation',
              'bloodType.inventory.screenedBlood' => function($query) use ($dateFrom,$dateTo)
              {
                $query->where('status', 'Done');
                $query->where(function($q) use ($dateFrom,$dateTo) {
                  $q->orWhereBetween('updated_at', [$dateFrom,$dateTo]);
                });
              },
            ])->orderBy('name')->get();
          } 
          else
          {
            $bloodTypes = BloodCategory::with([
          'bloodType' => function($query) 
          {
            $query->whereIn('category',Auth::guard('web_admin')->user()->institute->settings['bloodtype_available']);
            $query->orderBy('category');
          },'bloodType.inventory' => function ($query) use ($type,$dateFrom,$dateTo)
          {
            $query->where('status',$type);
            $query->where(function($q) use($dateFrom,$dateTo) {
              $q->whereBetween('updated_at', [$dateFrom,$dateTo]);
            });
          },'bloodType.inventory.screenedBlood',
          'bloodType.inventory.screenedBlood.donation'
        ])->orderBy('name')->get();
          }
        }  
      }
      else
      {
      $bloodTypes = BloodCategory::with([
          'bloodType.inventory.screenedBlood',
          'bloodType.inventory.screenedBlood.donation',
          'bloodType' => function($query) 
          {
            $query->whereIn('category',Auth::guard('web_admin')->user()->institute->settings['bloodtype_available']);
            $query->orderBy('category');
          },'bloodType.inventory' => function ($query)
          {
            $query->where('status','Available');
          },
        ])->orderBy('name')->get();
      }
      $types = collect('Non-reactive','Reactive','Distributed','Expired');
    	return view('admin.inventory',compact('bloodTypes'));
    }

    public function getBloodBagStatus(BloodRequest $request) {
        //
        // return response()->json($request);
        $qtyInv = $request->details->bloodType->load(['inventory' => function ($query) 
          {
            $query->where('status','Available');
          }]);
        $id = Auth::guard('web_admin')->user()->institution_id;
        $count = count($qtyInv->institutionInventory($id));
        // dd($count);
        // $string = null;
      	if($count >= $request->details->units)
      	{
      		$string[] = "Blood Inventory capable for this request(count is: ".$count.").";
      		$string[] = "Recommended: Accept and finish blood request";
      	}
      	else
      	{
      		$string[] = "Blood Inventory not capable for this request(count is: ".$count.").";
      		$string[] = "Recommended: notify to eligible donors.";

      	}
      	return response()->json(['updates' => $string, 'count' => $count]);
    }

    public function showBloodbags(Request $request)
    {
        $institutionId = Auth::guard('web_admin')->user()->institution_id;
        $pendingScreenedBloods = ScreenedBlood::where('status','Pending')->whereHas('donation', function($query) use($institutionId){ 
            $query->where('institution_id',$institutionId);
        })->get();

        return view('admin.screenedbloodbags',compact('pendingScreenedBloods'));
    }

    public function showStatustoStagedView(Request $request)
    {
      $single = false;
      $components = Auth::guard('web_admin')->user()->institute->settings['bloodtype_available'];
      $bloodbags = ScreenedBlood::whereIn('id',$request->input('bloodbags'))->get();
      $bloodbag = $bloodbags->first();
      $jsonComponents = json_encode($components);
      return view('admin.settostagedbloodbags',compact('bloodbags','bloodbag','single','components','jsonComponents'));
    }

    public function setStatusToStaged(Request $request)
    {
      // dd($request->input());
      foreach($request->input('bloodbag') as $bloodbagId)
      {
        $bloodBag = ScreenedBlood::find($bloodbagId);
        $name = $bloodBag->donation->user->bloodType;
        foreach($request->input('component') as $component)
        {
          $bloodType = BloodType::whereHas('bloodCategory', function ($query) use ($name)
              {
            $query->where('name',$name);
          })->where('category',$component)->first();
          // dd($bloodType);
          // dd($bloodType);
          $bloodinventory = BloodInventory::create([
            'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
            'screened_blood_id' => $bloodBag->id,
            'blood_type_id' => $bloodType->id,
            'expiry_date' => Carbon::now()->addDays(5)->toDateTimeString(),
            'status' => 'Pending']);

          // dd($bloodBag->bloodCategory->name);
        }
        $bloodBag->update([
        'status' => 'Staged',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);
      }
      return redirect('/admin/bloodbags')->with('status','Successfully started screening the blood bag');  

      //kwa.on ang screenedbloods given the set of ids
      //kwa.on ang mga unsa siya i-component
      //if S ang screeenedblood whole blood njd dayon na.

    }
    public function showSingleStatustoStagedView(Request $request, ScreenedBlood $bloodbag)
    {
      // dd($bloodbag);
      $single = true;
      $components = Auth::guard('web_admin')->user()->institute->settings['bloodtype_available'];
      $jsonComponents = json_encode($components);
      return view('admin.settostagedbloodbags',compact('bloodbags','bloodbag','single','components','jsonComponents'));
    }

    public function setSingleStatusToStaged(Request $request, ScreenedBlood $bloodbag)
    {
      // dd($request->input());
      $name = $bloodbag->donation->user->bloodType;
      foreach($request->input('component') as $component)
      {
        $bloodType = BloodType::whereHas('bloodCategory', function ($query) use ($name)
            {
          $query->where('name',$name);
        })->where('category',$component)->first();
        // dd($bloodType);
        $bloodinventory = BloodInventory::create([
          'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
          'screened_blood_id' => $bloodbag->id,
          'blood_type_id' => $bloodType->id,
          'expiry_date' => Carbon::now()->addDays(5)->toDateTimeString(),
          'status' => 'Pending']);

        // dd($bloodBag->bloodCategory->name);
      }
      $bloodbag->update([
        'status' => 'Staged',
        'updated_at' => Carbon::now()->toDateTimeString()
      ]);
      
      //log

      // dd($request->input('component'));
      return redirect('/admin/bloodbags')->with('status','Successfully started screening the blood bag');  

    }

    public function showStagedBloodbags(Request $request)
    {
      $institutionId = Auth::guard('web_admin')->user()->institution_id;
        $bloodBags = ScreenedBlood::where('status','Staged')->whereHas('donation', function($query) use($institutionId){ 
            $query->where('institution_id',$institutionId);
        })->get();
      return view('admin.stagedbloodbags',compact('bloodBags'));
    }

    public function showCompleteScreenedBlood(Request $request)
    {
      $single = false;
      $screenedBloodBags = ScreenedBlood::whereIn('id',$request->input('bloodbags'))->get();
      $firstScreen = $screenedBloodBags->first();
      return view('admin.showcompletebloodscreen',compact('screenedBloodBags','single','firstScreen'));
    }   

    public function completeScreenedBlood(Request $request)
    {
      $screenedBloodBags = ScreenedBlood::whereIn('id',$request->input('bloodbag'))->get();
      if($request->input('reactive') == 'true')
      {
        foreach($screenedBloodBags as $staged)
        {
        $staged->update([
          'status' => 'Done',
          'reactive' => 'true',
          'diagnose' => $request->input('diagnose'),
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        $staged->components()->update([
          'status' => 'Unavailable',
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        }
      }
      else
      {
        foreach($screenedBloodBags as $staged)
        {
        $staged->update([
          'status' => 'Done',
          'reactive' => 'false',
          'updated_at' => Carbon::now()->toDateTimeString()
          ]);
        $staged->components()->update([
          'status' => 'Available',
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        }
      }
      return redirect('/admin/bloodbags/stagedbloodbag')->with('status','Successfully screened these blood bags and added to inventory.');
    }

    public function showSingleCompleteScreenedBlood(Request $request, ScreenedBlood $staged)
    {
      $single = true;
      $screenedBloodBags = $staged;
      return view('admin.showcompletebloodscreen',compact('screenedBloodBags','single'));
    
    }
    public function completeSingleScreenedBlood(Request $request, ScreenedBlood $staged)
    {
      // dd($staged);

      if($request->input('reactive') == 'true')
      {
        // dd($request->input('diagnose'));
        $staged->update([
          'status' => 'Done',
          'reactive' => 'true',
          'diagnose' => $request->input('diagnose'),
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        $staged->components()->update([
          'status' => 'Unavailable',
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
      }
      else
      {
        // dd('false');
        $staged->update([
          'status' => 'Done',
          'reactive' => 'false',
          'updated_at' => Carbon::now()->toDateTimeString()
          ]);
        $staged->components()->update([
          'status' => 'Available',
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);

      }

      // Log::create([
      //   'initiated_id' => Auth::guard('web_admin')->user()->id,
      //   'initiated_type' => 'App\InstitutionAdmin',
      //   'reference_type' => 'App\ScreenedBlood',
      //   'reference_id' => $staged->id,
      //   'id' => strtoupper(substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 7)),
      //   'message' => 'You just succesfully completed a blood request transaction!'
      //   ]);

      return redirect('/admin/bloodbags/stagedbloodbag')->with('status','Successfully screened the blood bag and added to inventory.');  
    }

    public function getBloodBagComponents(Request $request)
    {
      $bloodBagBrand = $request->input('bloodbag');
      return response()->json(Auth::guard('web_admin')->user()->institute->settings['bloodbags'][$bloodBagBrand]);
    }

    public function flushBlood()
    {
      $bloodType = [
        'Whole Blood' => 21,
        'Packed RBC' => 42,
        'Washed RBC' => 14,
        'Platelet' => 5,
        'Fresh Frozen Plasma' => 365,
        'Cryoprecipitate' => 365
      ];

      //get blood inventory nga available pa
      $availableBloods = BloodInventory::where('status','Available')->get();
      //loop through the available blood bank
      $ctr = 0;

      foreach($availableBloods as $blood)
      {
        $cat = $blood->bloodType->category;
        $now = Carbon::now();
        if($now->diffIndays($blood->updated_at) >= $bloodType[$cat])
        {
          $blood->update([
            'status' => 'Expired',
            'updated_at' => Carbon::now()->toDateTimeString()
          ]);
        }
      }
      //check if ang updated_at is greater than 5 days 

      //if true mark as expired.
    }


    public function showBloodType(BloodCategory $bloodType)
    {
      $id = Auth::guard('web_admin')->user()->institution_id;
      try{
      $categories = BloodType::whereHas('inventory.screenedBlood.donation', function($query) use ($id)
      {
        $query->where('institution_id',$id);
      })->where('blood_category_id',$bloodType->id)->get();
      $merged = collect();
      $cats = collect();
      foreach($categories as $category)
      {
        $inventory = $category->institutionInventory($id);
        $updatedDates = $inventory->pluck('updated_at');
        $createdDates = $inventory->pluck('created_at');
        $merged = $merged->merge($updatedDates->merge($createdDates));
        if(!$cats->contains($category->category));
        $cats->push($category->id);
      }
      $dates = $merged->sort()->reverse()->unique(function ($item)
          {
            return $item->format('Y-m-d');
          });
      $inventory = null;
      if(count($categories) != 0)
      {
      $day = null;
      $logs = array();
      $expiredCount = 0;
      $nonReactiveCount = 0;
      $reactiveCount = 0;
      $soldCount = 0;
      $startedCount = 0;    
      // foreach($bloodCategory->load(['inventory' => function($query) {
      // $query->orderBy('updated_at','desc')->orderBy('created_at','desc');}])->inventory as $inventory)
      // {
      $tmpArray = array();

      foreach($dates as $date)
      {
        $inventories = BloodInventory::whereHas('screenedBlood.donation', function($query) {
          $query->where('institution_id',Auth::guard('web_admin')->user()->institution_id);
        })->whereIn('blood_type_id',$cats)->where(DB::raw('DATE(created_at)'),'=',$date->format('Y-m-d'))->orWhere(DB::raw('DATE(updated_at)'),'=',$date->format('Y-m-d'))->whereIn('blood_type_id',$cats)->get();
        $thisDate = $date->format('Y-m-d');
        foreach($inventories as $inventory)
        {
          $category = $inventory->bloodType->category;
          if($inventory->created_at->isSameDay($date))
          {
            if(!isset($logs[$thisDate]['started'][$category]))
            $logs[$thisDate]['started'][$category] = 1;
            else
            $logs[$thisDate]['started'][$category] += 1;
          }
          if($inventory->updated_at->isSameDay($date))
          {
            if($inventory->status == 'Expired')
            {
              if(!isset($logs[$thisDate]['expired'][$category]))
              $logs[$thisDate]['expired'][$category] = 1;
              else
              $logs[$thisDate]['expired'][$category] += 1;
              
              $tmpArray[] = array('date' => $inventory->screenedBlood->updated_at, 'category' => $category);

            }
            else if($inventory->status == 'Available')
            {
              if(!isset($logs[$thisDate]['available'][$category]))
              $logs[$thisDate]['available'][$category] = 1;
              else
              $logs[$thisDate]['available'][$category] += 1;
            }
            else if($inventory->status == 'Unavailable')
            {
              if(!isset($logs[$thisDate]['unavailable'][$category]))
              $logs[$thisDate]['unavailable'][$category] = 1;
              else
              $logs[$thisDate]['unavailable'][$category] += 1;
            }
            else if($inventory->status == 'Sold')
            {
              if(!isset($logs[$thisDate]['sold'][$category]))
              $logs[$thisDate]['sold'][$category] = 1;
              else
              $logs[$thisDate]['sold'][$category] += 1;

              $tmpArray[] = array('date' => $inventory->screenedBlood->updated_at, 'category' => $category);
            }
          }
        }
        foreach($tmpArray as $key => $array)
        {
          if($date->isSameDay($array['date']))
          {
            $thisDate = $date->format('Y-m-d');
            if(!isset($logs[$thisDate]['available'][$array['category']]))
            $logs[$thisDate]['available'][$array['category']] = 1;
            else
            $logs[$thisDate]['available'][$array['category']] += 1;    
            unset($tmpArray[$key]);
          }
        }
      
        }
      }
      else
      {
        $logs = null;
      }
      }
      catch(\Excetion $e)
      {
        $logs = null;
      }
      $logs = collect($logs);
      $title = $bloodType->name.' inventory';
        $type = 'bloodType';
      return view('admin.showcomponenthistory',compact('logs','title','type'));
    }
    public function showBloodCategory(BloodType $bloodCategory)
    {
      $id = Auth::guard('web_admin')->user()->institution_id;
      try{
        $inventory = $bloodCategory->institutionInventory($id);
        if($inventory != null)
        {
      $expiredCount = 0;
      $nonReactiveCount = 0;
      $reactiveCount = 0;
      $soldCount = 0;
      $startedCount = 0;


      $updatedDates = $inventory->pluck('updated_at');
      $createdDates = $inventory->pluck('created_at');
      $merged = $updatedDates->merge($createdDates);
      $dates = $merged->sort()->reverse()->unique(function ($item)
        {
          return $item->format('Y-m-d');
        });

      $logs = array();
      $tmpArray = array();

      foreach($dates as $date)
      {
        $inventories = BloodInventory::whereHas('screenedBlood.donation', function($query) {
          $query->where('institution_id',Auth::guard('web_admin')->user()->institution_id);
        })->where('blood_type_id',$bloodCategory->id)->where(DB::raw('DATE(created_at)'),'=',$date->format('Y-m-d'))->orWhere(DB::raw('DATE(updated_at)'),'=',$date->format('Y-m-d'))->where('blood_type_id',$bloodCategory->id)->get();
        $thisDate = $date->format('Y-m-d');
        foreach($inventories as $inventory)
        {
          if($inventory->created_at->isSameDay($date))
          {
            $startedCount++;
          }
          if($inventory->updated_at->isSameDay($date))
          {
            if($inventory->status == 'Expired')
            {
              $expiredCount++; 
              $tmpArray[] = $inventory->screenedBlood->updated_at;
            }
            else if($inventory->status == 'Available')
            {
              $nonReactiveCount++;
            }
            else if($inventory->status == 'Unavailable')
            {
              $reactiveCount++;
            }
            else if($inventory->status == 'Sold')
            {
              $soldCount++;
              $tmpArray[] = $inventory->screenedBlood->updated_at;
            }
          }
        }
        foreach($tmpArray as $key => $array)
        {
          if($date->isSameDay($array))
          {
            $nonReactiveCount++;
            unset($tmpArray[$key]);
          }
        }
        $logs[$thisDate]['started'] = $startedCount;
        $logs[$thisDate]['available'] = $nonReactiveCount;
        $logs[$thisDate]['unavailable'] = $reactiveCount;
        $logs[$thisDate]['sold'] = $soldCount;
        $logs[$thisDate]['expired'] = $expiredCount;
        $expiredCount = 0;
        $nonReactiveCount = 0;
        $reactiveCount = 0;
        $soldCount = 0;
        $startedCount = 0;
      }
      }
      else
      {
        $logs = null;
      }
      }
      catch(\Excetion $e)
      {
        $logs = null;
      }
      $title = $bloodCategory->category;
      $type = 'bloodCategory';
      $logs = collect($logs);
      return view('admin.showcomponenthistory',compact('logs','title','type'));
    }
}
