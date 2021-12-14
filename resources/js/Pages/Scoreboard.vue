<template>
    <Head title="Scoreboard" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Scoreboard
            </h2>
        </template>

        <div v-if="!response" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
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
                PNG, JPG up to 10MB
            </p>
            </div>
        </div>

        <div v-else>

            <div v-if="response.status === 'success'">

                <img :src="response.data.imageUrl" alt="Image not found">

                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Group
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Key
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                Lines
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(slot, slotIndex) in response.data.mapping.slots" :key="slotIndex" :class="slotIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ slot.group }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ slot.key }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ slot.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ slot.lines }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else>
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
