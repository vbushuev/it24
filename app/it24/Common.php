<?php
namespace it24;
class Common{
    protected $_properties = [];
    public function loadFromJson($s){
        $d = json_decode($s,true);
        $this->loadFromArray($d);
    }
    public function loadFromArray($d){
        if(is_array($d))$this->_properties=array_merge($this->_properties,$d);
    }
    public function dropEmpty(){
        foreach ($this->_properties as $key => $value) {
            if(empty($value)||is_null($value))unset($this->_properties[$key]);
        }
    }
    public function __get($n){
        return isset($this->_properties[$n])?$this->_properties[$n]:false;
    }
    public function __set($n,$v){
        $this->_properties[$n]=$v;
    }
    public function __toString(){
        return json_encode($this->toArray(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
    public function toArray(){
        $r = [];
        foreach($this->_properties as $k=>$v){
            if(false && is_object($v)&&($v instanceof Common)) $r[$k] = $v->toArray();
            //elseif(is_array($v)) $r[$k] = $v;
            else $r[$k] = $v;
        }
        return $this->_properties;
    }
    public function fetch($url){
        return file_get_contents($url);
    }
    public function save2file($f,$d){
        file_put_contents($f,$d);
    }

};
?>
