<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DB;

class DataController extends Controller{
    public function __construct(){
        //$this->middleware('auth');
    }
    public function index(Request $rq){
        return response()->json([],200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function uploads(Request $rq){
        $res = DB::table("upload_transactions")
            ->join("upload_statuses","upload_statuses.id","=","upload_transactions.status_id")
            ->join("errors","errors.id","=","upload_transactions.error_id")
            ->join("suppliers","suppliers.id","=","upload_transactions.supply_id")
            ->orderBy('upload_transactions.timestamp','desc')
            ->select("upload_transactions.id",
                "upload_transactions.timestamp as start",
                "upload_transactions.time_end as end",
                "upload_transactions.total",
                "upload_transactions.message",
                "upload_statuses.title as status",
                "suppliers.title",
                "errors.title as error",
                "errors.id as code"
                //,"(select count(*) from uploads where transaction_id = id) as total_counted"
            )
            //->where('upload_transactions.id','<=',$rq->input("f","99999999"))
            ->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function uploadsProgress(Request $rq){
        $res = DB::table("uploads")
            ->where('transaction_id','=',$rq->input('tr_id',0))
            ->select(DB::raw('count(uploads.id) as total','sum(amount) as sum'))
            ->first();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function suppliers(Request $rq){
        $res = DB::table("suppliers")
            ->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function brands(Request $rq){
        $res = DB::table("brands")
            ->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
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
        if(!empty($rq->input("brand_id","")))$sel->where("goods.brand_id","=",$rq->input("brand_id"));
        if(!empty($rq->input("supply_id","")))$sel->where("goods.supply_id","=",$rq->input("supply_id"));
        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
