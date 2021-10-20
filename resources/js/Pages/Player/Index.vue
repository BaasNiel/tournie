<template>
    <Head title="Players" />

    <Layout>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="ml-4 mt-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Players
                        </h3>
                    </div>
                    <div class="ml-4 mt-2 flex-shrink-0">
                        <Link
                            :href="route('players.create')"
                            as="button"
                            class="
                                relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md
                                text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                            ">
                            New Player
                        </Link>
                    </div>
                </div>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="player in players" :key="player.id">
                    <a :href="player.path" class="block hover:bg-gray-50">
                        <div class="flex items-center px-4 py-4 sm:px-6">
                            <div class="min-w-0 flex-1 flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-12 w-12 rounded-full" :src="player.image_url" alt="" />
                                </div>
                                <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                    <div>
                                        <p class="font-medium text-gray-900 truncate">{{ player.handle }}</p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500">
                                            <span class="truncate">{{ player.name }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <ChevronRightIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </Layout>
</template>

<script>
import Layout from '@/Layouts/Layout.vue'
import { Head } from '@inertiajs/inertia-vue3'
import { CheckCircleIcon, ChevronRightIcon, MailIcon } from '@heroicons/vue/solid'
import { Link } from "@inertiajs/inertia-vue3"

export default {
    components: {
        Head,
        Layout,
        CheckCircleIcon,
        ChevronRightIcon,
        MailIcon,
        Link,
    },

    props: {
        players: Array,
    },

    setup(props) {
        console.log('props.players: ', props.players);
        // onMounted(() => { console.log('var: ', var); })

        return {};
    },
}
</script>
