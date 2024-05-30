<?php

namespace App\Twig\Extension;

use App\Entity\Models;
use App\Repository\ModelsRepository;
use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public $modelsRepository;
    public function __construct(ModelsRepository $modelsRepository)
            {
            $this->modelsRepository = $modelsRepository;
            }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('Models', [$this, 'Models']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('Models', [$this, 'Models']),
        ];
    }

    public function Models():array
    {
        return $this->modelsRepository->findAll();
    }
}
