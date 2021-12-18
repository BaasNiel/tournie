<?php

namespace App\Services;

use App\Enums\Hero;
use App\Exceptions\ClientDecisionException;
use App\Models\PlayerAlias;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ScoreboardImageService
{
    public function __construct(
        private ScoreboardGoogleService $scoreboardGoogleService
    ) {}

    public function convertToStats(string $scoreboardPath): array
    {
        $data = $this->scoreboardGoogleService->getData($scoreboardPath);
        $lines = collect($data['lines']);
        $heroes = $this->getHeroes($lines);
        $stats = $this->getStats($lines);

        return [
            'lines' => $lines->values(),
            'heroes' => $heroes->values(),
            'stats' => $stats,
            'data' => $data
        ];
    }

    private function getStats(Collection $lines): array
    {
        $lines = $lines->values();
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

    private function getHeroes(Collection $lines): Collection
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

        return collect($heroes);
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
                'options' => array_values($options->toArray()),
            ]);
        }

        return [
            'heroName' => $heroName,
            'stats' => $this->mapHeroLineToStats($values)
        ];
    }

    private function mapHeroLineToStats(array $heroLines): ?array {
        $validationData = $this->validateHeroLines($heroLines);

        if (!empty($validationData['mapping'])) {
            return $validationData['mapping'];
        }

        $message = "No keys found in config for: ".implode(', ', $heroLines);
        throw new ClientDecisionException($message, [
            'action' => [
                'method' => 'POST',
                'endpoint' => '/client-exception/option'
            ],
            'type' => 'scoreboard-key-mapping',
            'label' => 'No mapping found',
            'validationData' => $validationData
        ]);
    }

    private function validateHeroLines(array $heroLines): array {
        $mapping = null;
        $types = config('snapshot.types');
        $columnMappings = collect(config('snapshot.columns'));

        // To-do: Implement playerNamesMerge fn
        if (in_array($heroLines[0], ['Los', '|Dream'])) {
            $heroLines[0] = implode(' ', [array_shift($heroLines), $heroLines[0]]);
        }

        $validations = $columnMappings->map(function ($columnMapping, $columnMappingIndex) use ($heroLines, $types, &$mapping) {
            $hasErrors = false;
            $map = [];


            foreach ($columnMapping as $columnMappingColumnIndex => $columnMappingColumn) {
                $error = false;
                $type = $types[$columnMappingColumn] ?? 'not-found';
                $value = $heroLines[$columnMappingColumnIndex] ?? null;

                switch ($type) {
                    case 'playerNames':
                        $player = PlayerAlias::where('alias', $value)->first();
                        $error = $player
                            ? null
                            : 'Unable to find player name "'.$value.'"';
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
                        $value = intval(str_replace(',', '', $value));
                        $error = $value > 100
                            ? null
                            : 'Expected value to be greater than zero';
                        break;
                    case 'kills':
                    case 'int':
                        $error = is_numeric($value) && $value > 0
                            ? null
                            : 'Expected value to be greater than zero';
                        break;
                    default:
                        $error = gettype($value) === $type
                            ? null
                            : 'Expected type of "'.$type.'" but found "'.gettype($value).'"';
                        break;
                }

                if ($error !== null) {
                    $hasErrors = true;
                }

                $map[] = [
                    'index' => $columnMappingColumnIndex,
                    'key' => $columnMappingColumn,
                    'type' => $type,
                    'value' => $value,
                    'error' => $error
                ];
            }

            if (!$hasErrors) {
                $mapping = $map;
                return false;
            }

            return $map;
        });

        return [
            'mapping' => $mapping,
            'validations' => $validations->toArray()
        ];
    }

    /*
    private function getScoreboardLines(string $scoreboardPath): Collection
    {
        $lines = $this->scoreboardGoogleService->getLines($scoreboardPath);
        $lines = $this->filterLines($lines);
        $lines = $this->mergeHeroNameLines($lines);

        $ignore = config('snapshot.ignore');
        $replace = config('snapshot.replace');
        return $lines->filter(function ($line) use ($ignore) {
            return !in_array($line, $ignore);
        })->map(function ($line) use ($replace) {
            if (isset($replace[$line])) {
                return $replace[$line];
            }

            return $line;
        });
    }
    */

    private function filterLines(Collection $lines): Collection
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

    private function mergeHeroNameLines(Collection $lines): Collection
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
}
