<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id,416);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功！');
    }
    public function message()
    {
        return[
            'name.unique' =>'用户名已被占用,请重新填写',
            'name.regex' =>'用户名只支持英文、数字、横杠和下划线.',
            'name.between' =>'用户名必须介于3 - 25个字符之间。',
            'name.required'=>'用户名不能为空。',
        ];
    }
}
