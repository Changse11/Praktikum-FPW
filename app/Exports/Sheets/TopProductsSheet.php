<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TopProductsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(protected $start, protected $end, protected $userId = null) {}

    public function collection(): Collection
    {
        return DB::table('transaction_details as d')
            ->join('transactions as t', 't.id', '=', 'd.transaction_id')
            ->join('products as p', 'p.id', '=', 'd.product_id')
            ->whereBetween('t.created_at', [$this->start, $this->end])
            ->when($this->userId, fn ($q) => $q->where('t.user_id', $this->userId))
            ->groupBy('p.id', 'p.code', 'p.name')
            ->orderByDesc('total_qty')
            ->orderBy('p.name')
            ->selectRaw('p.code, p.name, SUM(d.qty) as total_qty, SUM(d.subtotal) as total_omzet')
            ->get()
            ->values()
            ->map(fn ($row, $i) => [$i + 1, $row->code, $row->name, (int) $row->total_qty, (int) $row->total_omzet]);
    }

    public function headings(): array
    {
        return ['Peringkat', 'Kode', 'Nama Produk', 'Jumlah Terjual', 'Omzet'];
    }

    public function title(): string
    {
        return 'Produk Terlaris';
    }
}