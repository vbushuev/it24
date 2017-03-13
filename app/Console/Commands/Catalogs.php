<?php

namespace App\Console\Commands;

use DB;

use Illuminate\Console\Command;

class Catalogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:catalogs';

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
        $p = DB::table('categories')->whereNotNull('internal_id')->get();
        foreach ($p as $parent) {
            $s = DB::table('categories')
                ->where('parent_id','=',$parent->id)
                ->get();
            foreach ($s as $c) {
                try{
                    echo $parent->title. "[".$parent->internal_id."] => ".$c->title."\n";
                    $r = DB::table('catalogs')->where("title",'=',$c->title)->first();
                    $id="";
                    if(!isset($r->id))$id = DB::table('catalogs')->insertGetId(["title"=>$c->title,"parent_id"=>$parent->internal_id]);
                    else $id = $r->id;
                    DB::table('categories')->where('id','=',$c->id)->update(["internal_id"=>$id]);
                }catch(\Exception $e){}
            }
        }
        return true;
    }
}
