<?php

namespace App\Http\Controllers;

use App\Enums\Hero;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
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
                $lines = [];
                foreach ($textAnnotations as $textAnnotation) {
                    $description = $textAnnotation->getDescription();

                    $separators = [
                        "\n",
                        '/',
                        '[',
                        ')',
                        ':',
                        '单',
                        'numeric_3',
                        'numeric_2',
                    ];
                    $lines = array_merge(
                        $lines,
                        $this->explodeArray($separators, $description)
                    );
                }

                $return['lines'] = $lines;

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
                    'key' => $heroKey,
                    'values' => $heroValues
                ];
                $heroValues = [];
                $heroKey = null;
                continue;
            }

            $heroValues[] = $line;
        }

        // $heroesOut = [];
        foreach ($heroes as &$hero) {
            $hero['keys'] = $this->arrayToHero($hero['values']);
        }

        dd($heroes);
        return $heroes;
    }

    private function getKeys(array $heroValues): ?array {
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
                        $valid = in_array($value, ['E']);
                        break;
                    case 'level':
                    case 'heroLevel':
                        $valid = $value > 0 && $value <= 30;
                        break;
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

    private function arrayToHero(array $heroValues): array
    {
        $keys = $this->getKeys($heroValues);

        if (is_null($keys)) {
            return [
                'message' => 'No keys for '.count($heroValues)
            ];
        }

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
