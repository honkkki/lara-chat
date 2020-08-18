<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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



}
