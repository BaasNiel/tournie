<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static player()
 * @method static static clan()
 * @method static static rank()
 * @method static static kills()
 * @method static static deaths()
 * @method static static assists()
 * @method static static net_worth()
 * @method static static last_hits()
 * @method static static denies()
 * @method static static level()
 * @method static static hero_level()
 * @method static static gpm()
 */
final class HeroField extends Enum
{
    const player = 'player';
    const clan = 'clan';
    const rank = 'rank';
    const kills = 'kills';
    const deaths = 'deaths';
    const assists = 'assists';
    const net_worth = 'net_worth';
    const last_hits = 'last_hits';
    const denies = 'denies';
    const level = 'level';
    const hero_level = 'hero_level';
    const gpm = 'gpm';
}
