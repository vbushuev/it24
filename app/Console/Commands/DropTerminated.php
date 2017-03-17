<?php

namespace App\Console\Commands;

use DB;
use Log;
use Illuminate\Console\Command;

class DropTerminated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dropuploads';

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
     * @return mixed
     */
    public function handle()
    {
        $select = DB::table('upload_transactions')
            ->whereRaw('upload_transactions.timestamp <date_add(now(),INTERVAL -2 MINUTE) and status_id = 1')
            ->update(["time_end"=>date("Y-m-d H:i:s"),"status_id"=>"2","error_id"=>"2","message"=>"Droped by system. Too long time."]);
        //DB::table('uploads')->whereRaw('uploads.transaction_id in (select upload_transactions.id from upload_transactions where upload_transactions.timestamp < date_add(now(),INTERVAL -3 DAY)')->delete();
    }
}
