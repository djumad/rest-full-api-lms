<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(UserRequest $request){
        $data = $request->validated();

        $user = User::where('email' , $data['email'])->first();
        if(!$user){
            return response()->json([
                'message' => 'email dan password salah'
            ] , 401);
        }

        $cekPassword = Hash::check($data['password'] , $user->password);

        if(!$cekPassword){
            return response()->json([
                'message' => 'email dan password salah'
            ] , 401);
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            'message' => 'success',
            'data' => [
                'email' => $user->email,
                'nama' => $user->nama,
                'role' => $user->role,
                'token' => $user->token
            ]
        ], 200);
    }

    public function getUser(){
        $user = Auth::user();

        return response()->json([
            'message' => 'success',
            'data' => [
                'email' => $user->email,
                'nama' => $user->nama,
                'role' => $user->role,
                'token' => $user->token
            ]
        ] , 200);
    }
}
