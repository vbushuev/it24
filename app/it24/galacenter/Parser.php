<?php
namespace it24\galacenter;
use DB as DB;
use Log as Log;
use it24\Common as Common;
use it24\Product as Product;
use it24\Category as Category;

class Parser extends Common{
    public function __construct($x=null){
        $this->parse($x);
    }
    public function parse($xml=null,$j=[]){
        if(is_null($xml)||!isset($j["job_id"])||!isset($j["job"]))return;
        $_doneFile='gl-p'.date("Y-m-d").".json";
        $_done=(file_exists($_doneFile))?json_decode(file_get_contents($_doneFile),true):[];
        $job_id = $j["job_id"];
        $job = $j["job"];
        $cats = $xml->xml_catalog->shop->categories->category;
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
        $prods = isset($xml->xml_catalog)?$xml->xml_catalog->shop->offers->offer:$xml->shop->offers->offer;
        if(!isset($prods)&&!is_null($prods)){
            Log::error("No data in ".$job->title." ".$job->link);
            throw new \Exception("no-products",2);
        }
        //print_r($prods);
        $total=0;
        foreach ($prods as $item) {
            $total++;
            if(isset($_done[$item["id"]->__toString()]))continue;
            $_done[$item["id"]->__toString()]=1;
            $a = [
                "transaction_id"=>$job_id,
                "supply_id"=>$job->supply_id,
                "sid"=>$item["id"]->__toString(),
                "sku"=>$item["articul"]->__toString(),
                "quantity"=>"0",
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
                else if($param['name'] == 'store_ekb')$a["quantity"]=$param->__toString();
            }
            $a["quantity"] = (($a["quantity"]==2)?3:1)*$a["pack"];
            $o = new Product($a,$job->price_add);
            $o->store();
            file_put_contents($_doneFile,json_encode($_done));
        }
        unlink($_doneFile);
        return $total;
    }
}
