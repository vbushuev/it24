<?php

namespace App\Http\Controllers;


use Log;
use DB;
use App\Schedule;
use App\GoodAdds;
use App\User;
use it24\Exporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller{
    protected $_catalog_goods_count;
    public function __construct(){
        //$this->middleware('auth');
    }
    protected function filters(Request $rq,&$sel,$table=""){
        $t = (!empty($table)?$table.".":"");
        if(!empty($rq->input("category_id","")))$sel->whereIn("categories.id",$rq->input("category_id"));
        if(!empty($rq->input("catalog_id","")))$sel->whereIn("catalogs.id",$rq->input("catalog_id"));
        if(!empty($rq->input("brand_id","")))$sel->where($t."brand_id","=",$rq->input("brand_id"));
        if(!empty($rq->input("supply_id","")))$sel->where($t."supply_id","=",$rq->input("supply_id"));
        //if(!empty($rq->input("date","")))$sel->where(DB::raw("date_format(date(".(!empty($table)?$table.".":"")."timestamp),'%Y-%m-%d') = '".$rq->input("date")."'"));
        if(!empty($rq->input("date","")))$sel->whereDate($t."timestamp",'=',$rq->input("date"));
        if(!empty($rq->input("error","")))$sel->where($t."error_id",'>','0');
        if(!empty($rq->input("s","")))$sel->where($t.'title','like','%'.$rq->input("s").'%');
        if(!empty($rq->input("n","")))$sel->where($t.'name','like','%'.$rq->input("n").'%');
        if(!empty($rq->input("role","")))$sel->where($t.'role','=',$rq->input("role"));
        if(!empty($rq->input("client_id","")))$sel->where('users.id','=',$rq->input("client_id"));
        Log::debug($rq);
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
                //DB::raw('select count(uploads.id) as total, sum(amount*quantity) as summary from uploads where uploads.transaction_id = upload_transactions.id'),
                "suppliers.title",
                "errors.title as error",
                "errors.id as code"
                //,"(select count(*) from uploads where transaction_id = id) as total_counted"
            );
        $this->filters($rq,$sel,"upload_transactions");
        Log::debug($sel->toSql());
        $res = [];
        $rows = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        foreach ($rows as $r) {
            $row = (array)$r;
            $row["total"] = DB::table("uploads")->where("transaction_id","=",$r->id)->count();
            $sums = DB::table("uploads")->where("transaction_id","=",$r->id)->select(DB::raw("sum(amount*quantity) as summary"))->first();
            $row["summary"] = is_null($sums)?"0":$sums->summary;
            $res[]=$row;
        }
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
            ->select(DB::raw('download_transactions.*'),DB::raw('users.name'),DB::raw('download_schedules.id as schedule_id,download_schedules.title as schedule_title'),DB::raw('upload_statuses.title as status'));
        if(!Auth::user()->can('uploads')) $sel = $sel->where('download_schedules.user_id','=',Auth::user()->id);
        $this->filters($rq,$sel,"download_transactions");
        Log::debug("SQL: ".$sel->toSql());
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
        $id = $rq->input("id",false);
        $res=[];
        if($id!==false){
            $res["transactions"]=DB::table('download_transactions')->where('user_id','=',$id)->delete();
            $res["schedules"]=DB::table('download_schedules')->where('user_id','=',$id)->delete();
            $res["users"]=DB::table('users')->where('id','=',$id)->delete();
        }

        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function useredit(Request $rq){
        $data = $rq->all();
        $res = User::find($data["id"]);
        $res->fill([
            'name' => isset($data['name'])?$data['name']:$res->name,
            'email' => isset($data['email'])?$data['email']:$res->email,
            'password' => bcrypt($data['password']),
            'role' => isset($data["role"])?$data["role"]:$res->role
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
        $search = $rq->input("s","%");
        if($search!==false);
        // $sel = DB::table("categories")->orderBy('parent_id','asc')->where('title','like','%'.$search.'%')->whereNull("parent_id")->get();
        $sel = DB::table("categories")->orderBy('parent_id','asc')->whereNull("parent_id")->get();
        $res=[];
        foreach ($sel as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCategories($r->id,$search);
            if($search!=='%'){
                if(!count($res[$r->id]["childs"]) && stristr($res[$r->id]["title"],$search)===false) {
                    unset($res[$r->id]);
                    continue;
                }
            }
            $res[$r->id]["goods"] = DB::table("goods_categories")->where("category_id","=",$r->id)->count();
            foreach ($res[$r->id]["childs"] as $key => $value) $res[$r->id]["goods"] +=$value["goods"];
        }
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function recursiveCategories($id,$search="%"){
        if(empty($id)||is_null($id))return [];
        $res = [];
        $rows = DB::table("categories")->orderBy('parent_id','asc')->where('title','like','%'.$search.'%')->where("parent_id",'=',$id)->get();
        foreach ($rows as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCategories($r->id);
            $res[$r->id]["goods"] = DB::table("goods_categories")->where("category_id","=",$r->id)->count();
            foreach ($res[$r->id]["childs"] as $key => $value) $res[$r->id]["goods"] +=$value["goods"];
        }
        return $res;
    }
    public function goods(Request $rq){
        $from = $rq->input("f",0);
        $sel = DB::table("view_goods");
        $sel = DB::table('goods')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            //->join('goods_catalogs','goods_catalogs.good_id','=','goods.id')

            ->join('goods_categories','goods_categories.good_id','=','goods.id')
            ->join('categories',function($join){
                $join->on('categories.id','=','goods_categories.category_id');
                $join->on('categories.supply_id','=','goods.supply_id');
            })
            ->leftJoin('catalogs','catalogs.id','=','categories.internal_id')
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
	            'goods.price_add AS price_add',
	            'catalogs.id AS catalog_id',
	            'goods_categories.category_id AS category_id',
	            'brands.title AS brand',
	            'catalogs.title AS category',
                'suppliers.title AS supplier')
            ->orderBy('goods.id');
        $this->filters($rq,$sel,"goods");
        Log::debug("SQL: ".$sel->toSql());
        $res = $sel->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function goodupdate(Request $rq){
        $data = $rq->all();
        $s = DB::table("goods")->where("id","=",$rq->input("id","-1"));
        unset($data["id"]);
        $s->update($data);
        return response()->json($s->get(),200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function goodsfordownload(Request $rq){
        $from = $rq->input("f",0);
        $sel = DB::table('goods')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            ->join('goods_catalogs','goods_catalogs.good_id','=','goods.id')
            ->join('catalogs','catalogs.id','=','goods_catalogs.catalog_id');
        $this->filters($rq,$sel,"goods");
        Log::debug("SQL: ".$sel->toSql());

        $res = [
            "quantity" => $sel->count(),
            "amount" => $sel->sum('goods.price')
        ];
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function goodAdds(Request $rq){
        $code = 200;$res=[];
        $user = $rq->user();
        $gadds = GoodAdds::where('good_id',$rq->input('good_id','-1'))->where('user_id',$user->id)->first();
        if(!is_null($gadds) || isset($gadds->id)){//update
            $def = $gadds->price_add;
            $gadds->price_add = $rq->input('price_add',$def);
            $gadds->save();
            $res = $gadds;
        }else {//create
            $res=GoodAdds::create([
                "user_id"=>$user->id,
                "good_id"=>$rq->input('good_id','-1'),
                "price_add"=>$rq->input('price_add','0')
            ]);
        }
        return response()->json($res,$code,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function export(Request $rq){
        $id = $rq->input("id","-1");
        $what = DB::table("download_schedules")->where("id","=",$id)->first();
        if(isset($what->title)){
            $e = new Exporter($what);
            $f = "../storage/downloads/".$what->title."-client-".Auth::user()->id."-".date("Y-m-d_H-i-s").".xml";
            file_put_contents($f,$e->xml($what->catalogs,$what->goods));
        }
        return response()->download($f);
    }
    public function catalogpath(Request $rq){
        $id = $rq->input("id","");
        $s= $this->_catalogpath($id);
        return response()->json($s,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function catalog(Request $rq){
        $s = DB::table("catalogs");
        $parent_id = $rq->input("parent_id","");
        if(empty($parent_id))$s->whereNull('parent_id');
        else $s->where('parent_id','=',$parent_id);
        return response()->json($s->get(),200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function catalogs(Request $rq){
        $sel = DB::table("catalogs")->orderBy('parent_id','asc')->whereNull("parent_id")->get();
        $res=[];
        foreach ($sel as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCatalogs($r->id);
            $res[$r->id]["goods"]=DB::table("goods_categories")
                                    ->join("categories",'categories.id','=','goods_categories.category_id')
                                    ->join('catalogs','catalogs.id','=','categories.internal_id')
                                    ->where("catalogs.id","=",$r->id)->count();
            foreach ($res[$r->id]["childs"] as $key => $value) $res[$r->id]["goods"] +=$value["goods"];
        }
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function recursiveCatalogs($id){
        if(empty($id)||is_null($id))return [];
        $res = [];
        $rows = DB::table("catalogs")->orderBy('parent_id','asc')->where("parent_id",'=',$id)->get();
        foreach ($rows as $r) {
            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCatalogs($r->id);
            $res[$r->id]["goods"]=DB::table("goods_categories")
                                    ->join("categories",'categories.id','=','goods_categories.category_id')
                                    ->join('catalogs','catalogs.id','=','categories.internal_id')
                                    ->where("catalogs.id","=",$r->id)->count();
            foreach ($res[$r->id]["childs"] as $key => $value) $res[$r->id]["goods"] +=$value["goods"];
        }
        return $res;
    }
    protected function _catalogpath($id,$l=0){
        $r = [];
        $s = DB::table("catalogs")->where('id','=',$id)->first();
        $r[$l] = [$id=>$s->title];
        if(isset($s->parent_id)){
            $r = array_merge($r,$this->_catalogpath($s->parent_id,++$l));
        }
        return $r;
    }
    public function catalogedit(Request $rq){
        $data = $rq->all();
        $s = DB::table("catalogs")->where("id","=",$rq->input("id","-1"));
        unset($data["id"]);
        $data["parent_id"]=($data["parent_id"]=="null")?null:$data["parent_id"];
        $s->update($data);
        return response()->json($s->get(),200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function catalogremove(Request $rq){
        $s = $this->removeCatalog($rq->input("id",false));
        return response()->json($s,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function removeCatalog($id){
        if($id===false)return ["status"=>"nocatalog"];
        $res = [];
        $cat = DB::table("catalogs")->where("id","=",$id)->first();
        $childs = DB::table("catalogs")->orderBy('parent_id','asc')->where("parent_id",'=',$id)->get();
        foreach ($childs as $r) $this->removeCatalog($r->id);
        $res["categories"] = DB::table("categories")->where("internal_id","=",$id)->update(["internal_id"=>null]);
        $res["goods"] = DB::table("goods_catalogs")->where("catalog_id","=",$id)->delete();
        $res["catalogs"]= DB::table("catalogs")->where("id","=",$id)->delete();
        return $res;
    }
    protected function linkCatalog($id,$data,$copy=false){
        $res = [];
        $res[]=["id"=>$id];
        $childs = DB::table("categories")->where("parent_id","=",$id)->get();
        $catalog =$copy? DB::table("catalogs")->where("id","=",$data["internal_id"])->first():null;
        foreach ($childs as $child){
            if($copy){
                $rc = $this->_catalogadd(["title"=>$child->title,"level"=>$catalog->level+1,"parent_id"=>$data["internal_id"]]);
                Log::debug($rc);
                $res=array_merge($res,$this->linkCatalog($child->id,["internal_id"=>$rc["id"]],$copy));
            }
            else $res=array_merge($res,$this->linkCatalog($child->id,$data));
        }
        unset($data["copy"]);
        DB::table("categories")->where("id","=",$id)->update($data);
        return $res;
    }
    public function cataloglink(Request $rq){
        $data = $rq->all();
        unset($data["id"]);
        $id = $rq->input("id","-1");
        $copy = $rq->input("copy","-1");
        $copy = ($copy=="1"||$copy=="true"||$copy==true)?true:false;
        $s = $this->linkCatalog($id,$data,$copy);
        return response()->json($s,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function catalogunlink(Request $rq){
        $id = $rq->input("id","-1");
        $s = $this->linkCatalog($id,["internal_id"=>null]);
        return response()->json($s,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function _catalogadd($rq){
        $q = DB::table("catalogs")->where("title","=",$rq["title"]);
        if($rq["parent_id"]!="null"&&!is_null($rq["parent_id"])) $q->where("parent_id","=",$rq["parent_id"]);
        $s = $q->get();
        $res = ["status"=>"unknown"];
        $code = 500;
        if(!count($s)){
            $data = ["title"=>$rq["title"],"level"=>$rq["level"]];
            if($rq["parent_id"]!="null"&&!is_null($rq["parent_id"]))$data["parent_id"]=$rq["parent_id"];
            $s = DB::table("catalogs")->insertGetId($data);
            $res = ["status"=>"ok","id"=>$s];
            $code = 200;
        }
        else {
            $res = ["status"=>"already"];
            $code = 304;
        }
        $res["code"]=$code;
        return $res;
    }
    public function catalogadd(Request $rq){
        $data = $rq->all();
        $res = $this->_catalogadd($rq->all());
        $code = $res["code"];
        return response()->json($res,$code,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function goodPage(Request $rq){
        $from = $rq->input("f",0);
        $limit = $rq->input("l",24);
        $sel = DB::table('goods')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            ->join('goods_categories','goods_categories.good_id','=','goods.id')
            ->join('categories',function($join){
                $join->on('categories.id','=','goods_categories.category_id');
                $join->on('categories.supply_id','=','goods.supply_id');
            })
            ->leftJoin('catalogs','catalogs.id','=','categories.internal_id')
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
	            'goods.price_add AS price_add',
	            'catalogs.id AS catalog_id',
	            'goods_categories.category_id AS category_id',
	            'brands.title AS brand',
	            'catalogs.title AS category',
                'suppliers.title AS supplier');

        $this->filters($rq,$sel,"goods");
        Log::debug("SQL: ".$sel->toSql());
        $count = $sel->count();
        $sel->orderBy('goods.id');
        $res = $sel->offset($from)->limit($limit)->get();
        $response = [
            "from"=>$from,
            "limit"=>$limit,
            "count"=>$count,
            "data"=>$res
        ];
        return response()->json($response,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
