<?php

namespace App\Http\Controllers;

use App\Enums\ScoreboardSlotKey;
use App\Services\ScoreboardMappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoreboardMappingController extends Controller {

    public function __construct(
        private ScoreboardMappingService $scoreboardMappingService
    ) { }

    public function findTextCoordinates(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $text = $request->get('text');

        $coordinates = $this->scoreboardMappingService->findTextCoordinates(
            $scoreboardPath,
            $text
        );

        if (is_null($coordinates)) {
            return response()->fail([
                'error' => 'Could not find text "'.$text.'"'
            ]);
        }

        $anchorCoordinates = null;
        $slots = $this->scoreboardMappingService->getAvailableSlots(
            $scoreboardPath,
            $anchorCoordinates
        );

        return response()->success([
            'coordinates' => $coordinates,
            'slots' => $slots
        ]);
    }

    public function findTextFromCoordinates(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $anchorCoordinates = json_decode($request->get('anchorCoordinates', null), true);
        $textCoordinates = json_decode($request->get('textCoordinates', null), true);

        $strings = $this->scoreboardMappingService->findTextFromCoordinates(
            $scoreboardPath,
            $anchorCoordinates,
            $textCoordinates
        );

        $slots = $this->scoreboardMappingService->getAvailableSlots(
            $scoreboardPath,
            $anchorCoordinates,
            $textCoordinates
        );

        return response()->json([
            'success' => true,
            'scoreboardPath' => $scoreboardPath,
            'strings' => $strings,
            'slots' => $slots
        ]);
    }

    public function saveSlot(Request $request)
    {
        $scoreboardPath = $request->get('scoreboardPath');
        $anchorCoordinates = $request->get('anchorCoordinates');
        $slotCoordinates = $request->get('slotCoordinates');
        $slotKey = ScoreboardSlotKey::fromKey($request->get('slotKey'));

        $response = $this->scoreboardMappingService->updateOrCreateSlot(
            $scoreboardPath,
            $anchorCoordinates,
            $slotCoordinates,
            $slotKey
        );

        $slots = $this->scoreboardMappingService->getAvailableSlots(
            $scoreboardPath,
            $anchorCoordinates,
            $slotCoordinates
        );

        $response['slots'] = $slots;

        return response()->json($response);

        $config = [];
        $path = 'config/scoreboard-1.json';
        if (Storage::disk('local')->exists($path)) {
            $config = json_decode(Storage::disk('local')->get($path), true);
        }

        $slotCoordinates['slotKey'] = $slotKey;

        if (ScoreboardSlotKey::ANCHOR === $slotKey) {
            $anchorCoordinates = $slotCoordinates;
            $config['anchors'][$anchorCoordinates['text']] = $anchorCoordinates;
        } else if (!empty($anchorCoordinates)) {
            // Calculate relatives
            $slotCoordinates['x'] -= $anchorCoordinates['x'];
            $slotCoordinates['y'] -= $anchorCoordinates['y'];
        }

        $config['anchors'][$anchorCoordinates['text']]['slots'][$slotKey] = $slotCoordinates;

        Storage::disk('local')->put($path, json_encode($config));

        return response()->success([
            'anchorCoordinates' => $anchorCoordinates,
            'slotCoordinates' => $slotCoordinates,
            'slotKey' => $slotKey
        ]);

        // return response()->json([
        //     'success' => true,
        //     'scoreboardPath' => $scoreboardPath,
        //     'anchorCoordinates' => $anchorCoordinates,
        //     'slotCoordinates' => $slotCoordinates,
        //     'slotKey' => $slotKey
        // ]);
    }

}
