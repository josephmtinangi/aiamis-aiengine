<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimationAlgorithm extends Model
{
    use HasFactory;

    public function anomalyDetections()
    {
        return $this->hasMany(AnomalyDetection::class);
    }
}
