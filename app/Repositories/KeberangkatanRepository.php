<?php

namespace App\Repositories;

use App\interfaces\KeberangkatanRepositoryInterface;
use App\Models\Keberangkatan;

class KeberangkatanRepository implements KeberangkatanRepositoryInterface
{
    public function getAllKeberangkatans($filter = null)
    {
        $keberangkatans = Keberangkatan::query();

        if (!empty($filter['departure'])) {
            $keberangkatans->whereHas('segments', function ($query) use ($filter) {
                $query->where('destinasi_id', $filter['departure'])
                    ->where('sequence', 1);
            });
        }

        if (!empty($filter['arrival'])) {
            $keberangkatans->whereHas('segments', function ($query) use ($filter) {
                $query->where('destinasi_id', $filter['arrival']);
            });
        }

        if(!empty($filter['date'])) {
            $keberangkatans->whereHas('segments', function ($query) use ($filter) {
                $query->whereDate('time', $filter['date']);
        });
    }

    return $keberangkatans->get();

    }

    public function getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan)
    {
         // Perbaikan utama di sini: tambahkan ->first() atau ->firstOrFail()
        return Keberangkatan::with(['segments.destinasi', 'Mobil', 'classes.facilities'])
            ->where('nomor_keberangkatan', $nomorKeberangkatan)
            ->firstOrFail();
    }
}

