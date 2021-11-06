<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ScreenshotDimensionsService
{
    private $map = [
        'scoreRadiant' => [
            'x' => (252 - 246),
            'y' => (172 - 126),
            'width' => 500
        ],
        'firstPlayer' => [
            'x' => (216 - 246),
            'y' => (214 - 126),
            'width' => 500
        ]
    ];

    public function findTextCoordinates(string $screenshotPath, string $text): ?array
    {
        $data = $this->getJsonData($screenshotPath);
        foreach ($data['blocks'] as $block)  {
            if ($block['text'] === $text) {
                return $block['dimensions'];
            }
        }
    }

    private function getJsonData(string $screenshotPath): array {
        $jsonFilepath = dirname($screenshotPath).'/lines.json';
        return json_decode(Storage::disk('public')->get($jsonFilepath), true);
    }

    public function findShit(string $screenshotPath): array
    {
        $jsonFilepath = dirname($screenshotPath).'/lines.json';

        $data = json_decode(Storage::disk('public')->get($jsonFilepath), true);

        $anchorDimensions = 'Not found';
        foreach ($data['blocks'] as $block)  {
            if ($block['text'] === "Radiant") {
                $anchorDimensions = $block['dimensions']['tl'];
                break;
            }
        }

        // Find the score
        $mapped = [];
        foreach ($this->map as $key => $mapDimentions) {
            $width = $mapDimentions['width'];
            $anchorY = $anchorDimensions['y'] + $mapDimentions['y'];
            $anchorX = $anchorDimensions['x'] + $mapDimentions['x'];

            foreach ($data['blocks'] as $block) {

                // Coming from the top
                if ($block['dimensions']['tl']['y'] < $anchorY) {
                    continue;
                }

                // Coming from the left
                if ($block['dimensions']['tl']['x'] < $anchorX) {
                    continue;
                }

                $width -= abs($block['dimensions']['tr']['x'] - $mapDimentions['x']);
                if ($width < 0) {
                    break;
                }

                if (!isset($mapped[$key])) {
                    $mapped[$key] = '';
                }

                $mapped[$key] .= '|'.$block['text'];
            }
        }

        return [
            'screenshotPath' => $screenshotPath,
            'data' => $data,
            'dimensions' => $anchorDimensions,
            'mapped' => $mapped
        ];
    }
}
