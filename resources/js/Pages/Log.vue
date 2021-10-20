<template>
    <Head title="Log" />

    <Layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Rank
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Player
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Games Played
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Games Won
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Games Captained
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Captain Wins
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Points
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(person, index) in people" :key="person.handle">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ index + 1 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full" :src="person.image" alt="" />
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ person.handle }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ person.real_name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>


                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ person.games_played }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ person.games_won }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ person.games_captained }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ person.captain_wins }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ person.points }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from '@/Layouts/Layout.vue'
import { Head, usePage } from '@inertiajs/inertia-vue3';
import { computed } from '@vue/reactivity';

export default {
    components: {
        Layout,
        Head,
    },

    setup() {
        const user = computed(() => usePage().props.value.auth.user);

        const people = [
            {
                handle: 'BN',
                real_name: 'Niel Sarvari',
                games_played: 8,
                games_won: 8,
                games_captained: 3,
                captain_wins: 3,
                points: 22,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/97/97d2c010b0204d61d05dc661cd06345c93410b33_full.jpg',
            },
            {
                handle: 'Jazz',
                real_name: 'Jarryd Mildenhall',
                games_played: 7,
                games_won: 7,
                games_captained: 3,
                captain_wins: 3,
                points: 20,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/c0/c0534ac545358d17acfce81e3e48c8505c0c1f42_full.jpg',
            },
            {
                handle: 'Dimzozo',
                real_name: 'Dimitri Tosoni',
                games_played: 7,
                games_won: 6,
                games_captained: 3,
                captain_wins: 3,
                points: 18,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/44/44366e422a22937667d0e5189bacad1e6cfca614_full.jpg',
            },
            {
                handle: 'Sue_Hack',
                real_name: 'Mich Fokweetwatshaarvan',
                games_played: 7,
                games_won: 6,
                games_captained: 2,
                captain_wins: 2,
                points: 16,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/57/57cdb0f6bceb3529addf5d6383d3e192014e864b_full.jpg',
            },
            {
                handle: 'XBOCT',
                real_name: 'Ruan Keyser',
                games_played: 7,
                games_won: 6,
                games_captained: 2,
                captain_wins: 1,
                points: 14,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/8b/8be73ed66b0ff415edf289dbeb003c566e692fc0_full.jpg',
            },
            {
                handle: 'ImHere',
                real_name: 'Jaco van Staden',
                games_played: 7,
                games_won: 5,
                games_captained: 1,
                captain_wins: 1,
                points: 12,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/items/570/bd8d16beefbe0e02884195eb2805a1c7a5ab2579.gif',
            },
            {
                handle: 'MrMerns',
                real_name: 'Marnus Heunis',
                games_played: 5,
                games_won: 5,
                games_captained: 0,
                captain_wins: 0,
                points: 10,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/93/938d9761a0e3a5c6ad84ae4246ea9bef44daea72_full.jpg',
            },
            {
                handle: 'Ryon',
                real_name: 'Ryno Muller',
                games_played: 8,
                games_won: 4,
                games_captained: 0,
                captain_wins: 0,
                points: 8,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/32/3209ae12003a7b0c8ddbc18c9a21cfbe00a14bd7_full.jpg',
            },
            {
                handle: 'QwaQwaBoy',
                real_name: 'Henri van Staden',
                games_played: 8,
                games_won: 2,
                games_captained: 1,
                captain_wins: 1,
                points: 6,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/e9/e98414bae63fc8eb2eb7934a45f4c98564e6700c_full.jpg',
            },
            {
                handle: 'Delete',
                real_name: 'Wesley Meiring',
                games_played: 5,
                games_won: 2,
                games_captained: 2,
                captain_wins: 0,
                points: 4,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/e7/e7cb07085670524de3207fd2d14fd63eb1bb1986_full.jpg',
            },
            {
                handle: 'Wazab1',
                real_name: 'Hugo Luus',
                games_played: 0,
                games_won: 0,
                games_captained: 0,
                captain_wins: 0,
                points: 0,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/items/579720/8b8d2b0a9741393dc767cad197e5631baae8756b.gif',
            },
            {
                handle: 'Hendrik',
                real_name: 'Hendrik Prinsloo',
                games_played: 100,
                games_won: 0,
                games_captained: 100,
                captain_wins: 0,
                points: 0,
                image: 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/03/03988b7226b45eca3130ce0f11ff43f9e09a4148_full.jpg',
            },

        ]

        return {
            user,
            people,
        }
    },
}
</script>
