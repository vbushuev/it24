<?php

namespace App\Http\Controllers;

use Log;
use DB;
use Mail;
use App\Schedule;
use App\GoodAdds;
use App\User;
use App\Catalog;
use App\UserCatalog;
use App\UserCatalogGood;
use App\UserGood;
use it24\Exporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ClientController extends Controller{
    protected $_catalog_goods_count;
    public function __construct(){}
    protected function recursiveGetUserCatalogs($user,$parent_id,$callable=null){
        $catalogs = UserCatalog::where('user_id','=',$user->id)->where('parent_id',$parent_id)->get();
        $res = [];
        foreach($catalogs->toArray() as $cat){
            $cat["childs"] = $this->recursiveGetUserCatalogs($user,$cat["id"]);
            $res[]=$cat;
            if(!is_null($callable) && is_callable($callable)) $callable([
                "user"=>$user,
                "parent_id"=>$parent_id,
                "catalog"=>$cat
            ]);
        }
        return $res;
    }


    public function goods(Request $rq){
        $user = $rq->user();
        return view('pages.mygoods',['panel'=>'mygoods','user'=>$user]);
    }
    public function getcatalogs(Request $rq){
        $user = $rq->user();
        $res = $this->recursiveGetUserCatalogs($user,'0');
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function addcatalog(Request $rq){
        $user = $rq->user();
        $parent_id = empty($rq->input("parent_id","0"))?"0":$rq->input("parent_id","0");

        $catalog = UserCatalog::create([
            'user_id'=>$user->id,
            'title'=>$rq->input("title"),
            'parent_id'=>$parent_id
        ]);
        return response()->json($catalog,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function deletecatalog(Request $rq){
        $user = $rq->user();
        $catalog = UserCatalog::find($rq->input("id","0"));
        $this->recursiveGetUserCatalogs($user,$catalog->id,function($p){
            UserCatalogGood::where('user_catalog_id',$p["catalog"]['id'])->delete();
            UserCatalog::find($p["catalog"]['id'])->delete();
        });
        UserCatalogGood::where('user_catalog_id',$catalog->id)->delete();
        $catalog->delete();
        return response()->json(["success"=>true],200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function editcatalog(Request $rq){
        $user = $rq->user();
        $parent_id = empty($rq->input("parent_id","0"))?"0":$rq->input("parent_id","0");

        $catalog = UserCatalog::find($rq->input("id","0"));
        if($catalog!==false)$catalog->update([
            'user_id'=>$user->id,
            'title'=>$rq->input("title"),
            'parent_id'=>$parent_id
        ]);
        return response()->json($catalog,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function copyCatalog2UserCatalog($user,$userCatalogId,$catalogId){
        $userCatalog = UserCatalog::find($userCatalogId);
        $catalog = Catalog::find($catalogId);
        $newUserCatalog = UserCatalog::create([
            'user_id'=>$user->id,
            'title'=>$catalog->title,
            'parent_id'=>($userCatalogId==false)?'0':$userCatalog->id
        ]);
        $goods = DB::table('goods_categories')
            ->join('categories','categories.id','=','goods_categories.category_id')
            ->join('catalogs','catalogs.id','=','categories.internal_id')
            ->where('catalogs.id','=',$catalog->id)
            ->select('goods_categories.good_id')
            ->get();
        foreach ($goods as $good) {
            UserCatalogGood::create([
                'good_id'=>$good->good_id,
                "user_catalog_id"=>$newUserCatalog->id
            ]);
        }
        return $newUserCatalog;
    }
    public function copycatalog(Request $rq){
        $data = $rq->all();$res=[];
        $uc = $rq->input("user_catalog_id",false);
        $cat = $rq->input("catalog_id",false);
        if($cat != false ){
            $user = $rq->user();
            $newUserCatalog = $this->copyCatalog2UserCatalog($user,$uc,$cat);
            $links = [
                $cat=>["id"=>$newUserCatalog->id,"name"=>$newUserCatalog->title]
            ];
            // $res=$this->recursiveGetCatalogs($cat);
            // foreach($res as $ocat){
            //     $parent = $this->copyCatalog2UserCatalog($user,$newUserCatalog->id,$ocat);
            //     if($ocat)
            // }
            $res=$this->recursiveGetCatalogs($cat,function($p)use($user,$newUserCatalog,&$links){
                Log::debug(json_encode($links,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                $cat = $p["catalog"]["id"];
                $parent_id = $newUserCatalog->id;
                if(isset($p["parent_id"]) && isset($links[$p["parent_id"]])) $parent_id = $links[$p["parent_id"]]["id"];
                $userCatalog = $this->copyCatalog2UserCatalog($user,$parent_id,$cat);
                $links[$cat]=["id"=>$userCatalog->id,"name"=>$userCatalog->title,"callback"=>$p];
            });
            // $ucg = UserCatalogGood::create($data);
        }
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function recursiveGetCatalogs($parent_id,$callable=null){
        $catalogs = Catalog::where('parent_id',$parent_id)->orderBy("id")->get();
        $res = [];
        foreach($catalogs->toArray() as $cat){
            if(!is_null($callable) && is_callable($callable)) $callable([
                "parent_id"=>(($parent_id===false)?'null':$parent_id),
                "catalog"=>$cat
            ]);
            $cat["childs"] = $this->recursiveGetCatalogs($cat["id"],$callable);
            $res[]=$cat;
        }
        return $res;
    }
    public function linkcatalog(Request $rq){
        $data = $rq->all();
        $ucg= UserCatalogGood::create($data);
        return response()->json($ucg,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function unlinkcatalog(Request $rq){
        $ucg= UserCatalogGood::where("user_catalog_id",$rq->input("user_catalog_id"))->where("good_id",$rq->input("good_id"))->first();
        $ucg->delete();
        return response()->json($ucg,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    public function getgoods(Request $rq){
        $user = $rq->user();
        $from = $rq->input("f",0);
        $limit = $rq->input("l",24);
        $sel = DB::table('goods')
            ->join('brands','brands.id','=','goods.brand_id')
            ->join('suppliers','suppliers.id','=','goods.supply_id')
            // ->join('user_cata','goods_categories.good_id','=','goods.id')
            // ->join('categories',function($join){
            //     $join->on('categories.id','=','goods_categories.category_id');
            //     $join->on('categories.supply_id','=','goods.supply_id');
            // })
            ->join('user_catalog_goods','user_catalog_goods.good_id','=','goods.id')
            ->join('user_catalogs','user_catalog_goods.user_catalog_id','=','user_catalogs.id')
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
	            'user_catalogs.id AS catalog_id',
	            // 'goods_categories.category_id AS category_id',
	            'brands.title AS brand',
	            'user_catalogs.title AS category',
                'suppliers.title AS supplier')
            ->where('user_catalogs.user_id','=',$user->id);
        if($rq->input('user_catalog_id',false)!==false)
            $sel=$sel->where('user_catalogs.id','=',$rq->input('user_catalog_id'));
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
                "inn"=>$rq->input("inn"),
                "price_add"=>$rq->input("price_add","0"),
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
                'suppliers.price_add',
                'schedules.id as schedule_id',
                'schedules.period',
                'schedules.last',
                'protocols.id as protocol_id',
                'protocols.title as protocol'
            );
        $this->filters($rq,$sel);
        $res = $sel->orderBy("id")->offset($rq->input("f",0))->limit($rq->input("l",24))->get();
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
        $sort = $rq->input("sort",false);
        $sel = DB::table("catalogs")->whereNull("parent_id");
        if($sort!==false)$sel=$sel->orderBy('title');

        $rows=$sel->get();
        $res=[];
        foreach ($rows as $r) {
            $rd = (array)$r;
            $rd["childs"]=$this->recursiveCatalogs($r->id,$sort);
            $rd["goods"]=DB::table("goods_categories")
                                    ->join("categories",'categories.id','=','goods_categories.category_id')
                                    ->join('catalogs','catalogs.id','=','categories.internal_id')
                                    ->where("catalogs.id","=",$r->id)->count();
            foreach ($rd["childs"] as $key => $value) $rd["goods"] +=$value["goods"];
            $res[]=$rd;continue;

            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCatalogs($r->id,$sort);
            $res[$r->id]["goods"]=DB::table("goods_categories")
                                    ->join("categories",'categories.id','=','goods_categories.category_id')
                                    ->join('catalogs','catalogs.id','=','categories.internal_id')
                                    ->where("catalogs.id","=",$r->id)->count();
            foreach ($res[$r->id]["childs"] as $key => $value) $res[$r->id]["goods"] +=$value["goods"];
        }
        Log::debug(json_encode($res));
        return response()->json($res,200,['Content-Type' => 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    protected function recursiveCatalogs($id,$sort){
        if(empty($id)||is_null($id))return [];
        $res = [];
        $sel = DB::table("catalogs")->orderBy('parent_id','asc')->where("parent_id",'=',$id);
        if($sort!==false)$sel=$sel->orderBy('title','asc');
        $rows = $sel->get();
        foreach ($rows as $r) {
            $rd = (array)$r;
            $rd["childs"]=$this->recursiveCatalogs($r->id,$sort);
            $rd["goods"]=DB::table("goods_categories")
                                    ->join("categories",'categories.id','=','goods_categories.category_id')
                                    ->join('catalogs','catalogs.id','=','categories.internal_id')
                                    ->where("catalogs.id","=",$r->id)->count();
            foreach ($rd["childs"] as $key => $value) $rd["goods"] +=$value["goods"];
            $res[]=$rd;continue;

            $res[$r->id] = (array)$r;
            $res[$r->id]["childs"]=$this->recursiveCatalogs($r->id,$sort);
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

    public function cataloglink(Request $rq){
        $data = $rq->all();
        unset($data["id"]);
        $id = $rq->input("id","-1");
        $copy = $rq->input("copy","-1");
        Log::debug("COPY=".$copy);
        $copy = ($copy=="true")?true:false;
        Log::debug("COPY[".$copy."]=".($copy?"true":"false"));
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
