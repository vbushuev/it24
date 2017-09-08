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
                <url>http://utoy.ru/catalogs/in_stock/zhivotnye_na_r_u-18097/pingvin_na_r_u_27_5sm_kor_2051ru/</url>
                <price>769.73</price>
                <currencyId>RUR</currencyId>
                <categoryId>18097</categoryId>
                <picture>http://utoy.ru/upload/iblock/b3b/64cd97ab-d74c-11e2-bf01-005056987a0b.resize1.jpeg</picture>
                <name>Пингвин на Р/У 27,5см. кор. 2051RU</name>
                <description/>
                <quantity>-5</quantity>
                <param name="Бренд">Китай</param>
                <param name="Производитель"/>
                <param name="Напряжение"/>
                <param name="Количество в коробке"/>
                <param name="Материал"/>
                <param name="Высота, см"/>
                <param name="Длина, см"/>
                <param name="Размер"/>
                <param name="Упаковка"/>
                <param name="Высота упаковки, см"/>
                <param name="Остаток в пути">0</param>
                <param name="Количество в коробке">24</param>
                <param name="Упаковка"/>
                <param name="Размер упаковки"/>
                <param name="Длина упаковки, см"/>
                <param name="ШтрихКод">2031862030001</param>
                <param name="Артикул">T46-D173</param>
                <param name="Производитель"/>
                <param name="Химический состав"/>
                <param name="Ширина упаковки, см"/>
                <param name="Ширина, см"/>
                <param name="Диаметр"/>
                <param name="КодНоменклатуры">Н186203</param>
                <param name="Количество деталей"/>
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
                "quantity"=>$item->quantity->__toString(),
                "dateupdate"=>date("Y-m-d H:i:s"),
                "external_category_id"=>$item->categoryId->__toString(),
                "title"=>$item->name->__toString(),
                "image_url"=>$item->picture->__toString(),
                "amount"=>$item->price->__toString(),
                "pack"=>"",
                "brand"=>"",
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
                if($param['name'] == 'Количество в коробке')$a["pack"]=$param->__toString();
                else if($param['name'] == 'Бренд')$a["brand"]=$param->__toString();
                else if($param['name'] == 'ШтрихКод')$a["barcode"]=$param->__toString();
                else if($param['name'] == 'Длина, см')$a["depth"]=$param->__toString();
                else if($param['name'] == 'Ширина, см')$a["width"]=$param->__toString();
                else if($param['name'] == 'Высота, см')$a["height"]=$param->__toString();
                else if($param['name'] == 'Артикул')$a["sku"]=$param->__toString();
            }
            $a["quantity"]=(intval($a["quantity"])<0)?-intval($a["quantity"]):intval($a["quantity"]);
            $o = new Product($a,$job->price_add);
            $o->store();
            file_put_contents($_doneFile,json_encode($_done));
        }
        unlink($_doneFile);
        return $total;
    }
}
