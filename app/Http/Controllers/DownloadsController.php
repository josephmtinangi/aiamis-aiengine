<?php

namespace App\Http\Controllers;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

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

    public function read(Request $request)
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

        $file = public_path("storage/".$request->file_path);
        
        $results = Excel::toArray(new DataImport, $file);

        $headings = (new HeadingRowImport)->toArray($file);

        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => [
                'headings' => $headings[0][0],
                'anomalies' => $results[0]
            ],
            'message' => 'success',
        ], 200);        
    }
}
