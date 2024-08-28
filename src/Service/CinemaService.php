<?php
// src/Service/CinemaService.php
namespace App\Service;

use App\Repository\CinemaRepository;

class CinemaService
{
    private $cinemaRepository;

    public function __construct(CinemaRepository $cinemaRepository)
    {
        $this->cinemaRepository = $cinemaRepository;
    }

    public function getCinemas()
    {
        return $this->cinemaRepository->findAll();
    }
}

