<?php

namespace App\Http\Controllers;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('uploads');
        return view('pages.uploads',['panel'=>'uploads']);
    }
    public function goods(Request $rq)
    {
        $user = $rq->user();
        Log::debug($user->name." role:".$user->role_id);

        return view('pages.goods',['panel'=>'goods']);
    }
    public function suppliers()
    {
        $this->authorize('uploads');
        return view('pages.suppliers',['panel'=>'suppliers']);
    }
    public function users()
    {
        $this->authorize('uploads');
        return view('pages.users',['panel'=>'users']);
    }
    public function downloads()
    {
        return view('pages.downloads',['panel'=>'downloads']);
    }
    public function schedules()
    {
        return view('pages.schedules',['panel'=>'schedules']);
    }
    public function catalog()
    {
        $this->authorize('uploads');
        return view('pages.catalog',['panel'=>'catalog']);
    }
}
