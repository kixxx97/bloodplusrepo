<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Campaign;
use App\Attendance;
use Carbon\Carbon;
use App\Notifications\CampaignNotification;
class UpdateCampaignToOngoing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaignToOngoing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updates a campaign to its user and creator that there is an upcoming campaign na ilaha gi apilan/buhatan';

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
        //get all campaigns that are pending and are due today and make it ongoing 0600 , 0800
        
        Campaign::where('status','Pending')->whereDate('date_start','<=',Carbon::now()->addHours(12))->update([
        'status' => 'Ongoing',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        
        $onGoingCampaigns = Campaign::where('status','Ongoing')->get();
        foreach($onGoingCampaigns as $campaign)
        {
            $testCampaign = [
                'class' => 'App\Campaign',
                'id' => $campaign->id,
                'time' => Carbon::now()->toDateTimeString()
            ];
            $user = [
                'name' => $campaign->initiated->name(),
                'picture' => $campaign->initiated->picture()
            ];
            
            $message = $campaign->name." is happening today at ".$campaign->date_start->format('h:i A').". See you there!";
            $attendance = $campaign->attendance;
            foreach($attendance as $attendee)
            {
                if($attendee->status == 'Going')
                {
                $attendee->user->notify(new CampaignNotification($testCampaign,$user,$message));
                }
            }
            $institute = $campaign->initiated->institute;
            $admins = $institute->admins;
            foreach($admins as $admin)
            {
                $admin->notify(new CampaignNotification($testCampaign,$user,$message));
            }
        }
    }
}
