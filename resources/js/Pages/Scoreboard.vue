<template>
    <Head title="Scoreboard" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Scoreboard
            </h2>
        </template>

        <form v-if="!response" @submit="formSubmit" enctype="multipart/form-data">
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-lg flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a screenshot of the scoreboard</span>
                                <input v-on:change="onChange" id="file-upload" name="file-upload" type="file" class="sr-only" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PNG, JPG, GIF up to 10MB
                        </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div v-else>

            <div v-if="response.success == true">

                <img :src="response.urls.image" alt="Image not found">

                <pre>{{response.stats.game}}</pre>

                <div class="grid grid-cols-11">
                    <div class="font-bold">clan</div>
                    <div class="font-bold">player</div>
                    <div class="font-bold">heroName</div>
                    <div class="font-bold">level</div>
                    <div class="font-bold">kills</div>
                    <div class="font-bold">deaths</div>
                    <div class="font-bold">assists</div>
                    <div class="font-bold">net_worth</div>
                    <div class="font-bold">last_hits</div>
                    <div class="font-bold">denies</div>
                    <div class="font-bold">gpm</div>
                </div>
                <div v-for="(scoreboardLine, scoreboardLineIndex) in response.stats.heroes" :key="scoreboardLineIndex">
                    <scoreboard-line :data="scoreboardLine"/>
                </div>
            </div>

            <div v-if="response.success == false">
                <client-decision-exception :response="response" />
            </div>
        </div>

    </Layout>
</template>

<script>
import Layout from '@/Layouts/Layout.vue'
import { Head, usePage } from '@inertiajs/inertia-vue3';
import ClientDecisionException from '@/Components/ClientDecisionException.vue';
import ScoreboardLine from '@/Components/ScoreboardLine.vue';

export default {
    components: {
        Layout,
        Head,
        ClientDecisionException,
        ScoreboardLine,
    },

    data() {
        return {
            response: null
        };
    },

    methods: {
        onChange(e) {
            let me = this;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            let data = new FormData();
            data.append('file', e.target.files[0]);

            axios.post('/scoreboard', data, config)
                .then(function (res) {
                    me.response = res.data;
                })
                .catch(function (err) {
                    me.output = err;
                });
        }
    }
}
</script>
