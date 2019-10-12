<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use GatewayClient\Gateway;
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

        Gateway::$registerAddress = '127.0.0.1:1238';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $room_id = $request->input('room_id') ? $request->input('room_id') : 1;
        session()->put('room_id', $room_id);

        return view('home');
    }

    public function init(Request $request)
    {
        $this->bind($request);
        $this->users();
        $this->history();
        $this->login();
        $this->ping();
    }

    //绑定前端传过来的client_id和user_id
    private function bind($req)
    {
        $id = Auth::id();
        $client_id = $req->client_id;

        Gateway::bindUid($client_id, $id);
        //当前用户进入一个房间
        Gateway::joinGroup($client_id, session('room_id'));
        Gateway::setSession($client_id, [
            'id' => Auth::user()->id,
            'avatar' => Auth::user()->avatar(),
            'name' => Auth::user()->name,
        ]);
    }

    //user就是当前登录用户的对象模型
    public function login()
    {
        $data = [
            'type' => 'login',
            'data' => [
                'avatar' => Auth::user()->avatar(),
                'name' => Auth::user()->name,
                'content' => '进入了这个聊天室',
                'time' => date("Y-m-d H:i:s", time())
            ],
        ];

        Gateway::sendToGroup(session('room_id'), json_encode($data));
    }

    public function say(Request $request)
    {
        $data = [
            'type' => 'say',
            'data' => [
                'avatar' => Auth::user()->avatar(),
                'name' => Auth::user()->name,
                'content' => $request->input('content'),
                'time' => date("Y-m-d H:i:s", time()),
            ],
        ];

        //私聊
        if ($request->input('user_id')) {
            $data['data']['name'] = Auth::user()->name . '对' . User::query()->find($request->input('user_id'))->name . '说:';
            Gateway::sendToUid(Auth::id(), json_encode($data));

            $data['data']['name'] = Auth::user()->name . '对你说:';
            Gateway::sendToUid($request->input('user_id'), json_encode($data));

            return;
        }

        Gateway::sendToGroup(session('room_id'), json_encode($data));

        Message::query()->create([
            'user_id' => Auth::id(),
            'room_id' => session('room_id'),
            'content' => $request->input('content'),
        ]);
    }

    //测试websocket连接
    public function ping()
    {
        $data = [
            'type' => 'ping',
        ];

        Gateway::sendToAll(json_encode($data));
    }

    public function history()
    {
        $data = [
            'type' => 'history',
        ];

        $messages = Message::query()->with(['user'])->where('room_id', session('room_id'))->orderBy('id', 'desc')->limit(5)->get();

        //map重新组装数据返回
        $data['data'] = $messages->map(function ($item, $key) {
            return [
                'avatar' => $item->user->avatar(),
                'name' => $item->user->name,
                'content' => $item->content,
                'time' => $item->created_at->format("Y-m-d H:i:s"),
            ];
        });

        Gateway::sendToUid(Auth::id(), json_encode($data));
    }

    public function users()
    {
        $session = Gateway::getAllClientSessions();

//        $value = [];
//
//        foreach ($session as $s) {
//            $value[] = $s;
//        }
//
//        //针对同一浏览器登录出现多个相同用户
//        $users = array_unique($value, SORT_REGULAR);

        $data = [
            'type' => 'users',
            'data' => $session,
        ];

        Gateway::sendToGroup(session('room_id'), json_encode($data));

        //获取当前房间在线的用户
        $uids = Gateway::getUidListByGroup(session('room_id'));

        $users = [];

        foreach ($uids as $uid) {
            $user = User::query()->find($uid);
            $user['avatar'] = $user->avatar();
            $users[] = $user;
        }

        $session_data = [
            'type' => 'users',
            'data' => $users,
        ];

        Gateway::sendToUid(Auth::id(), json_encode($session_data));
    }

    public function usersByGroup()
    {
        $uids = Gateway::getUidListByGroup(session('room_id'));
        $users = [];

        foreach ($uids as $uid) {
            $user = User::query()->find($uid);
            $user['avatar'] = $user->avatar();
            $users[] = $user;
        }

        $data = [
            'type' => 'users',
            'data' => $users,
        ];

    }



}
