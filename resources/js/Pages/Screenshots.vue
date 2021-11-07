<template>
    <Head title="Snapshots" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Snapshots
            </h2>
        </template>

        <div class="container">
            <form @submit="formSubmit" enctype="multipart/form-data">
                <input type="file" v-on:change="onChange">
                <button class="btn btn-primary btn-block">Upload</button>
            </form>
            <div v-if="success != ''">
                {{ success }}
            </div>
        </div>

        <div v-if="response">

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
            name: '',
            file: '',
            success: '',
            response: null
        };
    },

    computed: {
        responsePretty() {
            return JSON.stringify(JSON.parse(this.response), null, 2);
        }
    },

    methods: {
        onChange(e) {
            this.file = e.target.files[0];
        },
        formSubmit(e) {
            e.preventDefault();
            let me = this;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            let data = new FormData();
            data.append('file', this.file);

            axios.post('/screenshot', data, config)
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
