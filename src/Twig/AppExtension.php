<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\CinemaService;

class AppExtension extends AbstractExtension
{
    private $cinemaService;

    public function __construct(CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    public function getFunctions(): array
    {   
        return [
            new TwigFunction('get_cinemas', [$this, 'getCinemas']),
        ];
    }

    public function getCinemas()
    {
        return $this->cinemaService->get_cinemas();
    }

    
}
