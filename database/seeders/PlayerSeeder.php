<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $players = [
            [
                'handle' => 'BN',
                'first_name' => 'Niel',
                'last_name' => 'Sarvari',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/97/97d2c010b0204d61d05dc661cd06345c93410b33_full.jpg',
            ],
            [
                'handle' => 'Jazz',
                'first_name' => 'Jarryd',
                'last_name' => 'Mildenhall',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/c0/c0534ac545358d17acfce81e3e48c8505c0c1f42_full.jpg',
            ],
            [
                'handle' => 'Dimzozo',
                'first_name' => 'Dimitri',
                'last_name' => 'Tosoni',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/44/44366e422a22937667d0e5189bacad1e6cfca614_full.jpg',
            ],
            [
                'handle' => 'Sue_Hack',
                'first_name' => 'Michaela',
                'last_name' => 'Earle',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/57/57cdb0f6bceb3529addf5d6383d3e192014e864b_full.jpg',
            ],
            [
                'handle' => 'XBOCT',
                'first_name' => 'Ruan',
                'last_name' => 'Keyser',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/8b/8be73ed66b0ff415edf289dbeb003c566e692fc0_full.jpg',
            ],
            [
                'handle' => 'ImHere',
                'first_name' => 'Jaco',
                'last_name' => 'van Staden',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/items/570/bd8d16beefbe0e02884195eb2805a1c7a5ab2579.gif',
            ],
            [
                'handle' => 'MrMerns',
                'first_name' => 'Marnus',
                'last_name' => 'Heunis',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/93/938d9761a0e3a5c6ad84ae4246ea9bef44daea72_full.jpg',
            ],
            [
                'handle' => 'Ryon',
                'first_name' => 'Ryno',
                'last_name' => 'Muller',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/32/3209ae12003a7b0c8ddbc18c9a21cfbe00a14bd7_full.jpg',
            ],
            [
                'handle' => 'QwaQwaBoy',
                'first_name' => 'Henri',
                'last_name' => 'van Staden',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/e9/e98414bae63fc8eb2eb7934a45f4c98564e6700c_full.jpg',
            ],
            [
                'handle' => 'Delete',
                'first_name' => 'Wesley',
                'last_name' => 'Meiring',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/e7/e7cb07085670524de3207fd2d14fd63eb1bb1986_full.jpg',
            ],
            [
                'handle' => 'Wazab1',
                'first_name' => 'Hugo',
                'last_name' => 'Luus',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/items/579720/8b8d2b0a9741393dc767cad197e5631baae8756b.gif',
            ],
            [
                'handle' => 'Hendrik',
                'first_name' => 'Hendrik',
                'last_name' => 'Prinsloo',
                'image_url' => 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/03/03988b7226b45eca3130ce0f11ff43f9e09a4148_full.jpg',
            ],
        ];

        foreach ($players as $player) {
            Player::create($player);
        }
    }
}
