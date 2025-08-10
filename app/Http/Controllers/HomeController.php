<?php

namespace App\Http\Controllers;

use App\interfaces\DestinasiRepositoryInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private DestinasiRepositoryInterface $destinasiRepository;

    public function __construct(DestinasiRepositoryInterface $destinasiRepository)
    {
        $this->destinasiRepository = $destinasiRepository;
    }

    public function index()
    {
        $destinasis = $this->destinasiRepository->getAllDestinasis();

        return view('pages.home', compact('destinasis'));
    }
}
