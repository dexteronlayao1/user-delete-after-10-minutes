<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $timeFrame = $this->choice(
            'Select the timeframe. If you did not select any of the timeframe, the default value would be Day',
            ['Day', 'Hour', 'Minute', 'Second'],
            0
        );

        $time = $this->ask("Input the number of $timeFrame/s");

        $newTimeFrame = $time > 1 ? "{$timeFrame}s" : $timeFrame;

        if ($this->confirm("Users that were created more than $time $newTimeFrame will be deleted. Do you wish to continue?")) {
            try {
                $usersCount = DB::table('users')->whereRaw("TIMESTAMPDIFF($timeFrame, created_at, now()) > $time")->count();
                DB::table('users')->whereRaw("TIMESTAMPDIFF($timeFrame, created_at, now()) > $time")->delete();
                $this->info("Number of users deleted: $usersCount");
            } catch (\Exception $ex) {
                $this->error('Something went wrong. Please double check the value you inserted');
            }
        }
    }
}
