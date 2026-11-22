<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        [$start, $end, $userId] = $this->filters($request);

        $transactions = Transaction::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->orderByDesc('id')
            ->get();

        $cashiers = User::whereIn('id', Transaction::select('user_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $totalPenjualan = $transactions->sum('total');
        $jumlahTransaksi = $transactions->count();

        return view('reports.sales', compact(
            'transactions', 'totalPenjualan', 'jumlahTransaksi', 'start', 'end', 'cashiers', 'userId'
        ));
    }

    public function export(Request $request)
    {
        [$start, $end, $userId] = $this->filters($request);

        $filename = 'laporan-penjualan-' . $start->format('Ymd') . '-' . $end->format('Ymd')
            . ($userId ? '-kasir' . $userId : '') . '.xlsx';

        return Excel::download(new SalesExport($start, $end, $userId), $filename);
    }

    private function filters(Request $request): array
    {
        $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', Rule::when($request->filled('start'), 'after_or_equal:start')],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'start.date' => 'Tanggal awal tidak valid.',
            'end.date' => 'Tanggal akhir tidak valid.',
            'end.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            'user_id.exists' => 'Kasir yang dipilih tidak ditemukan.',
        ]);

        $start = $request->filled('start')
            ? Carbon::parse($request->start)->startOfDay()
            : now()->subDays(6)->startOfDay();

        $end = $request->filled('end')
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        $userId = $request->filled('user_id') ? (int) $request->user_id : null;

        return [$start, $end, $userId];
    }
}