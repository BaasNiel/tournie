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

    public function findTextFromCoordinates(
        string $screenshotPath,
        array $anchorCoordinates = null,
        array $textCoordinates = null
    ): ?array {
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



        $width = $textCoordinates['width'];
        $anchorY = $anchorCoordinates['y'] + $textCoordinates['y'];
        $anchorX = $anchorCoordinates['x'] + $textCoordinates['x'];

        foreach ($data['blocks'] as $block) {

            // Coming from the top
            if ($block['dimensions']['tl']['y'] < $anchorY) {
                continue;
            }

            // Coming from the left
            if ($block['dimensions']['tl']['x'] < $anchorX) {
                continue;
            }

            $width -= abs($block['dimensions']['tr']['x'] - $textCoordinates['x']);
            if ($width < 0) {
                break;
            }

            $strings[] = $block['text'];
        }

        return [
            'screenshotPath' => $screenshotPath,
            'strings' => $strings,
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
