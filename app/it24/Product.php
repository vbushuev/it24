<?php
namespace it24;
use DB as DB;
use Log as Log;
use it24\Common as Common;

class Product extends Common{
    public function __construct($a = []){
        $this->loadFromArray($a);
    }
    public function store(){
        $this->_properties["title"] = ((!isset($this->_properties["title"]))||empty($this->_properties["title"])||$this->_properties["title"]=="")?"notitle":$this->_properties["title"];
        //Log::debug("Check Product ".$this->title);
        $this->dropEmpty();
        $pi = pathinfo($this->image_url);
        $job_id = $this->transaction_id;
        //check product
        $c = DB::table('goods')
            ->where([['sid','=',$this->sid],['supply_id','=',$this->supply_id]])
            ->first();
        if(isset($c->id)){
            $this->_properties["id"]=$c->id;
            DB::table('goods')->where("id","=",$c->id)->update([
                "sku"=>$this->sku,
                "title"=>$this->title,
                "barcode" => $this->barcode,
                "depth" => $this->depth,
                "width" => $this->width,
                "height" => $this->height,
                "weight" => $this->weight,
                "unit" => $this->unit,
                "certificate" => $this->certificate,
                "description" => $this->description,
                "pack"=>$this->pack,
                "price"=>$this->amount,
                "updated_at"=>date("Y-m-d H:i:s")
            ]);
        }
        else{
            //check brand
            $brand = DB::table('brands')->where('title','=',$this->brand)->first();
            $this->_properties["brand_id"] = (!isset($brand->id))?DB::table('brands')->insertGetId(['title'=>$this->brand]):$brand->id;
            $ins = $this->_properties;
            $ins["price"] = $ins["amount"];
            unset($ins["external_category_id"]);
            unset($ins["image_url"]);
            unset($ins["quantity"]);
            unset($ins["amount"]);
            unset($ins["brand"]);
            unset($ins["schedule_id"]);
            unset($ins["dateupdate"]);
            unset($ins["transaction_id"]);

            $this->_properties["id"]=DB::table('goods')->insertGetId($ins);
        }
        //check category
        if($this->external_category_id>0){
            $this->getCatalog();
        }
        $this->_properties["image"] = "S".str_pad($this->id, 10, "0", STR_PAD_LEFT).".".$pi['extension'];
        //echo "CURRENT WORKING DIR \t".getcwd()."\n";
        $imgdir = file_exists("/home/a0124380/domains/a0124380.xsph.ru/public_html/public/img/")?"/home/a0124380/domains/a0124380.xsph.ru/public_html/public/img/":"/Applications/AMPPS/www/it24/public/img/";
        if(!file_exists($imgdir.$this->image))$this->loadImage($this->image_url,$imgdir.$this->image);
        DB::table('goods')->where("id","=",$this->id)->update([
            "image"=>$this->image
        ]);
        //DB::raw('update upload_transactions set total = total+1,summary=summary+'.($this->quantity*$this->amount).' where id ='.$job_id);
        //uploads
        //$status = DB::table('upload_statuses')->where('title','=','done')->first();
        DB::table('uploads')->insert([
            "good_id" => $this->id,
            "transaction_id" => $job_id,
            "quantity" => $this->quantity,
            "amount" => $this->amount
        ]);
    }
    protected function getCatalog(){
        try{
            $p = DB::table('categories')
                ->where([['external_id','=',$this->external_category_id],['supply_id','=',$this->supply_id]])
                ->first();
            $cat_id=$p->id;
            // $ext_id = $p->id;
            // while(empty($cat_id)&&!is_null($ext_id)){
            //     if(isset($p->internal_id)&&!is_null($p->internal_id)) $cat_id=$p->internal_id;
            //     else $ext_id = $p->parent_id;
            //     $p = DB::table('categories')
            //         ->where([['id','=',$ext_id],['supply_id','=',$this->supply_id]])
            //         ->orWhere([['id','=',$ext_id],['supply_id','=',$this->supply_id]])
            //         ->first();
            // }
            if(!empty($cat_id)){
                $exists = DB::table('goods_categories')->where(["good_id"=>$this->id,"category_id"=>$cat_id])->first();
                if(!isset($exists->id))DB::table('goods_categories')->insert(["good_id"=>$this->id,"category_id"=>$cat_id]);
            }
        }catch(\Exception $e){Log::error($e);}
    }
    protected function loadImage($url,$f){
        try{
            $this->save2file($f,$this->fetch($url));
        }
        catch(\Exception $e){
            Log::error($e);
        }

    }
}
