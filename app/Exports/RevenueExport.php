<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RevenueExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        protected array $revenue,
        protected Carbon $fromDate,
        protected Carbon $toDate,
    ) {}

    public function array(): array
    {
        $rows = [];
        foreach ($this->revenue['labels'] as $index => $label) {
            $rows[] = [
                $label,
                $this->revenue['revenue'][$index] ?? 0,
            ];
        }

        $rows[] = ['Tổng cộng', $this->revenue['total'] ?? 0];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Kỳ ('.$this->fromDate->toDateString().' - '.$this->toDate->toDateString().')',
            'Doanh thu (VND)',
        ];
    }

    public function title(): string
    {
        return 'Doanh thu';
    }
}
