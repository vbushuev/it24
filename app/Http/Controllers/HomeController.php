<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.uploads',['panel'=>'uploads']);
    }
    public function goods()
    {
        return view('pages.goods',['panel'=>'goods']);
    }
    public function suppliers()
    {
        return view('pages.suppliers',['panel'=>'suppliers']);
    }
    public function users()
    {
        return view('pages.users',['panel'=>'users']);
    }
    public function downloads()
    {
        return view('pages.downloads',['panel'=>'downloads']);
    }
}
