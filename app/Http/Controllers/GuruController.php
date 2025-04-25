<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Tampilkan semua tugas yang dibuat oleh guru yang sedang login.
     */
    public function getTugas()
    {
        $user = Auth::user();

        $tugas = Tugas::with('kelas')
            ->where('guru_id', $user->id)
            ->get();

        return response()->json([
            'message' => 'Daftar tugas guru',
            'data' => $tugas
        ]);
    }

    /**
     * Buat tugas baru dan kaitkan ke kelas berdasarkan nama.
     */
    public function createTugas(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'kelas_nama' => 'required|array',
            'kelas_nama.*' => 'string'
        ]);

        $guru = Auth::user();

        $kelasIds = Kelas::whereIn('nama', $request->kelas_nama)->pluck('id');

        if ($kelasIds->isEmpty()) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $tugas = Tugas::create([
            'guru_id' => $guru->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline
        ]);

        $tugas->kelas()->sync($kelasIds);

        return response()->json([
            'message' => 'Tugas berhasil dibuat',
            'data' => $tugas->load('kelas')
        ]);
    }

    /**
     * Perbarui tugas milik guru.
     */
    public function updateTugas(Request $request, $id)
    {
        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'deadline' => 'sometimes|required|date',
            'kelas_nama' => 'sometimes|array',
            'kelas_nama.*' => 'string'
        ]);

        $guru = Auth::user();
        $tugas = Tugas::where('id', $id)->where('guru_id', $guru->id)->first();

        if (!$tugas) {
            return response()->json(['message' => 'Tugas tidak ditemukan atau bukan milik Anda'], 404);
        }

        $tugas->update($request->only(['judul', 'deskripsi', 'deadline']));

        if ($request->has('kelas_nama')) {
            $kelasIds = Kelas::whereIn('nama', $request->kelas_nama)->pluck('id');
            $tugas->kelas()->sync($kelasIds);
        }

        return response()->json([
            'message' => 'Tugas berhasil diperbarui',
            'data' => $tugas->load('kelas')
        ]);
    }

    /**
     * Hapus tugas milik guru.
     */
    public function deleteTugas($id)
    {
        $guru = Auth::user();
        $tugas = Tugas::where('id', $id)->where('guru_id', $guru->id)->first();

        if (!$tugas) {
            return response()->json(['message' => 'Tugas tidak ditemukan atau bukan milik Anda'], 404);
        }

        $tugas->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus'
        ]);
    }
}
