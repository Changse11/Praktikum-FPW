<?php

namespace App\Exports;

use App\Exports\Sheets\SalesSheet;
use App\Exports\Sheets\TopProductsSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesExport implements Export, WithMultipleSheets
{
    public function __construct(protected $start, protected $end, protected $userId = null) {}

    public function sheets(): array
    {
        return [
            new SalesSheet($this->start, $this->end, $this->userId),
            new TopProductsSheet($this->start, $this->end, $this->userId),
        ];
    }
}