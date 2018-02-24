<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BloodInventory;
use Carbon\Carbon;
class FlushBlood extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flushBlood';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flushes blood bags in inventory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
    }
}
