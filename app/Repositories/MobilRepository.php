<?php

namespace App\Repositories;

use App\interfaces\MobilRepositoryInterface;
use App\Models\Mobil;

class MobilRepository implements MobilRepositoryInterface
{
    public function getAllMobils()
    {
        return Mobil::all();
    }
}
