<template>
    <Head title="Dashboard" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div
                        v-if="user"
                        class="p-6 bg-white border-b border-gray-200">
                        You're logged in!
                    </div>
                    <div
                        v-else
                        class="p-6 bg-white border-b border-gray-200">
                        Not logged in
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

        return {
            user,
        }
    },
}
</script>
