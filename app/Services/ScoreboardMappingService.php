<?php

namespace App\Services;

use App\Enums\ScoreboardSlotKey;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingService
{
    public function findTextFromCoordinates(
        string $scoreboardPath,
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
        $data = $this->getJsonData($scoreboardPath);

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

    public function findTextCoordinates(string $scoreboardPath, string $text): ?array
    {
        $data = $this->getJsonData($scoreboardPath);
        foreach ($data['blocks'] as $block)  {
            if ($block['text'] === $text) {
                return $block['dimensions'];
            }
        }
    }

    public function getAvailableSlots(
        string $scoreboardPath,
        ?array $anchorCoordinates,
    ): ?array
    {
        $path = 'config/scoreboard-2.json';
        $anchorText = 'Radiant';
        $config = $this->getConfig($scoreboardPath);
        $configSlots = collect($config['anchors'][$anchorText]['slots'] ?? []);

        $anchor = $configSlots->firstWhere('slotKey', ScoreboardSlotKey::ANCHOR);
        if (!$anchor) {
            return [
                ScoreboardSlotKey::ANCHOR
            ];
        }

        // Filter slots by config
        return collect(config('scoreboard.slots'))->filter(function ($slotKey) use ($configSlots) {
            return !$configSlots->pluck('slotKey')->contains($slotKey);
        })->values()->toArray();
    }

    public function updateOrCreateSlot(
        string $scoreboardPath,
        ?array $anchorCoordinates,
        array $textCoordinates,
        ScoreboardSlotKey $slotKey
    ): array {
        $config = $this->getConfig($scoreboardPath);

        $textCoordinates['slotKey'] = $slotKey->value;

        if ($slotKey->value === ScoreboardSlotKey::ANCHOR) {
            $anchorCoordinates = $textCoordinates;
            $config['anchors'][$anchorCoordinates['text']] = $anchorCoordinates;
        }

        // Calculate relatives
        $textCoordinates['x'] -= $anchorCoordinates['x'] ?? 0;
        $textCoordinates['y'] -= $anchorCoordinates['y'] ?? 0;

        $config['anchors'][$anchorCoordinates['text']]['slots'][$slotKey->value] = $textCoordinates;

        $this->saveConfig($scoreboardPath, $config);

        return [
            'success' => true,
            'scoreboardPath' => $scoreboardPath,
            'anchorCoordinates' => $anchorCoordinates,
            'textCoordinates' => $textCoordinates,
            'slotKey' => $slotKey
        ];

        // if (ScoreboardSlotKey::ANCHOR === $slotKey) {
        //     $anchorCoordinates = $textCoordinates;
        //     $config['anchors'][$anchorCoordinates['text']] = $anchorCoordinates;
        // } else if (!empty($anchorCoordinates)) {
        //     // Calculate relatives
        //     $textCoordinates['x'] -= $anchorCoordinates['x'];
        //     $textCoordinates['y'] -= $anchorCoordinates['y'];
        // }

        // $config['anchors'][$anchorCoordinates['text']]['slots'][$slotKey] = $textCoordinates;

        // $this->saveConfig($scoreboardPath, $config);

        // return [
        //     'success' => true,
        //     'scoreboardPath' => $scoreboardPath,
        //     'anchorCoordinates' => $anchorCoordinates,
        //     'textCoordinates' => $textCoordinates,
        //     'slotKey' => $slotKey
        // ];
    }

    private function getConfig(string $scoreboardPath): ?array
    {
        $path = 'config/scoreboard-2.json';
        if (Storage::disk('local')->exists($path)) {
            return json_decode(Storage::disk('local')->get($path), true);
        }
    }

    private function saveConfig(string $scoreboardPath, array $config): bool
    {
        $path = 'config/scoreboard-2.json';
        return Storage::disk('local')->put($path, json_encode($config, JSON_PRETTY_PRINT));
    }

    private function getAnchorCoordinates() {
        return null;
    }

    private function hasAnchorCoordinates() {
        return $this->getAnchorCoordinates() ? true : false;
    }

    private function getJsonData(string $scoreboardPath): array {
        $jsonFilepath = dirname($scoreboardPath).'/lines.json';
        return json_decode(Storage::disk('public')->get($jsonFilepath), true);
    }
}
