<?php

namespace App\Repositories;

use App\interfaces\DestinasiRepositoryInterface;
use App\Models\Destinasi;

class DestinasiRepository implements DestinasiRepositoryInterface
{
    public function getAllDestinasis()
    {
        return Destinasi::all();
    }

    public function getAllDestinasiBySlug($slug)
    {
        return Destinasi::where('slug', $slug)->first();
    }

    public function getAllDestinasiByIataCode($iataCode)
    {
        return Destinasi::where('iata_code', $iataCode)->first();
    }
}
