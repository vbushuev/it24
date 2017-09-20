<?php
namespace it24\ostkom;
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
            <offer id="78752" type="vendor.model" available="true" articul="MAR1108-001">
        		<url>http://toys.ost-com.ru/products/70923/78752</url>
        		<categoryId>70923</categoryId>
        		<pickup>true</pickup>
        		<delivery>true</delivery>
        		<name>Игровой набор Mioshi Army "Лучник: охотник" (лук с лазер прицелом, 3 стрелы, 1 колчан)</name>
        		<vendor>Mioshi</vendor>
        		<param name="brand">Mioshi</param>
        		<param name="code1c">00000078752</param>
        		<param name="articul">MAR1108-001</param>
        		<param name="barcode">EAN13   4680213034939</param>
        		<param name="Возраст">от 8 лет</param>
        		<param name="Пол">для всех</param>
        		<param name="Материал">PVC</param>
        		<param name="Сезонность">Всесезонный</param>
        		<param name="Страна">КИТАЙ</param>
        		<picture>http://toys.ost-com.ru/images/quickcatalog/78752/b/bow-and-arrow-upload-on-internet-4.jpg</picture>
        		<picture>http://toys.ost-com.ru/images/quickcatalog/78752/b/9822-7-s.jpg</picture>
        		<picture>http://toys.ost-com.ru/images/quickcatalog/78752/b/bow-and-arrow-upload-on-internet-1.jpg</picture>
        		<picture>http://toys.ost-com.ru/images/quickcatalog/78752/b/bow-and-arrow-upload-on-internet-2.jpg</picture>
        		<param name="box">12</param>
        		<param name="Вес нетто 1 шт., кг">1</param>
        		<param name="Вес брутто 1 шт., кг">1</param>
        		<param name="lenght, cm">100</param>
        		<param name="width, cm">31</param>
        		<param name="height, cm">5</param>
        		<param name="size (l x w x h), cm">100 x 31 x 5</param>
        		<description>&lt;p&gt;&amp;nbsp;&lt;/p&gt;
        &lt;p&gt;Набор Mioshi Army "Лучник: следопыт" - это игровое оружие для самых смелых воинов. Стреляет стрелами с мягкими присосками, а лазерный прицел поможет точно попасть в цель. Можно играть целой компанией, устроить соревнование и стрелять по мишени.&lt;/p&gt;
        &lt;p&gt;&amp;nbsp;&lt;/p&gt;
        &lt;p&gt;В комплекте:&lt;/p&gt;
        &lt;p&gt;Лук&lt;/p&gt;
        &lt;p&gt;3 стрелы с присосками&lt;/p&gt;
        &lt;p&gt;Колчан&lt;/p&gt;
        </description>
        		<quantity>9</quantity>
        		<price>991</price>
        		<currencyId>RUR</currencyId>
        	</offer>
            */
            $total++;
            $pictures = $item->picture;
            $picture = (is_array($pictures))?$pictures[0]->__toString():$pictures->__toString();
            if(isset($_done[$item["id"]->__toString()]))continue;
            $_done[$item["id"]->__toString()]=1;
            $a = [
                "transaction_id"=>$job_id,
                "supply_id"=>$job->supply_id,
                "sid"=>$item["id"]->__toString(),
                "sku"=>$item["articul"]->__toString(),
                "quantity"=>$item->quantity->__toString(),
                "dateupdate"=>date("Y-m-d H:i:s"),
                "external_category_id"=>$item->categoryId->__toString(),
                "title"=>$item->name->__toString(),
                "image_url"=>$picture,
                "amount"=>$item->price->__toString(),
                "pack"=>"",
                "brand"=>$item->vendor->__toString(),
                "barcode"=>"",
                "depth"=>"",
                "width"=>"",
                "height"=>"",
                "weight"=>"",
                "unit"=>"",
                "certificate"=>"",
                "description"=>$item->description->__toString()
            ];
            foreach($item->param as $param){
                if($param['name'] == 'box')$a["pack"]=$param->__toString();
                // else if($param['name'] == 'Бренд')$a["brand"]=$param->__toString();
                else if($param['name'] == 'barcode')$a["barcode"]=preg_replace('/\S*\s*(\S+)/i','$1',$param->__toString());
                else if($param['name'] == 'Вес брутто 1 шт., кг')$a["weight"]=$param->__toString();
                else if($param['name'] == 'lenght, cm')$a["depth"]=$param->__toString();
                else if($param['name'] == 'width, cm')$a["width"]=$param->__toString();
                else if($param['name'] == 'height, cm')$a["height"]=$param->__toString();
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
