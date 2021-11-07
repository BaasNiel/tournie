<?php

namespace App\Http\Controllers;

use App\Enums\ScreenshotSlotKey;
use App\Services\ScreenshotDimensionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotMappingController extends Controller {

    public function __construct(
        private ScreenshotDimensionsService $screenshotDimensionsService
    ) { }

    public function findTextCoordinates(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $anchorCoordinates = json_decode($request->get('anchorCoordinates', null), true);
        $text = $request->get('text');

        $textCoordinates = $this->screenshotDimensionsService->findTextCoordinates(
            $screenshotPath,
            $text
        );

        // Only available slot is the anchor
        $slotKeys = [
            ScreenshotSlotKey::ANCHOR
        ];

        // Filter slots by config
        if ($anchorCoordinates) {
            $config = [];
            $path = 'config/screenshot-1.json';
            if (Storage::disk('local')->exists($path)) {
                $config = json_decode(Storage::disk('local')->get($path), true);
            }

            $configSlots = array_keys($config['anchors'][$anchorCoordinates['text']]['slots']) ?? [];
            $slotKeys = collect(config('screenshot.slots'))->filter(function ($slotKey) use ($configSlots) {
                return !in_array($slotKey, $configSlots);
            })->values();
        }

        return response()->json([
            'success' => true,
            'anchorCoordinates' => $anchorCoordinates,
            'textCoordinates' => $textCoordinates,
            'slotKeys' => $slotKeys,
        ]);
    }

    public function findTextFromCoordinates(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $anchorCoordinates = json_decode($request->get('anchorCoordinates', null), true);
        $textCoordinates = json_decode($request->get('textCoordinates', null), true);

        $strings = $this->screenshotDimensionsService->findTextFromCoordinates(
            $screenshotPath,
            $anchorCoordinates,
            $textCoordinates
        );

        return response()->json([
            'success' => true,
            'screenshotPath' => $screenshotPath,
            'strings' => $strings
        ]);
    }

    public function saveSlot(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $anchorCoordinates = $request->get('anchorCoordinates');
        $textCoordinates = $request->get('textCoordinates');
        $slotKey = ScreenshotSlotKey::getKey($request->get('slotKey'));

        $config = [];
        $path = 'config/screenshot-1.json';
        if (Storage::disk('local')->exists($path)) {
            $config = json_decode(Storage::disk('local')->get($path), true);
        }

        $textCoordinates['slotKey'] = $slotKey;

        if (ScreenshotSlotKey::ANCHOR === $slotKey) {
            $anchorCoordinates = $textCoordinates;
            $config['anchors'][$anchorCoordinates['text']] = $anchorCoordinates;
        } else if (!empty($anchorCoordinates)) {
            // Calculate relatives
            $textCoordinates['x'] -= $anchorCoordinates['x'];
            $textCoordinates['y'] -= $anchorCoordinates['y'];
        }

        $config['anchors'][$anchorCoordinates['text']]['slots'][$slotKey] = $textCoordinates;

        Storage::disk('local')->put($path, json_encode($config));

        return response()->json([
            'success' => true,
            'screenshotPath' => $screenshotPath,
            'anchorCoordinates' => $anchorCoordinates,
            'textCoordinates' => $textCoordinates,
            'slotKey' => $slotKey
        ]);
    }

    public function saveAnchor(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $textCoordinates = $request->get('textCoordinates');

        $config = [];
        $path = 'config/screenshot.json';
        if (Storage::disk('local')->exists($path)) {
            $config = json_decode(Storage::disk('local')->get($path), true);
        }

        $anchorKey = $textCoordinates['text'] ?? 'not-found';
        $config['anchors'][$anchorKey] = $textCoordinates;

        Storage::disk('local')->put($path, json_encode($config));

        return response()->json([
            'success' => true,
            'textCoordinates' => $textCoordinates,
            'config' => $config
        ]);
    }

    public function saveField(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $anchorCoordinates = $request->get('anchorCoordinates');
        $textCoordinates = $request->get('textCoordinates');
        $fieldType = $request->get('fieldType');

        $config = [];
        $path = 'config/screenshot.json';
        if (Storage::disk('local')->exists($path)) {
            $config = json_decode(Storage::disk('local')->get($path), true);
        }

        $anchorKey = $anchorCoordinates['text'] ?? 'not-found';

        if (!isset($config['anchors'][$anchorKey]['coordinates'])) {
            $config['anchors'][$anchorKey]['coordinates'] = [];
        }

        $textCoordinates['x'] -= $anchorCoordinates['x'];
        $textCoordinates['y'] -= $anchorCoordinates['y'];
        $textCoordinates['fieldType'] = $fieldType;

        $config['anchors'][$anchorKey]['coordinates'][] = $textCoordinates;

        Storage::disk('local')->put($path, json_encode($config));

        return response()->json([
            'success' => true,
            'textCoordinates' => $textCoordinates,
            'config' => $config,
        ]);
    }

}
