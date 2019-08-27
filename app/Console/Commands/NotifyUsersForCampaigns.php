<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Notifications\GeneralNotification;
use App\Campaign;

class NotifyUsersForCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifyUsersCampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notify the attendance and the institution for the campaign';

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
        //get all pending campaigns na due in less than a week;

        $pendingCampaigns = Campaign::where('status','Pending')->get();
        foreach($pendingCampaigns as $campaign)
        {
            $class = [
                "class" => "App\Campaign",
                "id" => $campaign->id,
                "time" => Carbon::now()->toDateTimeString()
            ];
            
            $dayMessage = $campaign->name." is going to happen tomorrow.";
            $weekMessage = $campaign->name." is a week from now.";
            if($campaign->date_start->diffInDays() == 1)
            {
                $this->fireNotification($campaign,$class,$dayMessage);
            }
            else if($campaign->date_start->diffInWeeks() == 1 && $campaign->date_start->diffInDays() == 7)
            {
                $this->fireNotification($campaign,$class,$weekMessage);
            }
    
        }
    }

    public function fireNotification($campaign,$class,$message)
    {
        $followers = $campaign->initiated->institute->followers;
        $attending = $campaign->attendanceUserModel;

        $leftBubble = $followers->diff($attending);
        $intersection = $followers->intersect($attending);

        
        foreach($leftBubble as $attendee)
        {
            $tmpMessage = $message + "See you there!";
            $user = [
            "name" => $attendee->name(),
            "picture" => $attendee->picture()
            ];
            $user->notify(new GeneralNotification($class,$user,$tmpMessage));
        }
        foreach($intersection as $potentialAttendee)
        {
            $tmpMessage = $message + "Come join us!";
            $user = [
            "name" => $potentialAttendee->name(),
            "picture" => $potentialAttendee->picture()
            ];
            $user->notify(new GeneralNotification($class,$user,$tmpMessage));
        }

        $admins = $campaign->initiated->institute->admins;
        foreach($admins as $admin)
        {
            $user = [
            "name" => $attendee->user->name(),
            "picture" => $attendee->user->picture()
            ];
            $admin->notify(new GeneralNotification($class,$user,$message));
        }
    }
}
