<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Campaign;
use Carbon\Carbon;
use App\Notifications\CampaignNotification;

class UpdateCampaignToDone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaignToDone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates a campaign set for the day to done and do the logic';

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
        $ongoingCampaigns = Campaign::where('status','Ongoing')->where('date_end','<=',Carbon::now()->addHours(12))->get();
        Campaign::where('status','Ongoing')->where('date_end','<=',Carbon::now()->addHours(12))->update([
        'status' => 'Done',
        'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        foreach($ongoingCampaigns as $campaign)
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
            $message =  $campaign->name." is officially done! Thank you for taking interest in the event.";
            $attendance = $campaign->attendance;
            foreach($attendance as $attendee)
            {
                $attendeee = $attendee;
                if($attendeee->remarks == 'Attended')
                {
                $attendeee->user->notify(new CampaignNotification($testCampaign,$user,$message));
                }
                else
                {
                    $attendeee->update([
                        'remarks' => 'Missed'
                    ]);
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
