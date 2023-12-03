<?php

use Torian257x\RubWrap\Service\RubixService;

return [
    'csv_path_output' => storage_path('app/public/csv/'),
    'csv_path_input' => storage_path('app/public/csv/'),
    'ai_model_path_output' => storage_path('app/public/model/'),
    'RubixMainClass' => RubixService::class,
];
