<?php

namespace App\interfaces;

interface KeberangkatanRepositoryInterface
{
    public function getAllKeberangkatans($filter = null);

    public function getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan);
}
