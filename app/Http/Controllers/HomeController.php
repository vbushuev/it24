<?php

namespace App\Http\Controllers;
use Log;
use Mail;
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
        //$user = $rq->user();
        //Log::debug($user->name." role:".$user->role_id);

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
    public function schedules(Request $rq)
    {
        return view('pages.schedules',['panel'=>'schedules',"user"=>$rq->user()]);
    }
    public function catalog(Request $rq)
    {
        $this->authorize('uploads');
        return view('pages.catalog',['panel'=>'catalog',"user"=>$rq->user()]);
    }
    public function profile(Request $rq){
        return view('pages.profile',['panel'=>'profile',"user"=>$rq->user()]);
    }
    public function support(Request $rq){
        $data = ['panel'=>'support',"user"=>$rq->user()];
        $emails = ['yanusdnd@inbox.ru','dev.igroland59@yandex.ru'];
        if($rq->input("title",false)!==false){
            $user = $rq->user();
            try{
                Mail::raw($rq->input('title')."\n".$rq->input('message'),function($m)use($user,$emails){

                    Log::debug($user);
                    //$m->from($user->email,$user->name);
                    foreach($emails as $email){
                        $m->to($email,$email);
                    }
                    $m->subject('Обращение в поддержку от '.$user->email.' '.$user->name);
                });
                $data["message"] = "Ваше сообщение отправлено в службу поддержки.";
                $data["status"] = "success";
            }
            catch(\Exception $e){
                Log::error($e);
                $address = [];
                foreach($emails as $email){
                    $address[]= '<a href="mailto:'.$email.'">'.$email.'</a>';
                }
                $data["message"] = "Ваше сообщение не отправлено в службу поддержки. Просим вас самостоятельно связаться по адресам ".join(", ",$address);
                $data["status"] = "failed";
            }
        }
        return view('pages.support',$data);
    }
}
