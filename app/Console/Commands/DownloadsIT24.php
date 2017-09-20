<?php

namespace App\Console\Commands;

use DB;
use Log;
use it24\Exporter as Exporter;
use Illuminate\Console\Command;

class DownloadsIT24 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:downloads';

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
        $job = DB::table('download_schedules')
            //->whereRaw('not exists(select 1 from download_transactions where schedule_id = download_schedules.id and status_id=1)')
            //->whereRaw('date_add(download_schedules.last,INTERVAL period MINUTE) <= now()')
            ->orderBy('download_schedules.last','asc')
            ->first();
        if(is_null($job))return;
        DB::table('download_schedules')->where('id','=',$job->id)->update(['last'=>date('Y-m-d H:i:s')]);
        $job_id = DB::table('download_transactions')->insertGetId(["schedule_id"=>$job->id,"user_id"=>$job->user_id,"status_id"=>1,"error_id"=>"0"]);
        try{
            $e = new Exporter();
            //file_put_contents()
            $e->xml();
            $total=0;$summary=0;
            DB::table('download_transactions')->where("id","=",$job_id)->update([
                "status_id"=>3,
                "total"=>$total,
                "summary"=>$summary,
                "time_end"=>date("Y-m-d H:i:s")
            ]);
        }
        catch(\Exception $e){
            Log::debug($e);
            $error_id =$e->getCode();
            $error_id =(preg_match("/^\d+$/",$error_id)&&$error_id<2)?$error_id:2;
            DB::table('download_transactions')->where("id","=",$job_id)->update([
                "status_id"=>2,
                "time_end"=>date("Y-m-d H:i:s"),
                "message"=>$e->getMessage(),
                "error_id"=>$error_id
            ]);
        }
    }
}
