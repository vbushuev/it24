<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DB;

class WebController extends Controller{
    public function __construct(){
        //$this->middleware('auth');
    }
    public function index(Request $rq){
        return view('layouts.main');
    }
}
