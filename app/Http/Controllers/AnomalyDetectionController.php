<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Imports\DataImport;
use App\Models\AnomalyDetection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Rubix\ML\AnomalyDetectors\GaussianMLE;
use Torian257x\RubixAi\Facades\RubixAi;

class AnomalyDetectionController extends Controller
{
    public function detect(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "contamination" => ['required'],
            "file" => ['required'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'code' => 400,
                'data' => null,
                'message' => json_encode($validator->getMessageBag())
            ], 400);
        }

        $ad = AnomalyDetection::find($request->anomalyDetectionId);

        if(!$ad)
        {
            return response()->json([
                'success' => false,
                'code' => 400,
                'data' => null,
                'message' => 'Anomaly detection id does not exist'
            ], 400);
        }

        $excel = Excel::toArray(new DataImport, $request->file('file'));

        $dataset = $excel[0];

        $ad->total = sizeof($excel[0]);

        $startTime = Carbon::now();
        $ad->started_at = $startTime;

        $model_filename = Carbon::now()->timestamp.".rbx";

        $results = RubixAi::train($dataset, estimator_algorithm: new GaussianMLE(contamination: $request->contamination), model_filename: $model_filename);
        
        $endTime = Carbon::now();
        $ad->ended_at = $endTime;

        $duration = $endTime->diffInMilliseconds($startTime)/1000;
        
        $ad->duration = str_replace(" after", "", $duration);

        $anomalies = 0;
        foreach($results as $result)
        {
            if($result['anomaly'] == 1)
            {
                $anomalies++;
            }
        }
        $ad->anomalies = $anomalies;

        $ad->model_path = "model/".$model_filename;

        $filename = Carbon::now()->timestamp.'.csv';
        RubixAi::toCsv($results, $filename);

        $ad->file_path = "csv/".$filename;

        $ad->save();

        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $results,
            'message' => 'success',
        ], 200);
    }
}
