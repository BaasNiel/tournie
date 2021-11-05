<?php

namespace App\Services;

use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ScreenshotGoogleService
{
    public function __construct(
        private ImageAnnotatorClient $imageAnnotatorClient
    ) { }

    public function __destruct()
    {
        $this->imageAnnotator->close();
    }

    public function getData(string $screenshotPath): array
    {
        $annotateImageResponse = $this->annotateImage($screenshotPath);

        $annotations = $this->getScreenshotTextAnnotations($annotateImageResponse);
        $document = $this->getScreenshotFullTextAnnotations($annotateImageResponse);

        return [
            'annotations' => $annotations->toArray(),
            'document' => $document,
        ];
    }

    private function getScreenshotFullTextAnnotations(AnnotateImageResponse $annotateImageResponse): array
    {
        $return = [];
        $fullTextAnnotation = $annotateImageResponse->getFullTextAnnotation();

        foreach ($fullTextAnnotation->getPages() as $page) {

            foreach ($page->getBlocks() as $block) {
                $block_text = '';
                foreach ($block->getParagraphs() as $paragraph) {
                    foreach ($paragraph->getWords() as $word) {
                        foreach ($word->getSymbols() as $symbol) {
                            $block_text .= $symbol->getText();
                        }
                        $block_text .= ' ';
                    }
                    $block_text .= "\n";
                }
                $return[] = $block_text;
            }

        }

        return $return;
    }

    private function getScreenshotTextAnnotations(AnnotateImageResponse $annotateImageResponse): Collection
    {
        $textAnnotations = $annotateImageResponse->getTextAnnotations();

        $lines = collect();
        foreach ($textAnnotations as $textAnnotation) {
            $lines->push($textAnnotation->getDescription());
        }

        return $lines;
    }

    private function annotateImage(string $screenshotPath): AnnotateImageResponse
    {
        $image = Storage::disk('public')->get($screenshotPath);
        return $this->imageAnnotatorClient->textDetection($image);
    }
}
