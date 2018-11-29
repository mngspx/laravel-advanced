<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Messages;
class socketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $all = DB::table('messages')->select('messages.id','name','id_user','content')->leftJoin('users','users.id','=','messages.id_user')->orderBy('messages.id','asc')->get();
        return view('writemessage',compact('all'));
    }
    public function sendMessage(Request $request){
        Messages::create(['id_user'=>Auth::id(),'content'=>$request->get('message')]);
        $redis = Redis::connection();
        $redis->publish('message', json_encode(['name'=>Auth::user()->name,'content'=>$request->get('message')]));
        return redirect()->route('chat');
    }
}
