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
        ini_set('max_execution_time', 900);
        $select =  DB::table('download_schedules')
            ->whereRaw('date_add(ifnull(download_schedules.last,date_add(now(),INTERVAL -10 DAY)),INTERVAL period MINUTE) <= now()')
            ->whereRaw('not exists(select 1 from download_transactions where download_transactions.schedule_id = download_schedules.id and download_transactions.status_id=1 and date_add(download_transactions.timestamp,INTERVAL -1440 MINUTE) <= now())')

            ->orderBy('download_schedules.last','asc');
        Log::debug($select->toSql());
        $job = $select->first();
        if(is_null($job))return;
        DB::table('download_schedules')->where('id','=',$job->id)->update(['last'=>date('Y-m-d H:i:s')]);
        $job_id = DB::table('download_transactions')->insertGetId(["schedule_id"=>$job->id,"user_id"=>$job->user_id,"status_id"=>1,"error_id"=>"0"]);
        try{
            Log::debug("Download transaction: ".json_encode($job,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            $exp = new Exporter($job);

            //$file = "../../../storage/downloads/user_".$job->user_id."-".date("Y-m-d_H-i-s").".xml";
            $file = "clnt_".$job->user_id."-".date("Y-m-d_H-i-s").".xml";
            Log::debug("File:".$file);
            $str = $exp->xml($job->catalogs,$job->goods);

            file_put_contents($file,$str);
            $total=0;$summary=0;

            // установка соединения
            $srv =preg_split("/\//",$job->remote_srv);
            Log::debug("ftp connect to :".$srv[0]);
            $conn_id = ftp_connect($srv[0]);
            $remote_file = "/".(isset($srv[1])?$srv[1]."/":"").$file;
            // проверка имени пользователя и пароля
            Log::debug("ftp login with :".$job->remote_user);
            $login_result = ftp_login($conn_id, $job->remote_user,$job->remote_pass);
            Log::debug("ftp upload to :".$remote_file);
            // загрузка файла
            if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
                Log::debug("$file успешно загружен на сервер");
            } else {
                Log::error("Не удалось загрузить $file на сервер");
            }

            // закрытие соединения
            ftp_close($conn_id);

            DB::table('download_transactions')->where("id","=",$job_id)->update([
                "status_id"=>3,
                "total"=>$total,
                "summary"=>$summary,
                "time_end"=>date("Y-m-d H:i:s")
            ]);
            Log::debug("Finished transaction:".$job_id);
        }
        catch(\Exception $e){
            Log::error("Download transaction EXCEPTION !!:".$job_id);
            Log::error($e);

            $error_id =$e->getCode();
            $error_id =(preg_match("/^\d+$/",$error_id)&&$error_id<2)?$error_id:2;
            DB::table('download_transactions')->where("id","=",$job_id)->update([
                "status_id"=>2,
                "time_end"=>date("Y-m-d H:i:s"),
                "message"=>$e->getMessage(),
                "error_id"=>2
            ]);
        }
    }
}
