<?php
// src/Service/CinemaService.php
namespace App\Service;

use App\Repository\CinemaRepository;

class CinemaService
{
    private CinemaRepository $cinemaRepository;

    public function __construct(CinemaRepository $cinemaRepository)
    {
        $this->cinemaRepository = $cinemaRepository;
    }

    public function get_cinemas()
    {
        $cinemas = $this->cinemaRepository->getAllCinemas();
        return $cinemas;
    }
}
