<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\TugasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    // Mendapatkan daftar tugas untuk siswa
    public function getTugas()
    {
        $user = Auth::user();
        $kelasIds = $user->kelas->pluck('id')->toArray();

        $tugas = Tugas::whereHas('kelas', function ($query) use ($kelasIds) {
            $query->whereIn('kelas.id', $kelasIds);
        })->with('kelas')->get();

        return response()->json([
            'message' => 'Tugas berhasil diambil.',
            'data' => $tugas
        ], 200);
    }

    // Mengumpulkan tugas baru
    public function kumpulkanTugas(Request $request, $tugasId)
    {
        $request->validate([
            'file_unggahan' => 'required|file|mimes:pdf,docx,zip,jpg,png|max:2048',
        ]);

        $user = Auth::user();
        $folderPath = 'tugas_siswa/' . $user->id . '/' . $tugasId;

        // Hapus file lama jika ada
        $existingTugas = TugasSiswa::where('siswa_id', $user->id)
                                ->where('tugas_id', $tugasId)
                                ->first();
        
        if ($existingTugas && $existingTugas->file_unggahan) {
            Storage::disk('public')->delete($existingTugas->file_unggahan);
        }

        // Simpan file baru
        $file = $request->file('file_unggahan');
        $filePath = $file->store($folderPath, 'public');

        // Simpan atau update data tugas siswa
        $tugasSiswa = TugasSiswa::updateOrCreate(
            ['siswa_id' => $user->id, 'tugas_id' => $tugasId],
            ['file_unggahan' => $filePath]
        );

        return response()->json([
            'message' => 'Tugas berhasil dikumpulkan.',
            'data' => $tugasSiswa
        ], 201);
    }

    // Mengupdate tugas yang sudah dikumpulkan
    public function updateTugas(Request $request, $tugasId)
    {
        $request->validate([
            'file_unggahan' => 'required|file|mimes:pdf,docx,zip,jpg,png|max:2048',
        ]);

        $user = Auth::user();
        $tugasSiswa = TugasSiswa::where('siswa_id', $user->id)
                            ->where('tugas_id', $tugasId)
                            ->firstOrFail();

        // Hapus file lama
        if ($tugasSiswa->file_unggahan) {
            Storage::disk('public')->delete($tugasSiswa->file_unggahan);
        }

        // Simpan file baru
        $folderPath = 'tugas_siswa/' . $user->id . '/' . $tugasId;
        $file = $request->file('file_unggahan');
        $filePath = $file->store($folderPath, 'public');

        // Update data tugas siswa
        $tugasSiswa->update(['file_unggahan' => $filePath]);

        return response()->json([
            'message' => 'Tugas berhasil diperbarui.',
            'data' => $tugasSiswa
        ], 200);
    }

    // Menghapus tugas yang sudah dikumpulkan
    public function deleteTugas($tugasId)
    {
        $user = Auth::user();
        $tugasSiswa = TugasSiswa::where('siswa_id', $user->id)
                            ->where('tugas_id', $tugasId)
                            ->firstOrFail();

        // Hapus file dari storage
        if ($tugasSiswa->file_unggahan) {
            Storage::disk('public')->delete($tugasSiswa->file_unggahan);
        }

        // Hapus record dari database
        $tugasSiswa->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus.'
        ], 200);
    }

    // Melihat tugas yang sudah dikumpulkan
    public function lihatTugasTerkumpul($tugasId)
    {
        $user = Auth::user();

        $tugasSiswa = TugasSiswa::where('tugas_id', $tugasId)
            ->where('siswa_id', $user->id)
            ->first();

        if (!$tugasSiswa) {
            return response()->json(['message' => 'Tugas belum dikumpulkan.'], 404);
        }

        return response()->json([
            'message' => 'Tugas ditemukan.',
            'data' => [
                'tugas' => $tugasSiswa,
                'file_url' => Storage::url($tugasSiswa->file_unggahan)
            ]
        ]);
    }

    // Melihat detail tugas tertentu
    public function detailTugas($id)
    {
        $tugas = Tugas::with('kelas')->findOrFail($id);

        // Cek apakah tugas sudah dikumpulkan
        $user = Auth::user();
        $tugasSiswa = TugasSiswa::where('tugas_id', $id)
                            ->where('siswa_id', $user->id)
                            ->first();

        $response = [
            'message' => 'Detail tugas ditemukan.',
            'tugas' => $tugas,
            'status_pengumpulan' => $tugasSiswa ? 'Sudah dikumpulkan' : 'Belum dikumpulkan'
        ];

        if ($tugasSiswa) {
            $response['file_url'] = Storage::url($tugasSiswa->file_unggahan);
            $response['tugas_siswa'] = $tugasSiswa;
        }

        return response()->json($response);
    }
}