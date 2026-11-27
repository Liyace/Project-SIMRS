<?php

namespace App\Exports;

use App\Models\Obat;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class StokObatExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $filter;

    public function __construct($filter = 'semua')
    {
        $this->filter = $filter;
    }

    public function view(): View
    {
        $query = Obat::query();

        if ($this->filter === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($this->filter === 'habis') {
            $query->where('stok', '<=', 0);
        } elseif ($this->filter === 'aman') {
            $query->whereColumn('stok', '>', 'stok_minimum');
        }

        $obatList = $query->orderBy('nama_obat', 'asc')->get();

        $totalItem = $obatList->count();
        $totalFisik = $obatList->sum('stok');
        $totalValuasi = $obatList->sum(function ($item) {
            return (float) ($item->stok * $item->harga_obat);
        });

        return view('apoteker.stok_excel', [
            'obatList'     => $obatList,
            'totalItem'    => $totalItem,
            'totalFisik'   => $totalFisik,
            'totalValuasi' => $totalValuasi,
            'tanggalCetak' => now()->translatedFormat('d F Y H:i'),
            'filter'       => $this->filter,
        ]);
    }

    public function title(): string
    {
        return 'Stok Opname Farmasi RS Delta';
    }
}
