<?php

namespace Tests\Unit;


use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Kernels\Distance\Manhattan;
use Tests\TestCase;
use Tests\Unit\TestEloquentModels\Apartment;
use Torian257x\RubixAi\Facades\RubixAi;

class CanClusterTest extends TestCase
{
    public static function getData()
    {

        $data = Apartment::limit(500)->get();
        $data = $data->map(
            function (Apartment $a) {

                $wh = $a->water_heating;

                if ($wh === 'No tiene') {
                    $wh_val = 0;
                } else if ($wh === 'Gas') {
                    $wh_val = 1;
                } else if ($wh === 'Eléctrico') {
                    $wh_val = 0.5;
                } else {
                    $wh_val = 0;
                }
                $a->water_heating = $wh_val;

                $dm = $a->doorman;


                if ($dm === '24 Horas') {
                    $dm_val = 1;
                } else if ($dm === 'Diurna') {
                    $dm_val = 0.5;
                } else if ($dm === 'No tiene') {
                    $dm_val = 0;
                } else {
                    $dm_val = 0;
                }

                $a->doorman = $dm_val;

                $a->rr = pow($a->rooms, 2);
                $a->pp = pow($a->price_millions, 2);
                $a->pp3 = pow($a->price_millions, 3);
                $a->pp4 = pow($a->price_millions, 4);
                $a->pp5 = pow($a->price_millions, 5);
                $a->p_t_r = $a->price_millions * $a->rooms ;
                $a->p_t_lat = $a->price_millions * $a->geo_lat;
                $a->p_t_lng = $a->price_millions * $a->geo_lng;
                $a->latlng1 = $a->geo_lat * $a->geo_lng;
                $a->latlng2 = pow($a->geo_lat * $a->geo_lng, 2);

                $rv = $a->toArray();

                unset($rv['zone_id']);
                unset($rv['zone_2_id']);

                return $rv;
            }
        );


        return $data;
    }

    public function test_can_cluster()
    {
        $all = self::getData();

        $nr_groups = ceil(sizeof($all)/10);

        $data_w_clusters = RubixAi::trainWithoutTest($all,
            estimator_algorithm: new KMeans($nr_groups,
                kernel: new Manhattan()));

        RubixAi::toCsv($data_w_clusters, 'data_w_clusters.csv');

        self::assertTrue(array_search(49, array_column($data_w_clusters,'cluster_nr')) !== false);

    }
}
