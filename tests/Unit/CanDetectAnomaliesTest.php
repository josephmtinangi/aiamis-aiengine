<?php

namespace Tests\Unit;


use Rubix\ML\AnomalyDetectors\GaussianMLE;
use Tests\TestCase;
use Tests\Unit\TestEloquentModels\Apartment;
use Torian257x\RubixAi\Facades\RubixAi;

class CanDetectAnomaliesTest extends TestCase
{
    public function test_anomalies()
    {
        $all = Apartment::query()->get();


        $all = $all->map(
            function ($a) {

                if ($a->water_heating === 'No tiene') {
                    $a->water_heating = 0;
                } else if ($a->water_heating === 'Gas') {
                    $a->water_heating = 1;
                } else if ($a->water_heating === 'Eléctrico') {
                    $a->water_heating = 0.5;
                } else {
                    $a->water_heating = 0;
                }

                if ($a->doorman === '24 Horas') {
                    $a->doorman = 1;
                } else if ($a->doorman === 'Diurna') {
                    $a->doorman = 0.5;
                } else if ($a->doorman === 'No tiene') {
                    $a->doorman = 0;
                } else {
                    $a->doorman = 0;
                }

                return $a;

            }
        );

        $all->makeHidden(['zone_2_id', 'zone_id']);

        $data = RubixAi::trainWithoutTest($all,
            estimator_algorithm: new GaussianMLE(contamination: 0.005));

        RubixAi::toCsv($data, 'anomalies.csv');

        $abnomalies = 0;

        foreach($data as $d){
            $abnomalies += $d['anomaly'];
        }
        self::assertGreaterThan(2, $abnomalies);

    }
}
