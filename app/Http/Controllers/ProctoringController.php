<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProctoringLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProctoringController extends Controller
{
    public function storeSnapshot(Request $request)
    {
        // Validasi data dari request AJAX JavaScript
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'image'           => 'required|string' // Format base64
        ]);

        $base64Image = $request->input('image');

        // Memecah format "data:image/jpeg;base64,....."
        $imageParts = explode(";base64,", $base64Image);

        if (count($imageParts) < 2) {
            return response()->json(['status' => 'error', 'message' => 'Format gambar tidak valid'], 400);
        }

        // Decode string menjadi file gambar
        $imageDecoded = base64_decode($imageParts[1]);

        // 2. Tentukan penamaan file yang rapi
        // Format: proctoring/user_{name}_session_{id}_{timestamp}.jpg
        $folderPath = 'proctoring';
        $fileName = 'user_' . auth()->user()->name . '_session_' . $request->exam_session_id . '_' . time() . '.jpg';
        $fullPath = $folderPath . '/' . $fileName;

        // 3. Pastikan folder 'proctoring' ada di direktori public
        $directory = public_path($folderPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // 4. Simpan file langsung ke public/proctoring (Tanpa Symlink)
        // Cara ini memudahkan HRD mengakses via URL langsung: ://domain.com
        file_put_contents(public_path($fullPath), $imageDecoded);

        // Catat ke database untuk memudahkan HRD memantau
        ProctoringLog::create([
            'exam_session_id' => $request->exam_session_id,
            'image_path'      => $fileName
        ]);

        return response()->json(['status' => 'success']);
    }
}
