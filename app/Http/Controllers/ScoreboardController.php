<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientDecisionException;
use App\Services\ScoreboardMappingService;
use App\Services\ScoreboardGoogleService;
use App\Services\ScoreboardImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScoreboardController extends Controller
{
    public function __construct(
        private ScoreboardGoogleService $scoreboardGoogleService,
        private ScoreboardImageService $scoreboardImageService,
        private ScoreboardMappingService $scoreboardMappingService
    ) {}

    public function upload(Request $request)
    {
        $request->validate([
           'file' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        $scoreboard = $request->file('file') ?? null;
        if (!$scoreboard) {
            return response()->json([
                'success' => false,
                'message' => 'No scoreboard image found'
            ]);
        }

        $scoreboardContent = file_get_contents($scoreboard);
        $scoreboardPath = 'scoreboards/'.md5($scoreboardContent).'/scoreboard.'.$scoreboard->extension();

        if (!Storage::disk('public')->exists($scoreboardPath)) {
            Storage::disk('public')->put($scoreboardPath, $scoreboardContent);
        }

        // $stats = $this->scoreboardImageService->convertToStats($scoreboardPath);

        $message = 'Do the mapping';
        throw new ClientDecisionException($message, [
            'action' => [
                'method' => 'POST',
                'endpoint' => '/client-exception/option'
            ],
            'urls' => [
                'image' => Storage::url($scoreboardPath),
            ],
            'scoreboardPath' => $scoreboardPath,
            'type' => 'mapping',
            'label' => 'Do the mapping!',
        ]);

        return response()->json([
            'success' => true,
            'urls' => [
                'image' => Storage::url($scoreboardPath),
            ]
        ]);
    }
}
