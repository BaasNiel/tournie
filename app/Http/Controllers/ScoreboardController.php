<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientDecisionException;
use App\Services\ScreenshotMappingService;
use App\Services\ScreenshotGoogleService;
use App\Services\ScreenshotImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoreboardController extends Controller
{
    public function __construct(
        private ScreenshotGoogleService $screenshotGoogleService,
        private ScreenshotImageService $screenshotImageService,
        private ScreenshotMappingService $screenshotMappingService
    ) {}

    public function upload(Request $request)
    {
        $request->validate([
           'file' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        $screenshot = $request->file('file') ?? null;
        if (!$screenshot) {
            return response()->json([
                'success' => false,
                'message' => 'No screenshot image found'
            ]);
        }

        $screenshotContent = file_get_contents($screenshot);
        $screenshotPath = 'screenshots/'.md5($screenshotContent).'/screenshot.'.$screenshot->extension();

        if (!Storage::disk('public')->exists($screenshotPath)) {
            Storage::disk('public')->put($screenshotPath, $screenshotContent);
        }

        // $stats = $this->screenshotImageService->convertToStats($screenshotPath);
        // $data = $this->screenshotMappingService->findShit($screenshotPath);

        $message = 'Do the mapping';
        throw new ClientDecisionException($message, [
            'action' => [
                'method' => 'POST',
                'endpoint' => '/client-exception/option'
            ],
            'urls' => [
                'image' => Storage::url($screenshotPath),
            ],
            'screenshotPath' => $screenshotPath,
            'type' => 'mapping',
            'label' => 'Do the mapping!',
        ]);

        return response()->json([
            'success' => true,
            'urls' => [
                'image' => Storage::url($screenshotPath),
            ]
        ]);
    }
}
