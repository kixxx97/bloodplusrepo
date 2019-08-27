<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DonateRequest;
use App\MedicalHistory;
use Carbon\Carbon;
use App\Notifications\MedicalHistoryNotification;

class InitiateMedicalHistoryForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initiateMedicalForm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initiates donors to fill up medical form prior from his medical form.';

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
        //kwa.on tanan request nga karon and wala pay medical history.
        $donationRequests = DonateRequest::where('status','Ongoing')->whereDate('appointment_time',Carbon::today())->get();
        foreach($donationRequests as $donation)
        {
            // if ang diff in hours sa appointment time compared to now is between 6 or 7
                // $this->info('Display this on the screen');
            // if($donation->status == 'Ongoing')
            // {
                // asdsas
                // dd('11111');
                if($donation->appointment_time->diffInHours(Carbon::now(),false) < 17)
                {
                    // dd('12345');
                $class = [
                    "class" => "App\MedicalHistory",
                    "id" => $donation->id,
                    "time" => Carbon::now()->toDateTimeString()
                ];
                $user = [
                    "name" => $donation->institute->name(),
                    "picture" => $donation->institute->picture()
                ];
                $message = "You are requested to fill up the medical history form for your donation.";
                $donation->user->notify(
                    new MedicalHistoryNotification($class,$user,$message));
                }
            // }
        }
    }
}
