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
                <button class="btn btn-primary btn-block">Upload (updated G)</button>
            </form>
            <div v-if="success != ''">
                {{ success }}
            </div>
        </div>

        <div v-if="response">

            <div v-if="response.success == true">
                <h1>Response</h1>
                <pre>{{ response }}</pre>
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
import { computed } from '@vue/reactivity';
import ClientDecisionException from '@/Components/ClientDecisionException.vue';

export default {
    components: {
        Layout,
        Head,
        ClientDecisionException,
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
            console.log({
                fn: 'onChange'
            });
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

    // setup() {
    //     const user = computed(() => usePage().props.value.auth.user);

    //     return {
    //         user,
    //     }
    // },
}
</script>
