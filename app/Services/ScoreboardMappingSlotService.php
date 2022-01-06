<?php

namespace App\Services;

use App\Collections\ScoreboardMappingSlotCollection;
use App\Enums\Hero;
use App\Enums\ScoreboardSlotKey;
use App\Exceptions\ClientDecisionException;
use Illuminate\Support\Collection;

class ScoreboardMappingSlotService {

    public function __construct(
        protected PlayerAliasService $playerAliasService
    ){ }

    public function getAnchor(
        Collection $blocks,
        ScoreboardMappingSlotCollection $slots
    ): array {

        // Fully mapped slots only
        if ($slots->count() !== count(ScoreboardSlotKey::getAll())) {
            return [];
        }

        // Matching anchor key
        $anchor = $slots->firstWhere('key', ScoreboardSlotKey::ANCHOR);
        if (!$blocks->containsStrict('text', $anchor->text)) {
            return [];
        }

        return [
            'x' => $anchor->left,
            'y' => $anchor->top,
            'width' => $anchor->width,
            'height' => $anchor->height
        ];
    }

    public function getMappedSlots(
        Collection $blocks,
        ScoreboardMappingSlotCollection $slots,
        array $anchorCoordinates
    ): Collection {

        return $slots->map(function ($slot) use ($blocks, $anchorCoordinates) {
            $coordinates = [
                'x' => $slot->left,
                'y' => $slot->top,
                'width' => $slot->width,
                'height' => $slot->height
            ];

            $lines = $this->findLinesByCoordinatesFromBlocks(
                $blocks,
                $anchorCoordinates,
                $coordinates
            );

            $scoreboardSlotKey = ScoreboardSlotKey::getSlot(ScoreboardSlotKey::getValue($slot->key));
            $scoreboardSlotKey['lines'] = $lines;

            return $scoreboardSlotKey;
        })->map(function ($slot) use ($blocks, $anchorCoordinates)  {
            return $this->validateSlot($slot);
        })
        ->sortBy('key', SORT_NATURAL)
        ->values();

    }

    protected function getNumericValue(array $lines): ?int
    {
        $value = implode(' ', $lines);

        // Special conversions
        // - Read "。" as "0" when it should be "3"
        if ($value === "。") {
            $value = 3;
        }

        // Remove non-numeric
        $value = preg_replace("/[^0-9.]/", '', $value);

        return $value >= 0
            ? intval($value)
            : null;
    }

    protected function validateSlot(array $slot) {
        extract($slot);

        $value = null;
        $type = collect(explode('_', $key))->last();

        $error = null;
        switch ($type) {
            case 'KILLS':
            case 'DEATHS':
            case 'ASSISTS':
            case 'WORTH':
            case 'HITS':
            case 'DENIES':
            case 'GPM':
            case 'LEVEL':
            case 'SCORE':
                $value = $this->getNumericValue($lines);
                if (is_null($value)) {
                    $error = 'Expected numerical value';
                }
            break;
            case 'ANCHOR':
                $value = implode(" ", $lines);
            break;
            case 'WINNER':
                $value = !empty($lines[0]) ? true : false;
            break;
            case 'NAME':
                $value = Hero::findHeroName($lines);
                if (is_null($value)) {
                    $error = 'Expected hero name';
                }
            break;
            case 'CLAN':
                $playerAlias = $this->playerAliasService->find($lines);

                if (is_null($playerAlias)) {
                    throw new ClientDecisionException(
                        'Player alias not found', [
                        'action' => [
                            'method' => 'POST',
                            'endpoint' => '/client-exception/alias'
                        ],
                        'type' => 'text',
                        'label' => 'Enter the alias',
                        'value' => implode(' ', $lines),
                        'slot' => $slot
                    ]);
                } else {
                    $value = $playerAlias->alias;
                    $slot['playerAlias'] = $playerAlias;
                }
            break;
        }

        $slot['validation'] = [
            'valid' => is_null($error),
            'error' => $error
        ];

        $slot['type'] = $type;
        $slot['value'] = $value;

        return $slot;
    }

    public function findLinesByCoordinatesFromBlocks(
        Collection $blocks,
        ?array $anchorCoordinates = null,
        ?array $coordinates = null
    ): array {
        $lines = [];

        $bounds = [
            'top' => $coordinates['y'],
            'right' => $coordinates['x'] + $coordinates['width'],
            'bottom' => $coordinates['y'] + $coordinates['height'],
            'left' => $coordinates['x'],
        ];

        foreach ($blocks as $block) {
            $left = $block['dimensions']['tl']['x'];
            $top = $block['dimensions']['tl']['y'];

            $outOfBounds = $left < $bounds['left']  ||
                           $left > $bounds['right'] ||
                           $top  < $bounds['top']   ||
                           $top  > $bounds['bottom'];

            if ($outOfBounds) {
                continue;
            }

            $lines[] = $block['text'];
        }

        return $lines;
    }
}
