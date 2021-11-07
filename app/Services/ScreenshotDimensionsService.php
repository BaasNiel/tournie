<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ScreenshotDimensionsService
{
    public function findTextFromCoordinates(
        string $screenshotPath,
        array $anchorCoordinates = null,
        array $textCoordinates = null
    ): array {
        $strings = [];

        // Append text coords with anchor (if set)
        $coordinates = [
            'x' => $textCoordinates['x'] ?? 0,
            'y' => $textCoordinates['y'] ?? 0,
            'width' => floatval($textCoordinates['width']),
            'height' => floatval($textCoordinates['height']),
        ];

        // Don't append yet (weird shit going on!)
        // $coordinates['x'] += !empty($anchorCoordinates['x'])
        //     ? $anchorCoordinates['x']
        //     : 0;

        // $coordinates['y'] += !empty($anchorCoordinates['y'])
        //     ? $anchorCoordinates['y']
        //     : 0;


        // Walk through blocks and get all the text
        $data = $this->getJsonData($screenshotPath);

        $boundaries = [
            'top' => $coordinates['y'],
            'right' => $coordinates['x'] + $coordinates['width'],
            'bottom' => $coordinates['y'] + $coordinates['height'],
            'left' => $coordinates['x'],
        ];

        foreach ($data['blocks'] as $block) {
            $blockCoordinates = [
                'top' => $block['dimensions']['tl']['y'],
                'left' => $block['dimensions']['tl']['x']
            ];

            // Too far left
            if ($blockCoordinates['left'] < $boundaries['left']) {
                continue;
            }

            // Too far right
            if ($blockCoordinates['left'] > $boundaries['right']) {
                continue;
            }

            // Too far top
            if ($blockCoordinates['top'] < $boundaries['top']) {
                continue;
            }

            // Too far bottom
            if ($blockCoordinates['top'] > $boundaries['bottom']) {
                continue;
            }

            $strings[] = $block['text'];
        }

        return [
            'anchorCoordinates' => $anchorCoordinates,
            'textCoordinates' => $textCoordinates,
            'coordinates' => $coordinates,
            'boundaries' => $boundaries,
            'strings' => $strings
        ];
    }

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
}
