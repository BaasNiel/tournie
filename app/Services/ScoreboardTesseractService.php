<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class ScoreboardTesseractService
{
    public function getData(string $scoreboardPath): array
    {
        // To-do: Change to dynamic route
        $path = '/var/www/html/storage/app/public/'.$scoreboardPath;
        $out = (new TesseractOCR($path))->run();
        return [
            'out' => $out,
            'scoreboardPath' => $scoreboardPath
        ];
    }
}
