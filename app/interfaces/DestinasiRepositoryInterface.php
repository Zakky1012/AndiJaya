<?php

namespace App\interfaces;

interface DestinasiRepositoryInterface
{
    public function getAllDestinasis();

    public function getAllDestinasiBySlug($slug);

    public function getAllDestinasiByIataCode($iataCode);
}
