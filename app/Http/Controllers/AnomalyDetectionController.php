<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Imports\DataImport;
use App\Models\AnomalyDetection;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Rubix\ML\AnomalyDetectors\GaussianMLE;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use Rubix\ML\AnomalyDetectors\LocalOutlierFactor;
use Rubix\ML\AnomalyDetectors\Loda;
use Rubix\ML\AnomalyDetectors\OneClassSVM;
use Rubix\ML\AnomalyDetectors\RobustZScore;
use Throwable;
use Torian257x\RubixAi\Facades\RubixAi;

class AnomalyDetectionController extends Controller
{
    public function index()
    {
        $message = "";
        try{
            //division by zero
            $number = 5/0;
        }
        catch(Error $e){
            $message = $e->getMessage();
        }
        catch(Throwable $e) {
            $message = "This should work as well";
        }    
        
        return $message;
    }

    public function detect(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "contamination" => ['numeric'],
            "file" => ['required'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'code' => "BAD_REQUEST",
                'data' => null,
                'message' => json_encode($validator->getMessageBag())
            ], 400);
        }

        $ad = AnomalyDetection::find($request->anomalyDetectionId);

        if(!$ad)
        {
            return response()->json([
                'success' => false,
                'code' => "BAD_REQUEST",
                'data' => null,
                'message' => 'Anomaly detection id does not exist'
            ], 400);
        }

        $excel = Excel::toArray(new DataImport, $request->file('file'));
        $dataset = $excel[0];
        $ad->total = sizeof($excel[0]);
        $results = [];

        $message = "";

        try{
            $startTime = Carbon::now();
            $ad->started_at = $startTime;
            $model_filename = Carbon::now()->timestamp.".rbx";

            if($ad->estimationAlgorithm->slug == "gaussian-mle")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new GaussianMLE(contamination: $request->contamination), model_filename: $model_filename);
            }
            if($ad->estimationAlgorithm->slug == "isolation-forest")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new IsolationForest(contamination: $request->contamination), model_filename: $model_filename);
            }
            if($ad->estimationAlgorithm->slug == "local-outlier-factor")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new LocalOutlierFactor(contamination: $request->contamination), model_filename: $model_filename);
            }
            if($ad->estimationAlgorithm->slug == "loda")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new Loda(contamination: $request->contamination), model_filename: $model_filename);
            }
            if($ad->estimationAlgorithm->slug == "one-class-svm")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new OneClassSVM(), model_filename: $model_filename);
            }
            if($ad->estimationAlgorithm->slug == "robust-z-score")
            {
                $results = RubixAi::train($dataset, estimator_algorithm: new RobustZScore(), model_filename: $model_filename);
            }

            $anomalies = 0;
            foreach($results as $result)
            {
                if($result['anomaly'] == 1)
                {
                    $anomalies++;
                }
            }

            $endTime = Carbon::now();
            $ad->ended_at = $endTime;
            $duration = $endTime->diffInMilliseconds($startTime)/1000;
            $ad->duration = str_replace(" after", "", $duration);

            $ad->anomalies = $anomalies;
            $ad->model_path = "model/".$model_filename;
    
            $filename = Carbon::now()->timestamp.'.csv';
            RubixAi::toCsv($results, $filename);
            $ad->file_path = "csv/".$filename;
            $ad->status = "success";
            $ad->save();

        } catch(Error $e) {
            $message = $e->getMessage();

            $ad->message = $message;
            $ad->status = "failed";
            $ad->save();

            return response()->json([
                'success' => false,
                'code' => "BAD_REQUEST",
                'data' => $results,
                'message' => $message,
            ], 400);
        }



        return response()->json([
            'success' => true,
            'code' => "OK",
            'data' => $results,
            'message' => 'success',
        ], 200);
    }
}
