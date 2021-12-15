<?php

namespace App\Services;

use App\Models\PlayerAlias;

class PlayerAliasService {

    public function find(array $lines): ?PlayerAlias
    {
        while (count($lines)) {
            $playerAlias = PlayerAlias::firstWhere('alias', implode(' ', $lines));

            if (!is_null($playerAlias)) {
                return $playerAlias;
            }

            array_pop($lines);
        }

        return null;
    }
}
