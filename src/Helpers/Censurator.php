<?php

namespace App\Helpers;

class Censurator
{

    public function purify(string $text): string
    {
        $filterWords = ['gosh', 'darn', 'poo'];
        $filterCount = count($filterWords);
        for ($i = 0; $i < $filterCount; $i++) {
            $text = preg_replace_callback(
                '/\\b' . $filterWords[$i] . '\\b/i',
                fn($matches) => str_repeat('*', strlen($matches[0])),
                $text
            );
        }
        return $text;
    }
}