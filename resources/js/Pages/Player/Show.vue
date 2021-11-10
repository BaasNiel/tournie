<template>
    <Head title="Welcome" />
    <Layout>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
    <h1 class="sr-only">Profile</h1>
    <!-- Main 3 column grid -->
    <div class="grid grid-cols-1 gap-4 items-start lg:grid-cols-3 lg:gap-8">
        <!-- Left column -->
        <div class="grid grid-cols-1 gap-4 lg:col-span-2">
            <!-- Welcome panel -->
            <section aria-labelledby="profile-overview-title">
                <div class="rounded-lg bg-white overflow-hidden shadow">
                    <h2 class="sr-only" id="profile-overview-title">Profile Overview</h2>
                    <div class="bg-white p-6">
                        <div class="sm:flex sm:items-center sm:justify-between">
                            <div class="sm:flex sm:space-x-5 sm:items-center">
                                <div class="flex-shrink-0">
                                    <img class="mx-auto h-20 w-20 rounded-full" :src="player.image_url" alt="">
                                </div>
                                <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                                    <p class="text-xl font-bold text-gray-900 sm:text-2xl">{{ player.handle }}</p>
                                    <p class="text-sm font-medium text-gray-600">{{ player.name }}</p>
                                </div>
                            </div>
                            <div class="mt-5 flex justify-center sm:mt-0 space-x-2">
                                <div>
                                    <a href="#" class="flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Edit
                                    </a>
                                </div>
                                <div>
                                    <a href="#" class="flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Delete
                                    </a>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="border-t border-gray-200 bg-gray-50 grid grid-cols-1 divide-y divide-gray-200 sm:grid-cols-3 sm:divide-y-0 sm:divide-x">

                        <div class="px-6 py-5 text-sm font-medium text-center">
                            <span class="text-gray-600">Currently Ranked</span>
                            {{ ' ' }}
                            <span class="text-gray-900">1st</span>
                        </div>

                        <div class="px-6 py-5 text-sm font-medium text-center">
                            <span class="text-gray-900">4</span>
                            {{ ' ' }}
                            <span class="text-gray-600">Achievements</span>
                        </div>

                        <div class="px-6 py-5 text-sm font-medium text-center">
                            <span class="text-gray-900">3</span>
                            {{ ' ' }}
                            <span class="text-gray-600">Records Held</span>
                        </div>
                    </div>
                </div>
            </section>
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Averages/Maximums
                </h3>
                <dl class="mt-5 grid grid-cols-1 rounded-lg bg-white overflow-hidden shadow divide-y divide-gray-200 md:grid-cols-5 md:divide-y-0 md:divide-x">
                    <div class="px-4 py-5 sm:p-4">
                        <dt class="text-base font-normal text-gray-900">
                            Winrate
                        </dt>
                        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                69%
                            </div>
                        </dd>
                    </div>

                    <div class="px-4 py-5 sm:p-4">
                        <dt class="text-base font-normal text-gray-900">
                            Kills
                        </dt>
                        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                7
                                <span class="ml-2 text-sm font-medium text-gray-500">
                                    10
                                </span>
                            </div>

                            <img class="h-7 w-7 mt-2" src="https://steamcdn-a.akamaihd.net/apps/dota2/images/heroes/luna_icon.png" alt="">
                        </dd>
                    </div>

                    <div class="px-4 py-5 sm:p-4">
                        <dt class="text-base font-normal text-gray-900">
                            Deaths
                        </dt>
                        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                10
                                <span class="ml-2 text-sm font-medium text-gray-500">
                                    21
                                </span>
                            </div>

                            <img class="h-7 w-7 mt-2" src="https://steamcdn-a.akamaihd.net/apps/dota2/images/heroes/mirana_icon.png" alt="">
                        </dd>
                    </div>

                    <div class="px-4 py-5 sm:p-4">
                        <dt class="text-base font-normal text-gray-900">
                            Assists
                        </dt>
                        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                12
                                <span class="ml-2 text-sm font-medium text-gray-500">
                                    21
                                </span>
                            </div>

                            <img class="h-7 w-7 mt-2" src="https://steamcdn-a.akamaihd.net/apps/dota2/images/heroes/riki_icon.png" alt="">
                        </dd>
                    </div>

                    <div class="px-4 py-5 sm:p-4">
                        <dt class="text-base font-normal text-gray-900">
                            Gold Per Minute
                        </dt>
                        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                420
                                <span class="ml-2 text-sm font-medium text-gray-500">
                                    869
                                </span>
                            </div>

                            <img class="h-7 w-7 mt-2" src="https://steamcdn-a.akamaihd.net/apps/dota2/images/heroes/alchemist_icon.png" alt="">
                        </dd>
                    </div>


                </dl>
            </div>

        </div>

        <!-- Right column -->
        <div class="grid grid-cols-1 gap-4">
            <!-- Announcements -->
            <section aria-labelledby="announcements-title">
                <div class="rounded-lg bg-white overflow-hidden shadow">
                    <div class="p-6">
                        <h2 class="text-base font-medium text-gray-900" id="announcements-title">Announcements</h2>
                        <div class="flow-root mt-6">
                            <ul role="list" class="-my-5 divide-y divide-gray-200">

                                <li class="py-5">
                                    <div class="relative focus-within:ring-2 focus-within:ring-cyan-500">
                                        <h3 class="text-sm font-semibold text-gray-800">
                                            <a href="#" class="hover:underline focus:outline-none">
                                                <!-- Extend touch target to entire panel -->
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                Office closed on July 2nd
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                            Cum qui rem deleniti. Suscipit in dolor veritatis sequi aut. Vero ut earum quis deleniti. Ut a sunt eum cum ut repudiandae possimus. Nihil ex tempora neque cum consectetur dolores.
                                        </p>
                                    </div>
                                </li>

                                <li class="py-5">
                                    <div class="relative focus-within:ring-2 focus-within:ring-cyan-500">
                                        <h3 class="text-sm font-semibold text-gray-800">
                                            <a href="#" class="hover:underline focus:outline-none">
                                                <!-- Extend touch target to entire panel -->
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                New password policy
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                            Alias inventore ut autem optio voluptas et repellendus. Facere totam quaerat quam quo laudantium cumque eaque excepturi vel. Accusamus maxime ipsam reprehenderit rerum id repellendus rerum. Culpa cum vel natus. Est sit autem mollitia.
                                        </p>
                                    </div>
                                </li>

                                <li class="py-5">
                                    <div class="relative focus-within:ring-2 focus-within:ring-cyan-500">
                                        <h3 class="text-sm font-semibold text-gray-800">
                                            <a href="#" class="hover:underline focus:outline-none">
                                                <!-- Extend touch target to entire panel -->
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                Office closed on July 2nd
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                            Tenetur libero voluptatem rerum occaecati qui est molestiae exercitationem. Voluptate quisquam iure assumenda consequatur ex et recusandae. Alias consectetur voluptatibus. Accusamus a ab dicta et. Consequatur quis dignissimos voluptatem nisi.
                                        </p>
                                    </div>
                                </li>

                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="#" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                View all
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Hires -->
            <section aria-labelledby="recent-hires-title">
                <div class="rounded-lg bg-white overflow-hidden shadow">
                    <div class="p-6">
                        <h2 class="text-base font-medium text-gray-900" id="recent-hires-title">Recent Hires</h2>
                        <div class="flow-root mt-6">
                            <ul role="list" class="-my-5 divide-y divide-gray-200">

                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1519345182560-3f2917c472ef?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                Leonard Krasner
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                @leonardkrasner
                                            </p>
                                        </div>
                                        <div>
                                            <a href="#" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </li>

                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                Floyd Miles
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                @floydmiles
                                            </p>
                                        </div>
                                        <div>
                                            <a href="#" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </li>

                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1502685104226-ee32379fefbe?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                Emily Selman
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                @emilyselman
                                            </p>
                                        </div>
                                        <div>
                                            <a href="#" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </li>

                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1500917293891-ef795e70e1f6?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                Kristin Watson
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                @kristinwatson
                                            </p>
                                        </div>
                                        <div>
                                            <a href="#" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="#" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                View all
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
    </Layout>
</template>

<script>
import Layout from '@/Layouts/Layout.vue'
import { Head } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        Layout,
    },

    props: {
        player: Object
    },

    setup() {

    },
}
</script>
