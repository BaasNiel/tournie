<?php

namespace App\Services;

use App\Enums\ScoreboardSlotKey;
use App\Models\ScoreboardMapping;
use App\Models\ScoreboardMappingSlot;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingService
{
    public function findScoreboardMapping(string $scoreboardPath, array $data): array
    {
        $blocks = collect($data['blocks']);

        // Map the slots and text
        $anchorCoordinates = [];
        $anchor = ScoreboardMapping::with('slots')->get()->first(function ($scoreboardMapping) use ($blocks, &$anchorCoordinates) {

            // Get the anchor
            $anchor = $scoreboardMapping->slots->firstWhere('key', ScoreboardSlotKey::ANCHOR);
            $anchorCoordinates = [
                'x' => $anchor->left,
                'y' => $anchor->top,
                'width' => $anchor->width,
                'height' => $anchor->height
            ];

            // Find anchor in scoreboard's blocks
            return $blocks->containsStrict('text', $anchor->text);
        });

        // Walk through all the slots and map the block text
        // $keys = $anchor->slots->mapWithKeys(function ($slot) use ($scoreboardPath, $anchorCoordinates) {
        //     $lines = $this->findLinesFromCoordinates(
        //         $scoreboardPath,
        //         $anchorCoordinates, [
        //             'x' => $slot->left,
        //             'y' => $slot->top,
        //             'width' => $slot->width,
        //             'height' => $slot->height
        //         ]
        //     );
        //     return [
        //         $slot->key => $lines
        //     ];
        // });

        // Walk through all the slots and map the block text
        $slots = $anchor->slots->map(function ($slot) use ($scoreboardPath, $anchorCoordinates) {
            $lines = $this->findLinesFromCoordinates(
                $scoreboardPath,
                $anchorCoordinates, [
                    'x' => $slot->left,
                    'y' => $slot->top,
                    'width' => $slot->width,
                    'height' => $slot->height
                ]
            );

            $scoreboardSlotKey = ScoreboardSlotKey::getSlot(ScoreboardSlotKey::getValue($slot->key));

            $scoreboardSlotKey['lines'] = $lines;

            return $scoreboardSlotKey;
        })
        ->sortBy('key', SORT_NATURAL)
        ->values();

        return [
            'anchor' => $anchor,
            'slots' => $slots,
            'anchorCoordinates' => $anchorCoordinates
        ];
    }

    public function findLinesFromCoordinates(
        string $scoreboardPath,
        array $anchorCoordinates = null,
        array $coordinates = null
    ): array {
        $lines = [];

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

    public function getAvailableSlotKeys(
        string $scoreboardPath,
        ?array $anchorCoordinates
    ): array
    {
        $availableSlotKeys = collect(ScoreboardSlotKey::getAll());

        // filter with set keys
        if (!is_null($anchorCoordinates)) {
            $scoreboardMapping = ScoreboardMapping::with('slots')->firstWhere('anchor_text', $anchorCoordinates['text']);
            $keys = $scoreboardMapping->slots->pluck('key');

            $availableSlotKeys = $availableSlotKeys->filter(function ($availableSlotKey) use ($keys) {
                return !$keys->contains($availableSlotKey['key']);
            });
        }

        return $availableSlotKeys->values()->toArray();
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

    private function getJsonData(string $scoreboardPath): array {
        $jsonFilepath = dirname($scoreboardPath).'/lines.json';
        return json_decode(Storage::disk('public')->get($jsonFilepath), true);
    }
}
