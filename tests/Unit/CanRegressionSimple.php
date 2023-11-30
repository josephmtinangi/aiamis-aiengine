<?php

namespace Tests\Unit;


use Rubix\ML\Regressors\Ridge;
use Tests\TestCase;
use Torian257x\RubixAi\Facades\RubixAi;

class CanRegressionSimple extends TestCase
{
    public function test_can_regression_simple()
    {
        $data = [
            ['space' => 10, 'price' => 100],
            ['space' => 20, 'price' => 200],
            ['space' => 30, 'price' => 300],
            ['space' => 40, 'price' => 400],
            ['space' => 50, 'price' => 500],
            ['space' => 60, 'price' => 600],
            ['space' => 70, 'price' => 700],
            ['space' => 110, 'price' => 1100],
            ['space' => 120, 'price' => 1200],
            ['space' => 130, 'price' => 1300],
            ['space' => 140, 'price' => 1400],
            ['space' => 150, 'price' => 1500],
            ['space' => 160, 'price' => 1600],
            ['space' => 170, 'price' => 1700],
            ['space' => 180, 'price' => 1800],
            ['space' => 190, 'price' => 1900],
            ['space' => 200, 'price' => 2000],
            ['space' => 210, 'price' => 2100],
            ['space' => 220, 'price' => 2200],
            ['space' => 230, 'price' => 2300],
            ['space' => 240, 'price' => 2400],
            ['space' => 250, 'price' => 2500],
            ['space' => 260, 'price' => 2600],
            ['space' => 270, 'price' => 2700],
            ['space' => 280, 'price' => 2800],
            ['space' => 290, 'price' => 2900],
            ['space' => 300, 'price' => 3000],
            ['space' => 310, 'price' => 3100],
            ['space' => 320, 'price' => 3200],
        ];

        $rep = RubixAi::train($data,
            'price',
            train_part_size: 0.95,
            estimator_algorithm: new Ridge(0.001)

        );

        $pm = [
            [74.5],
        ];

        $predictions = RubixAi::predict($pm);

        self::assertLessThan(200, abs($predictions[0] - 745));

    }
}
