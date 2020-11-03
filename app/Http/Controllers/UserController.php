<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function profile()
    {
        return view('userInfo');
    }

    // 上传图片到服务器 可优化为第三方存储
    public function upload(Request $request)
    {
        $path = $request->file('file')->store('images');
        Log::info($path);
        return $path;
    }

    // 保存用户提交的信息
    public function store(Request $request)
    {
        $user = auth('web')->user();
        $user['avatar'] = $request->input('avatar');
        $user['name'] = $request->input('name');
        $user->save();

        return response_success();
    }

    public function userInfo()
    {
        $user = auth('web')->user();
        return response_success($user);
    }

    public function redis()
    {
        $user = auth('web')->user();

        $redis = Redis::connection();
        $redis->set('user:' . $user['id'], json_encode($user));
        return $redis->get('user:' . $user['id']);
    }

    public function array()
    {
        $arr = [1,2,3];

        $arr = collect($arr)->map(function ($items, $key) {
            return $items * 2;
        });

        return $arr;
    }




}
