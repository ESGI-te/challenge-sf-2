<?php

namespace App\Twig\Extension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CentimesToEurosExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('centimes_to_euros', [$this, 'centimesToEurosFilter']),
        ];
    }

    public function centimesToEurosFilter(int $value): string
    {
        $euros = $value / 100;
        return number_format($euros, 2, '.', '') . ' €';
    }
}