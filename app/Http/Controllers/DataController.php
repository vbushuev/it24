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
            )->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function supplies(Request $rq){
        $res = DB::table("suppliers")->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function goods(Request $rq){
        $res = DB::table("goods")
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('goods_categories','goods_categories.good_id','=','goods.id')
            ->join('categories','goods_categories.category_id','=','categories.id')
            ->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
