<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFile;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function store(Request $request)
    {
        try {
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = $file->getClientOriginalName();
                $folder = uniqid() . '_' . now()->timestamp;
                $file->storeAs('avatars/tmp/' . $folder, $fileName);
                TemporaryFile::create([
                    'folder' => $folder,
                    'filename' => $fileName,
                ]);
                return $folder;
            }
            return '';
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
