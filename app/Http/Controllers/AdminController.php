<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAllGuru()
    {
        $guru = User::where('role', 'guru')->get();
        return response()->json([
            'data' => $guru
        ], 200);
    }
    public function getAllSiswa()
    {
        $siswa = User::where('role', 'siswa')->get();
        return response()->json([
            'data' => $siswa
        ], 200);
    }

    public function createUser(CreateUserRequest $request)
    {
        $data = $request->validated();
        $cekUser = User::where('email', $data['email'])->count();
        if ($cekUser) {
            return response()->json([
                'message' => 'email sudah terdaftar'
            ], 401);
        }
    
        $cekNomorIdentitas = User::where('nomor_identitas', $data['nomor_identitas'])->count();
        if ($cekNomorIdentitas) {
            return response()->json([
                'message' => 'Nomor identitas sudah terdaftar'
            ], 401);
        }
    
        $kelasData = $data['kelas']; // ambil kelas dari request
        unset($data['kelas']);       // hapus supaya tidak disimpan ke tabel `users`
    
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
    
        $kelasIds = [];
        foreach ($kelasData as $namaKelas) {
            $kelas = Kelas::firstOrCreate(['nama' => $namaKelas]);
            $kelasIds[] = $kelas->id; // pakai array [] bukan overwrite
        }
    
        $user->kelas()->attach($kelasIds);
    
        return response()->json([
            'message' => "success create {$user->role}",
            'data' => [
                'nama' => $user->nama,
                'email' => $user->email,
                'kelas' => $user->kelas()->pluck('nama'),
            ]
        ], 200);
    }
    

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|min:6',
            'nomor_identitas' => 'sometimes|required|unique:users,nomor_identitas,' . $id,
            'role' => 'sometimes|required|string',
            'kelas' => 'sometimes|required|array',
            'kelas.*' => 'string|max:255'
        ]);

        $data = $request->all();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Update relasi kelas jika ada
        if (isset($data['kelas'])) {
            $kelasIds = [];
            foreach ($data['kelas'] as $namaKelas) {
                $kelas = Kelas::firstOrCreate(['nama' => $namaKelas]);
                $kelasIds[] = $kelas->id;
            }
            $user->kelas()->sync($kelasIds); // sync = hapus relasi lama, tambah yang baru
        }

        return response()->json([
            'message' => 'Berhasil update user',
            'data' => [
                'nama' => $user->nama,
                'email' => $user->email,
                'kelas' => $user->kelas()->pluck('nama'),
            ]
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Hapus relasi dengan kelas dulu (opsional karena pakai cascade, tapi aman lebih jelas)
        $user->kelas()->detach();

        // Hapus user
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
    
}
