<?php

namespace App\Http\Controllers;

use App\Enums\Hero;
use App\Models\Player;
use Exception;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SnapshotController extends Controller
{
    /**
     * open the view.
     *
     * @param
     * @return void
     */
    public function index()
    {
        return view('snapshots');
    }

    /**
     * handle the image
     *
     * @param
     * @return void
     */
    public function submit(Request $request)
    {
        if ($request->file('image')) {
            $return = [];
            $image = file_get_contents($request->file('image'));
            $imageMd5 = md5($image);

            Storage::disk('local')->put($imageMd5.'.png', $image);

            if (Storage::disk('local')->exists($imageMd5.'.json')) {
                $return = json_decode(Storage::disk('local')->get($imageMd5.'.json'), true);
                $return['source'] = 'local';
            } else {
                $return['source'] = 'google';

                $client = new ImageAnnotatorClient([
                    'credentials' => '/var/www/html/google-credentials.json'
                ]);

                $response = $client->textDetection($image);
                $textAnnotations = $response->getTextAnnotations();

                // Create filtered list of lines
                $lines = collect();
                foreach ($textAnnotations as $index => $textAnnotation) {

                    // skip the first line
                    if ($index === 0) {
                        continue;
                    }

                    $lines->push($textAnnotation->getDescription());
                }

                $exclude = [
                    'backpack',
                    'buffs'
                ];
                $lines = $lines->filter(function ($line) use ($exclude) {
                    return !in_array(strtolower($line), $exclude);
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
                });

                $exclude = [
                    '/'
                ];
                $lines = $lines->filter(function ($line) use ($exclude) {
                    return !in_array(strtolower($line), $exclude);
                });

                $return['lines'] = $this->mergeHeroNameLines($lines)->toArray();

                $client->close();
                Storage::disk('local')->put(
                    $imageMd5.'.json',
                    json_encode($return, JSON_PRETTY_PRINT)
                );
            }

            $stats = $this->textToStats($return['lines']);
            echo json_encode($stats);die();
        }
    }

    // Merge the hero name lines
    private function mergeHeroNameLines(Collection $lines): Collection {
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

    private function textToStats(array $lines): array
    {
        $heroes = [];
        $heroKey = null;
        $heroValues = [];
        foreach ($lines as $line) {
            $heroName = ucwords(strtolower($line));
            if (Hero::hasValue($heroName, false)) {
                $heroKey = Hero::getKey($heroName);
            }

            if (!is_null($heroKey)) {
                $heroes[] = [
                    'name' => $heroName,
                    'values' => $heroValues
                ];
                $heroValues = [];
                $heroKey = null;
                continue;
            }

            $heroValues[] = $line;
        }

        foreach ($heroes as &$hero) {
            $hero = $this->getHeroStats($hero['name'], $hero['values']);
        }

        $gameStats = $this->getGameStats($lines);

        return [
            'heroes' => $heroes,
            'game' => $gameStats
        ];
    }

    private function getGameStats(array $lines): array {
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

    private function heroValuesToKeys(array $heroValues): ?array {
        $types = config('snapshot.types');
        $columns = collect(config('snapshot.columns'));

        // Get columns based on fields
        $columns = $columns->filter(function ($columnNames) use ($heroValues) {
            // has the same column count
            return count($columnNames) === count($heroValues);
        })->filter(function ($columnNames) use ($heroValues, $types) {

            // satisfies the types
            $valid = true;
            foreach ($heroValues as $index => $heroValue) {
                if (!$valid) {
                    break;
                }

                $field = $columnNames[$index];
                $value = $heroValue;
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

        return array_combine($columns, $heroValues);
    }

    private function getHeroStats(string $heroName, array $heroValues): array
    {
        $fields = implode(', ', $heroValues);

        $values = [];
        $player = null;
        while (count($heroValues)) {
            $heroValue = array_shift($heroValues);

            if (is_null($player)) {
                $player = Player::where('slug', Str::slug($heroValue))->first();
            }

            if (!is_null($player)) {
                $values[] = $heroValue;
            }
        }

        if (empty($values)) {
            throw new Exception("Could not find player in fields: ".json_encode($fields), 1);
        }

        $keys = $this->heroValuesToKeys($values);
        if (is_null($keys)) {
            throw new Exception("No keys found in config for: ".json_encode($values), 1);
        }

        $keys['hero'] = $heroName;

        return $keys;
    }

    private function explodeArray(array $separators, mixed $strings): array
    {
        if (!is_array($strings)) {
            $strings = [$strings];
        }

        if (empty($separators)) {
            return $strings;
        }

        $map = [
            '[' => ']',
            ']' => '[',
            '(' => ')',
            ')' => '(',
        ];

        $separator = array_shift($separators);
        $stringsOut = [];
        foreach ($strings as $string) {

            if ($separator === '单' && strpos($string, '单') !== false) {
                $string = str_replace('单', '单 ', $string);
                $separator = ' ';
            }

            if ($separator === 'numeric_3') {
                $parts = collect(explode(' ', $string))->filter(function ($value) {
                    return is_numeric($value) && $value > 0;
                });
                if ($parts->count() === 3) {
                    $separator = ' ';
                }
            }

            if ($separator === 'numeric_2') {
                $parts = collect(explode(' ', $string))->filter(function ($value) {
                    return is_numeric($value) && $value > 0;
                });
                if ($parts->count() === 2) {
                    $separator = ' ';
                }
            }


            if (isset($map[$separator])) {
                $string = str_replace($map[$separator], '', $string);
            }

            $stringsOut = array_merge(
                $stringsOut,
                explode($separator, trim($string))
            );
        }
        return $this->explodeArray($separators, $stringsOut);
    }
}
