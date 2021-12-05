<?php

namespace App\Services;

use App\Enums\ScoreboardSlotKey;
use App\Models\ScoreboardMapping;
use App\Models\ScoreboardMappingSlot;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingService
{
    public function findLinesFromCoordinates(
        string $scoreboardPath,
        array $anchorCoordinates = null,
        array $textCoordinates = null
    ): array {
        $lines = [];

        $coordinates = [
            'x' => $textCoordinates['x'] ?? 0,
            'y' => $textCoordinates['y'] ?? 0,
            'width' => floatval($textCoordinates['width']),
            'height' => floatval($textCoordinates['height']),
        ];

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

            $lines[] = $block['text'];
        }

        return $lines;
    }

    public function findCoordinatesFromText(string $scoreboardPath, string $text): ?array
    {
        $data = $this->getJsonData($scoreboardPath);
        foreach ($data['blocks'] as $block)  {
            if ($block['text'] === $text) {
                return $block['dimensions'];
            }
        }

        return null;
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
        array $anchorCoordinates,
        array $slotCoordinates,
        ScoreboardSlotKey $slotKey
    ): ScoreboardMappingSlot {

        $scoreboardMapping = ScoreboardMapping::updateOrCreate([
            'anchor_text' => $anchorCoordinates['text']
        ], [
            'scoreboard_path' => $scoreboardPath,
            'height' => 100,
            'width' => 200,
            'size_kb' => 300
        ]);

        return ScoreboardMappingSlot::updateOrCreate([
            'scoreboard_mapping_id' => $scoreboardMapping->id,
            'key' => $slotKey->value
        ], [
            'offset_x' => intval($slotCoordinates['left']) - intval($anchorCoordinates['left']),
            'offset_y' => intval($slotCoordinates['top']) - intval($anchorCoordinates['top']),
            'top' => $slotCoordinates['top'],
            'right' => $slotCoordinates['right'],
            'bottom' => $slotCoordinates['bottom'],
            'left' => $slotCoordinates['left'],
            'width' => $slotCoordinates['width'],
            'height' => $slotCoordinates['height'],
            'text' => $slotCoordinates['text'],
        ]);
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
