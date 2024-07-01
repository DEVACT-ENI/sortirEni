<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('file_exists', 'file_exists'),
            new TwigFunction('realpath', 'realpath'),
        ];
    }
}