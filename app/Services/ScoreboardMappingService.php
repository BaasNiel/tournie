<?php

namespace App\Services;

use App\Enums\ScoreboardSlotKey;
use App\Models\ScoreboardMapping;
use App\Models\ScoreboardMappingSlot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingService
{
    public function __construct(
        protected ScoreboardMappingSlotService $slotService,
        protected array|null $anchorCoordinates = null,
        protected array|null $mappedSlots = null
    ){ }

    public function findScoreboardMapping(string $scoreboardPath, array $data): array
    {
        $scoreboardBlocks = collect($data['blocks']);

        $anchor = ScoreboardMapping::with('slots')->get()->first(function ($scoreboardMapping) use ($scoreboardBlocks) {
            $this->anchorCoordinates = $this->slotService->getAnchor(
                $scoreboardBlocks,
                $scoreboardMapping->slots
            );

            if (is_null($this->anchorCoordinates)) {
                return false;
            }

            $this->mappedSlots = $this->slotService->getMappedSlots(
                $scoreboardBlocks,
                $scoreboardMapping->slots,
                $this->anchorCoordinates
            );

            return true;
        });

        return [
            'anchorCoordinates' => $this->anchorCoordinates,
            'slots' => $this->mappedSlots
        ];
    }

    public function findLinesByCoordinates(
        string $scoreboardPath,
        ?array $anchorCoordinates = null,
        ?array $coordinates = null
    ): array {

        // Get blocks from json file
        $blocks = collect($this->getJsonData($scoreboardPath)['blocks'] ?? []);

        return $this->slotService->findLinesByCoordinatesFromBlocks(
            $blocks,
            $anchorCoordinates,
            $coordinates
        );
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
