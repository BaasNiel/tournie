<template>
    <Head title="Log" />

    <Layout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    ID
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Result
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Duration
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Radiant Team
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Dire Team
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="game in games" :key="game.id">
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    {{ game.id }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    <div v-if="game.radiant_win">
                                                        <span class="text-green-900"> Radiant Victory </span>
                                                    </div>
                                                    <div v-else>
                                                        <span class="text-red-900"> Dire Victory </span>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    {{ game.duration }}
                                                </td>

                                                <td class="px-6 py-3 text-sm font-medium text-gray-500">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex flex-shrink-0 -space-x-1">
                                                            <span
                                                                v-for="player in game.players"
                                                                :key="player.id">
                                                                <img
                                                                    v-if="player.player_slot <= 4"
                                                                    class="w-6 h-6 rounded-full max-w-none ring-2 ring-white" :src="player.image_url">
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-3 text-sm font-medium text-gray-500">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex flex-shrink-0 -space-x-1">
                                                            <span
                                                                v-for="player in game.players"
                                                                :key="player.id">
                                                                <img
                                                                    v-if="player.player_slot >= 5"
                                                                    class="w-6 h-6 rounded-full max-w-none ring-2 ring-white" :src="player.image_url">
                                                            </span>
                                                        </div>
                                                    </div>
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

    props: {
        games: Array,
    },

    setup(props) {
        console.log('props.games: ', props.games);
        return {};
        // const user = computed(() => usePage().props.value.auth.user);

        // return {
        //     user,
        // }
    },
}
</script>
