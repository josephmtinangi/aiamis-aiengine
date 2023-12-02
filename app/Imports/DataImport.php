<?php

namespace App\Imports;

use App\Models\Data;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements WithHeadingRow
{
    public function model(array $row)
    {
        return new Data([
           'id'     => $row[0],
        ]);
    }
}
