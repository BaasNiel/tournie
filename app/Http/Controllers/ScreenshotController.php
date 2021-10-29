<?php

namespace App\Http\Controllers;

use App\Enums\Hero;
use App\Exceptions\ClientDecisionException;
use App\Models\FileUpload;
use App\Models\PlayerAlias;
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

        // print_r($lines->toArray());

        $ignore = config('snapshot.ignore');
        $replace = config('snapshot.replace');
        $lines = $lines->filter(function ($line) use ($ignore) {
            return !in_array($line, $ignore);
        })->map(function ($line) use ($replace) {
            if (isset($replace[$line])) {
                return $replace[$line];
            }

            return $line;
        });

        // print_r($lines->toArray());

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
            'game' => $gameStats,
            'heroes' => $heroes,
        ];
    }

    private function convertLinesToHeroStats(string $heroName, array $heroLines): array
    {
        $heroLinesOriginal = $heroLines;

        $values = [];
        $player = null;
        while (count($heroLines)) {
            $heroLine = array_shift($heroLines);
            $heroLinePlus = count($heroLines) ? $heroLine . ' ' . $heroLines[0] : null;

            if (is_null($player)) {

                $player = PlayerAlias::whereIn('slug', [
                    Str::slug($heroLinePlus),
                    Str::slug($heroLine)
                ])->first();

                // $player = PlayerAlias::where('slug', Str::slug($heroLine))->first();
            }

            if (!is_null($player)) {
                $values[] = $heroLine;
            }
        }

        if (empty($values)) {

            $options = collect($heroLinesOriginal)->filter(function ($value) {
                $value = str_replace(',', '', $value);

                if (Str::length($value) < 3) {
                    return false;
                }

                if (is_numeric($value)) {
                    return false;
                }

                return true;
            });

            $message = "Could not find player alias in fields: ".implode(', ', $heroLinesOriginal);
            throw new ClientDecisionException($message, [
                'action' => [
                    'method' => 'POST',
                    'endpoint' => '/client-exception/option'
                ],
                'type' => 'dropdown',
                'label' => 'Select the player alias',
                'options' => $options,
            ]);
        }

        $keys = $this->heroLinesToKeys($values);

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
            $message = "No keys found in config for: ".implode(', ', $heroLines);

            $types = config('snapshot.types');
            $columns = config('snapshot.columns');

            $mappings = collect($columns)->map(function ($mappingColumns) use ($heroLines) {
                $validationData = $this->validateHeroLines($heroLines);
                // foreach ($mappingColumns as $index => $mappingColumn) {
                //     $validations[] = [
                //         'index' => $index,
                //         'field' => $mappingColumn,
                //         'line' => $heroLines[$index] ?? null
                //     ];
                // }

                return [
                    'mapping' => array_combine($mappingColumns, array_splice($heroLines, 0, count($mappingColumns))),
                    'validationData' => $validationData
                ];
            });

            throw new ClientDecisionException($message, [
                'action' => [
                    'method' => 'POST',
                    'endpoint' => '/client-exception/option'
                ],
                'type' => 'screenshot-key-mapping',
                'label' => 'Select the player alias',
                'options' => $heroLines,
                'mappings' => $mappings->toArray(),
                'columns' => config('snapshot.types'),
            ]);
        }

        $columns = $columns->shift();

        return array_combine($columns, $heroLines);
    }

    private function validateHeroLines(array $heroLines): array {
        $types = config('snapshot.types');
        $columnMappings = collect(config('snapshot.columns'));

        $out = $columnMappings->map(function ($columnMapping, $columnMappingIndex) use ($heroLines, $types) {
            $map = [];
            foreach ($columnMapping as $columnMappingColumnIndex => $columnMappingColumn) {
                $error = false;
                $type = $types[$columnMappingColumn] ?? 'not-found';
                $value = $heroLines[$columnMappingColumnIndex] ?? null;

                switch ($type) {
                    case 'playerNames':
                        $error = strlen($value) > 0
                            ? null
                            : 'Expects length to be greater than zero';
                        break;
                    case 'clanNames':
                        $error = strlen($value) > 0
                            ? null
                            : 'Expects length to be greater than zero';
                        break;
                    case 'netWorth':
                        $value = intval(str_replace(',', '', $value));
                        $error = $value > 5000
                            ? null
                            : 'Expected value to be greater than 5000';
                        break;
                    case 'rankCode':
                        $error = strlen($value) > 0
                            ? null
                            : 'Expected length to be greater than zero';
                        break;
                    case 'level':
                    case 'heroLevel':
                        $error = $value > 0 && $value <= 30
                            ? null
                            : 'Expected value between 0 and 30';
                        break;
                    case 'goldPerMinute':
                    case 'int':
                        $error = $value > 0
                            ? null
                            : 'Expected value to be greater than zero';
                        break;
                    default:
                        $error = gettype($value) === $type
                            ? null
                            : 'Expected type of "'.$type.'" but found "'.gettype($value).'"';
                        break;
                }

                $map[] = [
                    'index' => $columnMappingColumnIndex,
                    'key' => $columnMappingColumn,
                    'type' => $type,
                    'value' => $value,
                    'error' => $error
                ];
            }

            return [
                'columnMappingIndex' => $columnMappingIndex,
                'columnMapping' => $columnMapping,
                'map' => $map
            ];
        });

        return $out->toArray();
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
                '/',
                '单'
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
            'credentials' => base_path(env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION')),
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
