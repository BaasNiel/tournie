<template>
    <Head title="Create Player" />
    <Layout>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="ml-4 mt-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Create New Player
                        </h3>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form @submit.prevent="form.post(route('players.store'))" class="space-y-8 divide-y divide-gray-200">
                    <div class="space-y-8 divide-y divide-gray-200">
                        <div>
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="first-name" class="block text-sm font-medium text-gray-700">
                                        First name
                                    </label>
                                    <div class="mt-1">
                                        <input v-model="form.first_name" type="text" name="first-name" id="first-name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div v-if="form.errors.first_name">{{ form.errors.first_name }}</div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="last-name" class="block text-sm font-medium text-gray-700">
                                        Last name
                                    </label>
                                    <div class="mt-1">
                                        <input v-model="form.last_name" type="text" name="last-name" id="last-name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div v-if="form.errors.last_name">{{ form.errors.last_name }}</div>
                                </div>

                                <div class="sm:col-span-3 sm:col-start-1">
                                    <label for="handle" class="block text-sm font-medium text-gray-700">
                                        Handle
                                    </label>
                                    <div class="mt-1">
                                        <input v-model="form.handle" type="text" name="handle" id="handle" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div v-if="form.errors.handle">{{ form.errors.handle }}</div>
                                </div>

                                <div class="sm:col-span-3 sm:col-start-1">
                                    <label for="image-url" class="block text-sm font-medium text-gray-700">
                                        Avatar URL
                                    </label>
                                    <div class="mt-1 flex items-center">
                                        <div class="mr-2 w-full">
                                            <input v-model="form.image_url" type="text" name="image-url" id="image-url" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                        </div>
                                        <img v-if="form.image_url" class="h-14 w-16 rounded-full overflow-hidden bg-gray-100" :src="form.image_url">
                                        <div v-else class="h-14 w-16 rounded-full overflow-hidden bg-gray-100">
                                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.image_url">{{ form.errors.image_url }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <div class="flex justify-end">
                            <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </button>
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from '@/Layouts/Layout.vue'
import { Head, useForm } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        Layout,
    },

    setup() {
        const form = useForm({
            first_name: null,
            last_name: null,
            handle: null,
            image_url: null,
        })

        return { form }
    },
}
</script>
