<?php
namespace it24;
use DB as DB;
use Log as Log;
use it24\Common as Common;


class Category extends Common{
    public function __construct($a = []){
        $this->loadFromArray($a);
    }
    public function store(){
        //Log::debug("Check category ".$this->title." parent = ".$this->external_parent_id);
        if($this->external_parent_id>0){
            $p = DB::table('categories')
                ->where([['external_id','=',$this->external_parent_id],['supply_id','=',$this->supply_id]])
                ->first();

            $this->_properties["parent_id"] = is_null($p)?null:$p->id;
            //Log::debug("parent_id => ".$this->parent_id." was ".$this->external_parent_id);
        }
        else $this->_properties["parent_id"] = null;
        unset($this->_properties["external_parent_id"]);

        $c = DB::table('categories')->where([['external_id','=',$this->external_id],['supply_id','=',$this->supply_id]])->first();

        //Log::debug($c);

        if(isset($c->id)){
            $this->loadFromArray(["id"=>$c->id]);
            $a = ["title"=>$this->title];
            if($this->parent_id>0)$a["parent_id"]=$this->parent_id;
            DB::table('categories')->where("id","=",$c->id)->update($a);
        }
        else{
            DB::table('categories')->insert($this->_properties);
        }
    }
}
