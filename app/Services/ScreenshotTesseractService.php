<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class ScreenshotTesseractService
{
    public function getData(string $screenshotPath): array
    {
        $path = '/var/www/html/storage/app/public/'.$screenshotPath;
        $out = (new TesseractOCR($path))->run();
        return [
            'out' => $out,
            'screenshotPath' => $screenshotPath
        ];
    }
}
