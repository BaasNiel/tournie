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

        <!-- <div class="container">
            <div class="row mt-3">
                <form method="post" action="{{ route('submit') }}" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="image" class="form-label">Select image:</label>
                        <input type="file" id="image" name="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn btn-primary">Submit</button>
                </form>
            </div>
        </div> -->

        <!-- <div class="container">
            <file-drag-and-drop id="my-file" />
        </div> -->

        <!-- or with the option for a more robust message -->
        <!-- <file-drag-and-drop id="my-file">
            <img src="file-icon.svg">
            <strong>Upload File</strong>
        </file-drag-and-drop> -->

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
            success: ''
        };
    },

    methods: {
        onChange(e) {
            this.file = e.target.files[0];
        },
        formSubmit(e) {
            e.preventDefault();
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
