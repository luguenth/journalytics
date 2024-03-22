<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('group_by', [$this, 'groupBy']),
        ];
    }

    public function groupBy(array $array, string $property): array
    {
        $grouped = [];
        foreach ($array as $element) {
            $getter = 'get' . ucfirst($property);
            if (method_exists($element, $getter)) {
                $key = $element->$getter();
                $grouped[$key][] = $element;
            }
        }
        dump($grouped);
        return $grouped;
    }
}
