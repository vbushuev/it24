<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use DB;
use it24\Product as Product;
use it24\Category as Category;
use it24\utoys\Parser as utoys;
use it24\galacenter\Parser as galacenter;

class UploadsIT24 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for uploads';

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
        //select schedules.id as schedule_id,schedules.period, schedules.last,suppliers.title,schedules.supply_id,suppliers.link from schedules join suppliers on suppliers.id=schedules.supply_id where date_add(schedules.last,INTERVAL period MINUTE)<=now();
        $jobs = DB::table('schedules')
            ->select('schedules.id as schedule_id','schedules.period','schedules.last','suppliers.title','suppliers.code','schedules.supply_id','schedules.protocol_id','suppliers.link')
            ->join('suppliers','suppliers.id','=','schedules.supply_id')

            ->where('schedules.supply_id','=','2')
            //->whereRaw('date_add(schedules.last,INTERVAL period MINUTE) <= now()')
            ->orderBy('schedules.last','asc')
            ->get();
        foreach ($jobs as $job) {
            DB::table('schedules')->where('id','=',$job->schedule_id)->update(['last'=>date('Y-m-d H:i:s')]);
            if(!in_array($job->protocol_id,[1]))continue;
            $job_id = DB::table('upload_transactions')->insertGetId(["schedule_id"=>$job->schedule_id,"supply_id"=>$job->supply_id,"status_id"=>1,"error_id"=>"0"]);
            try{
                $out = file_get_contents($job->link);
                //Log::debug($job->title." ".$job->link);
                libxml_use_internal_errors();
                $xml = simplexml_load_string($out);
                foreach(libxml_get_errors() as $e){
                    Log::error($e);
                    throw new \Exception("no-connection",1);
                }
                //Parser
                $nm = "it24\\".$job->code."\\Parser";
                $parser = new $nm;
                $total = $parser->parse($xml,["job_id"=>$job_id,"job"=>$job]);

                DB::table('upload_transactions')->where("id","=",$job_id)->update([
                    "status_id"=>3,
                    "total"=>$total,
                    "time_end"=>date("Y-m-d H:i:s")
                ]);
            }
            catch(\Exception $e){
                Log::debug($e);
                $error_id =$e->getCode();
                $error_id =(preg_match("/^\d+$/",$error_id)&&$error_id<2)?$error_id:2;
                DB::table('upload_transactions')->where("id","=",$job_id)->update([
                    "status_id"=>2,
                    "time_end"=>date("Y-m-d H:i:s"),
                    "message"=>$e->getMessage(),
                    "error_id"=>$error_id
                ]);
            }

        }
        //file_put_contents("catalog.xml",$out);

    }
}
