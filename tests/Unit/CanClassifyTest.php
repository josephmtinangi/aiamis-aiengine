<?php

namespace Tests\Unit;


use Tests\TestCase;
use Tests\Unit\TestEloquentModels\IrisFlower;
use Torian257x\RubixAi\Facades\RubixAi;

class CanClassifyTest extends TestCase
{
    public function test_can_classify()
    {

        $all_flowers = IrisFlower::all();

        $report = RubixAi::train($all_flowers, 'iris_plant_type');

        self::assertGreaterThan(0.8, $report['mcc']);

    }

    public function test_can_predict(){
        $predict_me = [
          [6.7,3.1,4.6,1.4], //versicolor
          [7.7,2.4,6.7,1.2], //virginica
        ];

        $results = RubixAi::predict($predict_me);

        self::assertEquals('versicolor', $results[0]);
        self::assertEquals('virginica', $results[1]);
    }
}
