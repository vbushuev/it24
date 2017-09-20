<?php

namespace App\Http\Controllers;


use Log;
use DB;
use App\Schedule;
use App\User;
use it24\Exporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller{
    public function __construct(){
        //$this->middleware('auth');
    }
    protected function filters(Request $rq,&$sel,$table=""){
        $t = (!empty($table)?$table.".":"");
        if(!empty($rq->input("category_id","")))$sel->whereIn("categories.id",$rq->input("category_id"));
        if(!empty($rq->input("brand_id","")))$sel->where($t."brand_id","=",$rq->input("brand_id"));
        if(!empty($rq->input("supply_id","")))$sel->where($t."supply_id","=",$rq->input("supply_id"));
        //if(!empty($rq->input("date","")))$sel->where(DB::raw("date_format(date(".(!empty($table)?$table.".":"")."timestamp),'%Y-%m-%d') = '".$rq->input("date")."'"));
        if(!empty($rq->input("date","")))$sel->whereDate($t."timestamp",'=',$rq->input("date"));
        if(!empty($rq->input("error","")))$sel->where($t."error_id",'>','0');
        if(!empty($rq->input("s","")))$sel->where($t.'title','like','%'.$rq->input("s").'%');
        if(!empty($rq->input("n","")))$sel->where($t.'name','like','%'.$rq->input("n").'%');
        if(!empty($rq->input("role","")))$sel->where($t.'role','=',$rq->input("role"));
    }
    public function index(Request $rq){
        return response()->json([],200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function uploads(Request $rq){
        $sel= DB::table("upload_transactions")
            ->join("upload_statuses","upload_statuses.id","=","upload_transactions.status_id")
            ->join("errors","errors.id","=","upload_transactions.error_id")
            ->join("suppliers","suppliers.id","=","upload_transactions.supply_id")
            ->orderBy('upload_transactions.timestamp','desc')
            ->select("upload_transactions.id",
                "upload_transactions.timestamp as start",
                "upload_transactions.time_end as end",
                "upload_transactions.total",
                "upload_transactions.summary",
                "upload_transactions.message",
                "upload_statuses.title as status",
                "suppliers.title",
                "errors.title as error",
                "errors.id as code"
                //,"(select count(*) from uploads where transaction_id = id) as total_counted"
            );
        $this->filters($rq,$sel,"upload_transactions");

        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function uploadsProgress(Request $rq){
        $res = DB::table("uploads")
            ->where('transaction_id','=',$rq->input('tr_id',0))
            ->select(DB::raw('count(uploads.id) as total, sum(amount*quantity) as sum'))
            ->first();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function supplierupdate(Request $rq){
        $res = DB::table('suppliers')->where("id","=",$rq->input("id",-1))
            ->update([
                "title"=>$rq->input("title"),
                "link"=>$rq->input("link"),
                "code"=>$rq->input("code"),
                "inn"=>$rq->input("inn")
            ]);
        $res = DB::table('schedules')->where("supply_id","=",$rq->input("id",-1))
                ->update(["period"=>$rq->input("period")]);

        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function suppliers(Request $rq){
        $sel = DB::table("suppliers")
            ->join("schedules","schedules.supply_id","=","suppliers.id")
            ->join("protocols","schedules.protocol_id","=","protocols.id")
            ->select(
                'suppliers.id',
                'suppliers.title',
                'suppliers.inn',
                'suppliers.code',
                'suppliers.link',
                'schedules.id as schedule_id',
                'schedules.period',
                'schedules.last',
                'protocols.id as protocol_id',
                'protocols.title as protocol'
            );
        $this->filters($rq,$sel);
        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function schedules(Request $rq){
        $sel = DB::table("download_schedules")
            ->where('download_schedules.user_id','=',Auth::user()->id);
        //$this->filters($rq,$sel);
        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function scheduleadd(Request $rq){
        $res = Schedule::create($rq->all());
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function scheduleedit(Request $rq){
        $d=$rq->all();
        $res = Schedule::find($d["id"]);
        $res->fill($d);
        $res->save();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function downloads(Request $rq){
        $sel = DB::table("download_transactions")
            ->join('download_schedules','download_schedules.id','=','download_transactions.schedule_id')
            ->join('users','users.id','=','download_schedules.user_id')
            ->join('upload_statuses','upload_statuses.id','=','download_transactions.status_id')
            ->orderBy("download_transactions.id","desc")
            ->select(DB::raw('download_transactions.*'),DB::raw('users.name'),DB::raw('download_schedules.*'),DB::raw('upload_statuses.title as status'));
        if(!Auth::user()->can('uploads')) $sel = $sel->where('download_schedules.user_id','=',Auth::user()->id);
        $this->filters($rq,$sel);

        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function brands(Request $rq){
        $res = DB::table("brands")
            ->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function roles(Request $rq){
        $res = DB::table("roles")->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function users(Request $rq){
        $sel = DB::table("users");
        $this->filters($rq,$sel,'users');
        $res=$sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function userdel(Request $rq){
        $res=[];
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function useredit(Request $rq){
        $data = $rq->all();
        $res = User::find($data["id"]);
        $res->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data["role"]
        ]);
        $res->save();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function useradd(Request $rq){
        $data = $rq->all();
        $res = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data["role"]
        ]);
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function categories(Request $rq){
        $sel = DB::table("categories")->orderBy('parent_id','asc')->whereNull("parent_id")->get();
        $res=[];
        foreach ($sel as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCategories($r->id);
        }
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function recursiveCategories($id){
        if(empty($id)||is_null($id))return [];
        $res = [];
        $rows = DB::table("categories")->orderBy('parent_id','asc')->where("parent_id",'=',$id)->get();
        foreach ($rows as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCategories($r->id);
        }
        return $res;
    }
    public function goods(Request $rq){
        $from = $rq->input("f",0);
        $sel = DB::table("view_goods");
        $sel = DB::table('goods')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            ->join('goods_categories','goods_categories.good_id','=','goods.id')
            ->join('categories','categories.id','=','goods_categories.category_id')
            ->select('goods.id AS id',
                'goods.sid AS sid',
	            'goods.timestamp AS timestamp',
	            'goods.title AS title',
	            'goods.sku AS sku',
	            'goods.description AS description',
	            'goods.certificate AS certificate',
	            'goods.barcode AS barcode',
	            'goods.image AS image',
	            'goods.unit AS unit',
	            'goods.pack AS pack',
	            'goods.weight AS weight',
	            'goods.width AS width',
	            'goods.depth AS depth',
	            'goods.height AS height',
	            'goods.brand_id AS brand_id',
	            'goods.supply_id AS supply_id',
	            'goods.price AS price',
	            'goods_categories.category_id AS category_id',
	            'brands.title AS brand',
	            'categories.title AS category',
                'suppliers.title AS supplier')
            ->orderBy('goods.id');
        $this->filters($rq,$sel,"goods");
        Log::debug("SQL: ".$sel->toSql());
        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function export(Request $rq){
        $e = new Exporter();
        $f = "../storage/downloads/user_".Auth::user()->id."-".date("Y-m-d_H-i-s").".xml";
        file_put_contents($f,$e->xml());
        return response()->download($f);
    }
}
