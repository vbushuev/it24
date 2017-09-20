<?php
namespace it24\rcvostok;
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
        $_doneFile='ut-p'.date("Y-m-d").".json";
        $_done=(file_exists($_doneFile))?json_decode(file_get_contents($_doneFile),true):[];
        $job_id = $j["job_id"];
        $job = $j["job"];
        $cats = $xml->shop->categories->category;
        if(!isset($cats)&&!is_null($cats)){
            Log::error("No data in ".$job->title." ".$job->link);
            throw new \Exception("no-categories",2);
        }
        foreach($cats as $category){
            // print_r($category);exit;
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
            /*
            <offer id="1675250" available="true">
                <categoryId>4281</categoryId>
                <picture>http://static.rcvostok.ru/images/product/1/b/1/1b165244-fae5-11e5-9226-0cc47a018b6a/600x600_5e301415-9d0e-11e6-80ca-2c59e542282b.jpg</picture>
                <name>Карнавальный аксессуар &quot;Крылья Фея Бабочка&quot; желтый</name>
                <param name="price">118.15</param>
                <param name="box">240</param>
                <param name="brand">Серпантин</param>
                <param name="quantity">68</param>
                <param name="barcode">6931993616197</param>
                <param name="material">пвх, пластик, текстиль</param>
            </offer>
            */
            $total++;
            if(isset($_done[$item["id"]->__toString()]))continue;
            $_done[$item["id"]->__toString()]=1;
            $a = [
                "transaction_id"=>$job_id,
                "supply_id"=>$job->supply_id,
                "sid"=>$item["id"]->__toString(),
                "sku"=>"",
                "quantity"=>"",
                "dateupdate"=>date("Y-m-d H:i:s"),
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
                if($param['name'] == 'box')$a["pack"]=$param->__toString();
                else if($param['name'] == 'brand')$a["brand"]=$param->__toString();
                else if($param['name'] == 'barcode')$a["barcode"]=$param->__toString();
                else if($param['name'] == 'price')$a["amount"]=$param->__toString();
                else if($param['name'] == 'quantity')$a["quantity"]=$param->__toString();
                else if($param['name'] == 'material')$a["description"]=$param->__toString();
                // else if($param['name'] == 'Артикул')$a["sku"]=$param->__toString();
            }
            $a["quantity"]=(intval($a["quantity"])<0)?-intval($a["quantity"]):intval($a["quantity"]);
            print_r($a);
            $o = new Product($a,$job->price_add);
            $o->store();
            file_put_contents($_doneFile,json_encode($_done));
        }
        unlink($_doneFile);
        return $total;
    }
}
