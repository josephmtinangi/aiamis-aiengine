<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnomalyDetection extends Model
{
    use HasFactory;

    public function estimationAlgorithm()
    {
        return $this->belongsTo(EstimationAlgorithm::class);
    }
}
