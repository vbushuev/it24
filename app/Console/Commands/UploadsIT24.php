<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use DB;
use it24\Product as Product;
use it24\Category as Category;

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
            ->select('schedules.id as schedule_id','schedules.period','schedules.last','suppliers.title','schedules.supply_id','schedules.protocol_id','suppliers.link')
            ->join('suppliers','suppliers.id','=','schedules.supply_id')
            ->whereRaw('date_add(schedules.last,INTERVAL period MINUTE) <= now()')
            ->orderBy('schedules.last','asc')
            ->get();
        foreach ($jobs as $job) {
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
                $cats = isset($xml->xml_catalog)?$xml->xml_catalog->shop->categories->category:((isset($xml->yml_catalog))?$xml->yml_catalog->shop->categories->category:null);
                if(!isset($cats)&&!is_null($cats)){
                    Log::error("No data in ".$job->title." ".$job->link);
                    throw new \Exception("no-categories",2);
                }
                foreach($cats as $category){
                    $a = [
                        "external_id" =>$category["id"]->__toString(),
                        "external_parent_id" =>isset($category["parentId"])?$category["parentId"]->__toString():null,
                        "title" => $category->__toString(),
                        "supply_id"=>$job->supply_id
                    ];
                    $o = new Category($a);
                    $o->store();
                }
                $prods = isset($xml->xml_catalog)?$xml->xml_catalog->shop->offers->offer:$xml->yml_catalog->shop->offers->offer;
                if(!isset($prods)&&!is_null($prods)){
                    Log::error("No data in ".$job->title." ".$job->link);
                    throw new \Exception("no-products",2);
                }
                //print_r($prods);
                $total=0;
                foreach ($prods as $item) {
                    $a = [
                        "transaction_id"=>$job_id,
                        "supply_id"=>$job->supply_id,
                        "sid"=>$item["id"]->__toString(),
                        "sku"=>$item["articul"]->__toString(),
                        "quantity"=>($item["available"]=="false")?"0":"0",
                        "dateupdate"=>$item["dateupdate"]->__toString(),
                        "external_category_id"=>$item->categoryId->__toString(),
                        "title"=>$item->name->__toString(),
                        "image_url"=>$item->picture->__toString(),
                        "amount"=>"",
                        "pack"=>"",
                        "brand"=>"",
                        "barcode"=>"",
                        "depth"=>"",
                        "width"=>"",
                        "height"=>"",
                        "weight"=>"",
                        "unit"=>"",
                        "certificate"=>"",
                        "description"=>""
                    ];
                    foreach($item->param as $param){
                        if($param['name'] == 'price_base')$a["amount"]=$param->__toString();
                        else if($param['name'] == 'box')$a["pack"]=$param->__toString();
                        else if($param['name'] == 'brand')$a["brand"]=$param->__toString();
                        else if($param['name'] == 'quantity')$a["quantity"]=$param->__toString();
                        else if($param['name'] == 'barcode')$a["barcode"]=$param->__toString();
                        else if($param['name'] == 'leigh')$a["depth"]=$param->__toString();
                        else if($param['name'] == 'width')$a["width"]=$param->__toString();
                        else if($param['name'] == 'height')$a["height"]=$param->__toString();
                    }
                    $o = new Product($a);
                    $o->store();
                    $total++;
                }
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
                print_r($error_id);
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
