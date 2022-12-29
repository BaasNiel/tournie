<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientDecisionException;
use App\Services\ScoreboardMappingService;
use App\Services\ScoreboardGoogleService;
use App\Services\ScoreboardImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoreboardController extends Controller
{
    public function __construct(
        private ScoreboardGoogleService $scoreboardGoogleService,
        private ScoreboardImageService $scoreboardImageService,
        private ScoreboardMappingService $scoreboardMappingService
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
           'file' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        $scoreboard = $request->file('file') ?? null;
        if (!$scoreboard) {
            return response()->fail([
                'message' => 'Expected parameter "file" not found'
            ]);
        }

        $scoreboardContent = file_get_contents($scoreboard);
        $scoreboardPath = 'scoreboards/'.md5($scoreboardContent).'/scoreboard.'.$scoreboard->extension();

        if (!Storage::disk('public')->exists($scoreboardPath)) {
            Storage::disk('public')->put($scoreboardPath, $scoreboardContent);
        }

        return response()->success([
            'url' => Storage::url($scoreboardPath),
            'path' => $scoreboardPath
        ]);
    }

    public function getMapping(Request $request): JsonResponse
    {
        $request->validate([
            'scoreboardPath' => 'required|string'
        ]);

        $scoreboardPath = $request->get('scoreboardPath');

        // Get or fetch the scoreboard's stats
        $data = $this->scoreboardGoogleService->getData($scoreboardPath);

        $data['mapping'] = $this->scoreboardMappingService->findScoreboardMapping($scoreboardPath, $data);

        return response()->success($data);
    }
}
