<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DownloadsController extends Controller
{
    public function download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "file_path" => ['required'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'code' => 400,
                'data' => null,
                'message' => 'File name is required'
            ], 400);
        }
        
        return Storage::download($request->file_path);
    }
}
