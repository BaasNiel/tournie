<?php

namespace App\Services;

use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ScoreboardGoogleService
{
    public function __construct(
        private ImageAnnotatorClient $imageAnnotatorClient
    ) { }

    public function __destruct()
    {
        $this->imageAnnotatorClient->close();
    }

    public function getData(string $scoreboardPath): array
    {
        $jsonFilepath = dirname($scoreboardPath).'/lines.json';

        // Caching load
        if (Storage::disk('public')->exists($jsonFilepath)) {
            $data = Storage::disk('public')->get($jsonFilepath);
            return json_decode($data, true);
        }

        $annotateImageResponse = $this->annotateImage($scoreboardPath);
        $data = $this->getScoreboardTextAnnotations($annotateImageResponse);

        // Caching save
        Storage::disk('public')->put($jsonFilepath, json_encode($data));

        return $data;
    }

    private function getScoreboardTextAnnotations(AnnotateImageResponse $annotateImageResponse): array
    {
        $textAnnotations = $annotateImageResponse->getTextAnnotations();

        $blocks = collect();
        $lines = collect();
        foreach ($textAnnotations as $textAnnotation) {
            $line = $textAnnotation->getDescription();
            $lines->push($line);

             # get bounds
            $vertices = $textAnnotation->getBoundingPoly()->getVertices();
            $sides = [
                'tl',
                'tr',
                'bl',
                'br'
            ];
            $dimensions = [];
            foreach ($vertices as $index => $vertex) {
                $side = $sides[$index] ?? 'no-side';
                $dimensions[$side] = [
                    'x' => $vertex->getX(),
                    'y' => $vertex->getY()
                ];
            }
            $blocks->push([
                'text' => $line,
                'dimensions' => $dimensions
            ]);
        }

        return [
            'lines' => $lines->values()->toArray(),
            'blocks' => $blocks->values()->toArray()
        ];
    }

    private function annotateImage(string $scoreboardPath): AnnotateImageResponse
    {
        $image = Storage::disk('public')->get($scoreboardPath);
        return $this->imageAnnotatorClient->textDetection($image);
    }
}
