<?php
namespace it24;
use DB;
use Log;
class Exporter{
    protected $catalogs=[];
    protected $products=[];
    protected $xmla=[];
    public function __construct(){}
    protected function get($c=[],$p=[]){
        $db = DB::table('catalogs');
        if(count($c))$db=$db->whereIn('id',$c);
        $this->catalogs = $db->get();
        $db = DB::table('goods')->leftJoin('goods_catalogs','goods.id','=','goods_catalogs.good_id')->join('brands','brands.id','=','goods.brand_id')
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
                'goods_catalogs.catalog_id',
                'brands.title as brand',
                DB::raw('(select quantity from uploads where good_id = goods.id order by timestamp desc limit 1) as quantity')
            );
        if(count($p))$db=$db->whereIn('goods.id',$p);
        if(count($c)){
            $ct = [];
            foreach ($c as $k) {
                $ct[]=$k;
                $this->recursiveCatalogs($k,$ct);
            }
            $db=$db->whereIn('goods_catalogs.catalog_id',$ct);
        }
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
                        "@value"=>$product->price
                    ],
                    [
                        "@attributes"=>["name"=>"brand"],
                        "@value"=>$product->brand
                    ],
                    [
                        "@attributes"=>["name"=>"barcode"],
                        "@value"=>$product->barcode
                    ],
                    [
                        "@attributes"=>["name"=>"width"],
                        "@value"=>$product->width
                    ],
                    [
                        "@attributes"=>["name"=>"height"],
                        "@value"=>$product->height
                    ],
                    [
                        "@attributes"=>["name"=>"depth"],
                        "@value"=>$product->depth
                    ],
                    [
                        "@attributes"=>["name"=>"unit"],
                        "@value"=>$product->unit
                    ],
                    [
                        "@attributes"=>["name"=>"weight"],
                        "@value"=>$product->weight
                    ],
                    [
                        "@attributes"=>["name"=>"box"],
                        "@value"=>$product->pack
                    ],
                    [
                        "@attributes"=>["name"=>"quantity"],
                        "@value"=>$product->quantity
                    ]/**/
                ]
            ];
        }
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
