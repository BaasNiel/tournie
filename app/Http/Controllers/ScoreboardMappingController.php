<?php

namespace App\Http\Controllers;

use App\Enums\ScoreboardSlotKey;
use App\Models\ScoreboardMapping;
use App\Models\ScoreboardMappingSlot;
use App\Services\ScoreboardMappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingController extends Controller {

    public function __construct(
        private ScoreboardMappingService $scoreboardMappingService
    ) { }

    public function getCoordinatesFromText(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $text = $request->get('text');

        $coordinates = $this->scoreboardMappingService->findCoordinatesFromText(
            $scoreboardPath,
            $text
        );

        if (is_null($coordinates)) {
            return response()->fail([
                'error' => 'Could not find text "'.$text.'"'
            ]);
        }

        return response()->success([
            'coordinates' => $coordinates
        ]);
    }

    public function getLinesFromCoordinates(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $anchorCoordinates = json_decode($request->get('anchorCoordinates', null), true);
        $coordinates = json_decode($request->get('coordinates', null), true);

        $lines = $this->scoreboardMappingService->findLinesFromCoordinates(
            $scoreboardPath,
            $anchorCoordinates,
            $coordinates
        );

        $keys = $this->scoreboardMappingService->getAvailableSlotKeys(
            $scoreboardPath,
            $anchorCoordinates
        );

        return response()->success([
            'lines' => $lines,
            'keys' => $keys
        ]);
    }

    public function saveSlot(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $anchorCoordinates = $request->get('anchorCoordinates');
        $slotCoordinates = $request->get('slotCoordinates');
        $slotKey = ScoreboardSlotKey::fromKey($request->get('slotKey'));

        // Clone anchor (if slotKey is anchor)
        if ($slotKey->value === ScoreboardSlotKey::ANCHOR) {
            $anchorCoordinates = $slotCoordinates;
        }

        $scoreboardMappingSlot = $this->scoreboardMappingService->updateOrCreateSlot(
            $scoreboardPath,
            $anchorCoordinates,
            $slotCoordinates,
            $slotKey
        );

        $scoreboardMapping = ScoreboardMapping::with('slots')->findOrFail($scoreboardMappingSlot->scoreboard_mapping_id);


        return response()->success([
            'anchorCoordinates' => $slotKey->value === ScoreboardSlotKey::ANCHOR
                ? $scoreboardMappingSlot
                : $anchorCoordinates,
            'scoreboardMapping' => $scoreboardMapping,
            'scoreboardMappingSlot' => $scoreboardMappingSlot
        ]);
    }

}
