<?php

namespace App\Http\Controllers;

use App\Models\PlayerAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientExceptionController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->get('action');
        $value = $request->get('value');

        switch ($action) {
            case 'createAlias':
                return $this->createAlias($request);
                break;

            default:
                # code...
                break;
        }

        return response()->json([
            'success' => true,
            'request' => [
                'action' => $action,
                'value' => $value,
            ]
        ]);
    }

    protected function createAlias(Request $request)
    {
        $value = $request->get('value');

        PlayerAlias::updateOrCreate([
            'slug' => Str::slug($value)
        ], [
            'alias' => $value
        ]);
    }
}
