<?php
namespace it24;
use DB;
use Log;
use App\GoodAdds;
use App\Supplier;
class Exporter{
    protected $catalogs=[];
    protected $products=[];
    protected $xmla=[];
    protected $job=0;
    public function __construct($job){
        $this->job = $job;
    }
    protected function get($c=[],$p=[]){
        $db = DB::table('catalogs');
        if(!empty($c) && !is_null($c) && strtolower($c)!="null"){
            if(!is_array($c))$c=preg_split("/,/m",$c);
            $db=$db->whereIn('id',$c);
        }
        $this->catalogs = $db->get();
        $db = DB::table('goods')
            ->join('goods_categories','goods.id','=','goods_categories.good_id')
            ->join('categories','categories.id','=','goods_categories.category_id')
            ->join('brands','brands.id','=','goods.brand_id')
            ->select('goods.id',
                'goods.image',
                DB::raw("ifnull(goods.sku,'') as sku"),
                DB::raw("ifnull(goods.title,'') as title"),
                DB::raw("ifnull(goods.barcode,'') as barcode"),
                DB::raw("ifnull(goods.width,'') as width"),
                DB::raw("ifnull(goods.height,'') as height"),
                DB::raw("ifnull(goods.weight,'') as weight"),
                DB::raw("ifnull(goods.depth,'') as depth"),
                DB::raw("ifnull(goods.unit,'') as unit"),
                DB::raw("ifnull(goods.certificate,'') as certificate"),
                DB::raw("ifnull(goods.description,'') as description"),
                DB::raw("ifnull(goods.pack,'') as pack"),
                DB::raw("ifnull(goods.price,'') as price"),
                DB::raw("ifnull(goods.updated_at,'') as updated_at"),
                'categories.internal_id as catalog_id',
                'brands.title as brand',
                DB::raw('(select quantity from uploads where good_id = goods.id order by timestamp desc limit 1) as quantity')
            );
        if(!empty($p) && !is_null($p) && strtolower($p)!="null"){
            if(!is_array($p))$p=preg_split("/,/m",$p);
            $db=$db->whereIn('goods.id',$p);
        }
        if(count($c)){
            $ct = [];
            foreach ($c as $k) {
                $ct[]=$k;
                $this->recursiveCatalogs($k,$ct);
            }
            $db=$db->whereIn('categories.internal_id',$ct);
        }
        Log::debug($db->toSQL());
        $this->products = $db->get();
    }
    public function xml($c=[],$p=[]){
        $this->get($c,$p);
        $this->xmla = [
            "@attributes"=>["date"=>date("Y-m-d H:i:s")],
            "shop"=>[
                "name" => config('app.name'),
                "url" => config('app.url')
            ],
            "categories"=>[
                "category"=>[]
            ],
            "offers"=>[
                "offer"=>[]
            ]
        ];
        Log::debug("Catalog count:".count($this->catalogs));
        Log::debug("Product count:".count($this->products));
        foreach($this->catalogs as $catalog){
            $this->xmla["categories"]["category"][] = [
                "@attributes" => [
                    "id" => $catalog->id,
                    "parentId" => $catalog->parent_id
                ],
                "@value"=>$catalog->title
            ];
        }
        foreach($this->products as $product){
            $gadds = GoodAdds::where('good_id',$product->id)->where('user_id',$this->job->user_id)->first();
            $productPrice = (!is_null($gadds) || isset($gadds->id))
                ?$productPrice = is_null($product->price)?"":($product->price+$product->price*($gadds->price_add/100))
                :is_null($product->price)?"":($product->price+$product->price*($this->job->price_add/100));
            $this->xmla["offers"]["offer"][] = [
                "@attributes" => [
                    "id" => $product->id,
                    "articul" => $product->sku,
                    "dateupdate" =>$product->updated_at
                ],
                "categoryId" => $product->catalog_id,
                "name" => $product->title,
                "picture" => config('app.url')."/img/".$product->image,
                "param" =>[
                    [
                        "@attributes"=>["name"=>"price"],
                        "@value"=>$productPrice
                    ],
                    [
                        "@attributes"=>["name"=>"brand"],
                        "@value"=>is_null($product->brand)?"":$product->brand
                    ],
                    [
                        "@attributes"=>["name"=>"barcode"],
                        "@value"=>is_null($product->barcode)?"":$product->barcode
                    ],
                    [
                        "@attributes"=>["name"=>"width"],
                        "@value"=>is_null($product->width)?"":$product->width
                    ],
                    [
                        "@attributes"=>["name"=>"height"],
                        "@value"=>is_null($product->height)?"":$product->height
                    ],
                    [
                        "@attributes"=>["name"=>"depth"],
                        "@value"=>is_null($product->depth)?"":$product->depth
                    ],
                    [
                        "@attributes"=>["name"=>"unit"],
                        "@value"=>is_null($product->unit)?"":$product->unit
                    ],
                    [
                        "@attributes"=>["name"=>"weight"],
                        "@value"=>is_null($product->weight)?"":$product->weight
                    ],
                    [
                        "@attributes"=>["name"=>"box"],
                        "@value"=>is_null($product->pack)?"":$product->pack
                    ],
                    [
                        "@attributes"=>["name"=>"quantity"],
                        "@value"=>is_null($product->quantity)?"":$product->quantity
                    ]/**/
                ]
            ];
        }
        //file_put_contents('xml_expa.json',json_encode($this->xmla,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        $xml = Array2XML::createXML('xml_catalog', $this->xmla);
        return $xml->saveXML();
        /*
        $books = array(
            '@attributes' => array(
                'type' => 'fiction'
            ),
            'book' => array(
                array(
                    '@attributes' => array(
                        'author' => 'George Orwell'
                    ),
                    'title' => '1984'
                ),
                array(
                    '@attributes' => array(
                        'author' => 'Isaac Asimov'
                    ),
                    'title' => array('@cdata'=>'Foundation'),
                    'price' => '$15.61'
                ),
                array(
                    '@attributes' => array(
                        'author' => 'Robert A Heinlein'
                    ),
                    'title' =>  array('@cdata'=>'Stranger in a Strange Land'),
                    'price' => array(
                        '@attributes' => array(
                            'discount' => '10%'
                        ),
                        '@value' => '$18.00'
                    )
                )
            )
        );
        */
    }
    protected function recursiveCatalogs($id,&$res){
        if(empty($id)||is_null($id))return [];
        $rows = DB::table("catalogs")->orderBy('parent_id','asc')->where("parent_id",'=',$id)->get();
        foreach ($rows as $r) {
            $res[]=$r->id;
            $this->recursiveCatalogs($r->id,$res);
        }
        return;
    }
};
?>
