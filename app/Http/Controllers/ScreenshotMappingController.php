<?php

namespace App\Http\Controllers;

use App\Services\ScreenshotDimensionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotMappingController extends Controller {

    public function __construct(
        private ScreenshotDimensionsService $screenshotDimensionsService
    ) { }

    public function findText(Request $request)
    {
        $screenshotPath = $request->get('screenshotPath');
        $text = $request->get('text');

        $coordinates = $this->screenshotDimensionsService->findTextCoordinates(
            $screenshotPath,
            $text
        );

        // To-do: filter existing field types (based on anchorCoordinates)
        $fieldTypes = array_keys(config('screenshot.fieldTypes'));

        return response()->json([
            'success' => true,
            'screenshotPath' => $screenshotPath,
            'text' => $text,
            'coordinates' => $coordinates,
            'fieldTypes' => $fieldTypes
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
