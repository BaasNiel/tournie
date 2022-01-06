<template>
    <Head title="Scoreboard" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Scoreboard
            </h2>
        </template>

        <div v-if="!response"
            class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                    aria-hidden="true">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Upload a screenshot of the scoreboard</span>
                        <input v-on:change="onChange" id="file-upload" name="file-upload" type="file" class="sr-only" />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">
                    PNG, JPG up to 10MB
                </p>
            </div>
        </div>

        <img v-if="scoreboard.url" :src="scoreboard.url" alt="Image not found">

        <scoreboard-table v-if="scoreboard.mapping" :slots="scoreboard.mapping.slots" />


        <div v-if="clientDecisionException">

            <div v-if="clientDecisionException.type === 'mapping'">
                <scoreboard-mapping :response="clientDecisionException"/>
            </div>
            <client-decision-exception v-else
                :clientDecisionException="clientDecisionException"
            />
        </div>

    </Layout>
</template>

<script>
import ApiBase from '@/Apis/ApiBase';
import Layout from '@/Layouts/Layout.vue'
import { Head } from '@inertiajs/inertia-vue3';
import ClientDecisionException from '@/Components/ClientDecisionException';
import ScoreboardTable from '@/Components/ScoreboardTable';
import ScoreboardMapping from '@/Components/ScoreboardMapping.vue';

export default {
    components: {
        Layout,
        Head,
        ClientDecisionException,
        ScoreboardTable,
        ScoreboardMapping
    },

    data() {
        return {
            response: null,
            scoreboard: {
                path: null,
                url: null,
                mapping: null
            },
            clientDecisionException: null
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
            ApiBase.post('/scoreboard', data, config)
                .then(function (res) {
                    me.scoreboard.path = res.data.data.path;
                    me.scoreboard.url = res.data.data.url;

                    me.getScoreboardMapping();
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        getScoreboardMapping() {
            let me = this
            let data = {
                scoreboardPath: this.scoreboard.path
            }

            ApiBase.get('/scoreboard/mapping', {params: data})
                .then(function (res) {
                    console.log({
                        res: res
                    })

                    if (res.data.status === 'success') {
                        me.scoreboard.mapping = res.data.data.mapping;
                    } else {
                        me.clientDecisionException = res.data.data;
                    }
                })
                .catch(function (err) {
                    console.log(err);
                });
        }
    }
}
</script>
