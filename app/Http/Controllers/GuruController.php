<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Menampilkan semua tugas yang diberikan oleh guru
     */
    public function getTugas()
    {
        $guru = Auth::user();

        // Ambil tugas-tugas yang dimiliki guru ini, serta kelas yang berhubungan
        $tugas = Tugas::where('guru_id', $guru->id)
                      ->with('kelas') // Memuat relasi kelas
                      ->get();

        return response()->json([
            'message' => 'Berhasil mendapatkan tugas',
            'data' => $tugas
        ]);
    }

    /**
     * Membuat tugas baru untuk guru
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

        // Ambil kelas-kelas yang diajarkan oleh guru
        $kelasIds = $guru->kelas->pluck('id')->toArray(); // Kelas yang diajarkan oleh guru

        // Ambil ID kelas yang dipilih dari request
        $selectedKelasIds = Kelas::whereIn('nama', $request->kelas_nama)->pluck('id');

        // Validasi apakah kelas yang dipilih berhubungan dengan kelas yang diajarkan oleh guru
        if ($selectedKelasIds->diff($kelasIds)->isNotEmpty()) {
            return response()->json(['message' => 'Guru hanya dapat membuat tugas untuk kelas yang diajarkan'], 403);
        }

        // Jika validasi kelas berhasil, buat tugas
        $tugas = Tugas::create([
            'guru_id' => $guru->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline
        ]);

        // Sinkronisasi tugas dengan kelas yang dipilih
        $tugas->kelas()->sync($selectedKelasIds);

        return response()->json([
            'message' => 'Tugas berhasil dibuat',
            'data' => $tugas->load('kelas') // Mengembalikan tugas beserta relasi kelas
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

        // Perbarui tugas berdasarkan input dari request
        $tugas->update($request->only(['judul', 'deskripsi', 'deadline']));

        // Jika kelas diubah, sinkronisasi ulang kelas
        if ($request->has('kelas_nama')) {
            $kelasIds = Kelas::whereIn('nama', $request->kelas_nama)->pluck('id');
            $tugas->kelas()->sync($kelasIds); // Sinkronisasi kelas
        }

        return response()->json([
            'message' => 'Tugas berhasil diperbarui',
            'data' => $tugas->load('kelas') // Kembalikan tugas beserta relasi kelas
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
