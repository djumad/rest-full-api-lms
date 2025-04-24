<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAllGuru(){
        $guru = User::where('role' , 'guru')->get();
        return response()->json([
            'data' => $guru
        ] , 200);
    }
    public function getAllSiswa(){
        $siswa = User::where('role' , 'siswa')->get();
        return response()->json([
            'data' => $siswa
        ] , 200);
    }

    public function createUser(CreateUserRequest $request){
        $data = $request->validated();
        $cekUser = User::where('email' , $data['email'])->count();
        if($cekUser){
            return response()->json([
                'message' => 'email sudah terdaftar'
            ] , 401);
        }
        $cekNomorIdentitas = User::where('nomor_identitas' , $data['nomor_identitas'])->count();
        if($cekNomorIdentitas){
            return response()->json([
                'message' => 'Nomor identitas sudah terdaftar'
            ] , 401);
        }

        $user = new User($data);
        $user->save();
        return response()->json([
            'message' => "success create {$user->role}",
            'data' => [
                'nama' => $user->nama,
                'email' => $user->email,
            ]
        ], 200);
    }
}
