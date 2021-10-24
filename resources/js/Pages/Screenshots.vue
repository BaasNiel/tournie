<template>
    <Head title="Snapshots" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Snapshots
            </h2>
        </template>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Upload screenshot</div>

                        <div class="card-body">
                            <div v-if="success != ''" class="alert alert-success">
                                {{success}}
                            </div>

                            <form @submit="formSubmit" enctype="multipart/form-data">
                                <input type="file" class="form-control" v-on:change="onChange">
                                <button class="btn btn-primary btn-block">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <pre>{{ response }}</pre>
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
            let existingObj = this;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            let data = new FormData();
            data.append('file', this.file);

            axios.post('/screenshot', data, config)
                .then(function (res) {
                    existingObj.success = res.data.success;
                    me.response = res.data;
                })
                .catch(function (err) {
                    existingObj.output = err;
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
