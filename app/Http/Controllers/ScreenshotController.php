<?php

namespace App\Http\Controllers;

use App\Enums\Hero;
use App\Models\FileUpload;
use App\Models\Player;
use Exception;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScreenshotController extends Controller
{

    public function upload(Request $request)
    {
        $request->validate([
           'file' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        $file = $request->file('file') ?? null;
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'No file found'
            ]);
        }

        $content = file_get_contents($file);
        $filename = md5($content).'.'.$file->extension();
        $filepath = 'screenshots/'.$filename;

        if (!Storage::disk('local')->exists($filepath)) {
            Storage::disk('local')->put($filepath, $content);

            $fileUpload = new FileUpload;
            $fileUpload->name = $filename;
            $fileUpload->path = '/storage/'.$filepath;
            $fileUpload->save();
        }

        $stats = $this->convertImageToStats($filepath);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'stats' => $stats
        ]);
    }


    protected function convertImageToStats(string $filepath): array
    {
        $lines = $this->convertImageToTextLines($filepath);

        return $this->convertTextLinesToStats($lines);
    }

    protected function convertTextLinesToStats(Collection $lines): array
    {
        $lines = $lines->toArray();

        $heroes = [];
        $heroKey = null;
        $heroLines = [];
        foreach ($lines as $line) {
            $heroName = ucwords(strtolower($line));
            if (Hero::hasValue($heroName, false)) {
                $heroKey = Hero::getKey($heroName);
            }

            if (!is_null($heroKey)) {
                $heroes[] = [
                    'name' => $heroName,
                    'values' => $heroLines
                ];
                $heroLines = [];
                $heroKey = null;
                continue;
            }

            $heroLines[] = $line;
        }

        foreach ($heroes as &$hero) {
            $hero = $this->convertLinesToHeroStats($hero['name'], $hero['values']);
        }

        $gameStats = $this->convertLinesToGameStats($lines);

        return [
            'heroes' => $heroes,
            'game' => $gameStats
        ];
    }

    private function convertLinesToHeroStats(string $heroName, array $heroLines): array
    {
        $fields = implode(', ', $heroLines);

        $values = [];
        $player = null;
        while (count($heroLines)) {
            $heroLine = array_shift($heroLines);

            if (is_null($player)) {
                $player = Player::where('slug', Str::slug($heroLine))->first();
            }

            if (!is_null($player)) {
                $values[] = $heroLine;
            }
        }

        if (empty($values)) {
            throw new Exception("Could not find player in fields: ".json_encode($fields), 1);
        }

        $keys = $this->heroLinesToKeys($values);
        if (is_null($keys)) {
            throw new Exception("No keys found in config for: ".json_encode($values), 1);
        }

        $keys['hero'] = $heroName;

        return $keys;
    }

    private function heroLinesToKeys(array $heroLines): ?array {
        $types = config('snapshot.types');
        $columns = collect(config('snapshot.columns'));

        // Get columns based on fields
        $columns = $columns->filter(function ($columnNames) use ($heroLines) {
            // has the same column count
            return count($columnNames) === count($heroLines);
        })->filter(function ($columnNames) use ($heroLines, $types) {

            // satisfies the types
            $valid = true;
            foreach ($heroLines as $index => $heroLine) {
                if (!$valid) {
                    break;
                }

                $field = $columnNames[$index];
                $value = $heroLine;
                $type = $types[$field];

                switch ($type) {
                    case 'playerNames':
                        $valid = strlen($value) > 0;
                        break;
                    case 'clanNames':
                        $valid = strlen($value) > 0;
                        break;
                    case 'netWorth':
                        $value = intval(str_replace(',', '', $value));
                        $valid = $value > 5000;
                        break;
                    case 'rankCode':
                        // random string (might be the player's heroLevel actually)
                        $valid = strlen($value) > 0;
                        break;
                    case 'level':
                    case 'heroLevel':
                        $valid = $value > 0 && $value <= 30;
                        break;
                    case 'goldPerMinute':
                    case 'int':
                        $valid = $value > 0;
                        break;
                    default:
                        $valid = gettype($value) === $type;
                        break;
                }
            }

            return $valid;
        });

        if ($columns->isEmpty()) {
            return null;
        }

        $columns = $columns->shift();

        return array_combine($columns, $heroLines);
    }

    private function convertLinesToGameStats(array $lines): array {
        $stats = [];

        // Find the winner
        $side = null;
        foreach ($lines as $index => $line) {
            if (in_array(strtolower($line), ['radiant', 'dire'])) {
                $side = strtolower($line);
            }

            if (is_null($side)) {
                continue;
            }

            if ($line === 'WINNER') {
                $stats['winner'] = $side;
            }

            if (
                $line === 'SCORE:' &&
                isset($lines[$index + 1]) &&
                $lines[$index + 1] > 0
            ) {
                $stats[strtolower($side)] = intval($lines[$index + 1]);
            }
        }

        return $stats;
    }

    protected function convertImageToTextLines(string $filepath): Collection
    {
        $jsonFilepath = $filepath.'.json';

        if (Storage::disk('local')->exists($jsonFilepath)) {
            $lines = json_decode(Storage::disk('local')->get($jsonFilepath), true);
            $lines = collect($lines);
        } else {
            $lines = $this->getTextAnnotations($filepath);
            $lines = $this->filterLines($lines);
            $lines = $this->mergeHeroNameLines($lines);

            Storage::disk('local')->put($jsonFilepath, json_encode($lines->toArray()));
        }

        return $lines;
    }

    protected function filterLines(Collection $lines): Collection
    {
        $lines = $lines->filter(function ($line) {
            return !in_array(strtolower($line), [
                'backpack',
                'buffs',
            ]);
        })->map(function ($line) {
            if (
                Str::startsWith($line, ['[', '(']) &&
                Str::endsWith($line, [']', ')'])
            ) {
                $line = Str::substr($line, 1, -1);
            }

            if (Str::startsWith($line, ['[', '('])) {
                $line = Str::substr($line, 0, -1);
            }

            if (Str::endsWith($line, [']', ')'])) {
                $line = Str::substr($line, 0, -1);
            }

            return $line;
        })->filter(function ($line) {
            return !in_array(strtolower($line), [
                '/'
            ]);
        });

        return $lines;
    }

    protected function mergeHeroNameLines(Collection $lines): Collection
    {
        $lines = $lines->values()->toArray();
        $joinedLines = collect();

        $skipCount = 0;
        foreach ($lines as $index => $line) {

            $sliced = array_slice($lines, $index, 5);
            $heroName = $skipCount < 1
                ? Hero::findHeroName($sliced)
                : null;

            if (!is_null($heroName)) {
                $skipCount = 1 + Str::substrCount($heroName, ' ');
                $joinedLines->push($heroName);
            }

            if ($skipCount > 0) {
                $skipCount--;
                continue;
            }

            $joinedLines->push($line);
        }

        return $joinedLines;
    }

    protected function getTextAnnotations(string $filepath): Collection
    {
        $image = Storage::disk('local')->get($filepath);

        $client = new ImageAnnotatorClient([
            'credentials' => '/var/www/html/google-credentials.json'
        ]);

        $response = $client->textDetection($image);
        $textAnnotations = $response->getTextAnnotations();

        // Create filtered list of lines
        $lines = collect();
        foreach ($textAnnotations as $index => $textAnnotation) {

            // The first line is bullshit
            if ($index === 0) {
                continue;
            }

            $lines->push($textAnnotation->getDescription());
        }

        $client->close();

        return $lines;
    }
}
