<?php
namespace Framework\Twig;

class TimeExtension extends \Twig\Extension\AbstractExtension
{

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i')
    {
        return '<span class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">' .
            $date->format($format) .
            '</span>';
    }
}
