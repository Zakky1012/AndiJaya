<?php

namespace App\Http\Controllers;

use App\interfaces\DestinasiRepositoryInterface;
use App\interfaces\KeberangkatanRepositoryInterface;
use App\interfaces\MobilRepositoryInterface;
use Illuminate\Http\Request;

class KeberangkatanController extends Controller
{
    private DestinasiRepositoryInterface $destinasiRepository;
    private MobilRepositoryInterface $mobilRepository;
    private KeberangkatanRepositoryInterface $keberangkatanRepository;

    public function __construct(
        DestinasiRepositoryInterface $destinasiRepository,
        MobilRepositoryInterface $mobilRepository,
        KeberangkatanRepositoryInterface $keberangkatanRepository
    ) {
        $this->destinasiRepository      = $destinasiRepository;
        $this->mobilRepository          = $mobilRepository;
        $this->keberangkatanRepository  = $keberangkatanRepository;
    }

    public function index(Request $request)
    {
        $departure      = $this->destinasiRepository->getAllDestinasiByIataCode($request->departure);
        $arrival        = $this->destinasiRepository->getAllDestinasiByIataCode($request->arrival);

        $keberangkatans = $this->keberangkatanRepository->getAllKeberangkatans([
            'departure' => $departure->id ?? null,
            'arrival'   => $arrival->id ?? null,
            'date'      => $request->date ?? null
        ]);

        $mobils     = $this->mobilRepository->getAllMobils();

        return view('pages.keberangkatan.index', compact('keberangkatans', 'mobils'));
    }

    public function show($nomorKeberangkatan)
    {
        $keberangkatan = $this->keberangkatanRepository->getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan);

        return view('pages.keberangkatan.show', compact('keberangkatan'));
    }
}
