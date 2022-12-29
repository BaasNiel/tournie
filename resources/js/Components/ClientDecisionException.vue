<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="fixed z-10 inset-0 overflow-y-auto" @close="open = false">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <TransitionChild as="template" enter="ease-out duration-300"
                    enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                    leave-from="opacity-100 translate-y-0 sm:scale-100"
                    leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <ExclamationIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900">
                                        {{ clientDecisionException.message }}
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <!-- <p class="text-sm text-gray-500">
                                            Are you sure you want to deactivate your account? All of your data will
                                            be permanently removed. This action cannot be undone.
                                        </p> -->
                                        <p>
                                            Team: {{ clientDecisionException.slot.group }}<br />
                                            Field: {{ clientDecisionException.slot.title }}<br />
                                            Lines: {{ clientDecisionException.slot.lines }}<br />
                                        </p>
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ clientDecisionException.label }}
                                        </label>
                                        <input
                                            type="text"
                                            v-model="clientDecisionException.value"
                                            placeholder="..."
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="open = false"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="submit"
                                ref="cancelButtonRef"
                            >
                                Submit
                            </button>
                        </div>
                    </div>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- <div v-if="response.data.type === 'dropdown' && response.data.options">
        <Multiselect
            mode="tags"
            v-model="selectedOptions"
            :options="response.data.options"
        />

        <button v-if="selectedOptionsJoined" @click="createAlias">Create alias '{{ selectedOptionsJoined }}'</button>
    </div>

    <scoreboard-mapping
        v-if="response.data.type === 'mapping'"
        :response="response"
    />

    <div v-if="response.data.type === 'scoreboard-key-mapping'">

        <div v-for="(validation, validationIndex) in response.data.validationData.validations" :key="validationIndex">
            <h1>Validation attempt #{{ validationIndex }}</h1>
            <table>
                <thead>
                    <tr>
                        <th>index</th>
                        <th>key</th>
                        <th>value</th>
                        <th>type</th>
                        <th>error</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(column, columnIndex) in validation" :key="columnIndex">
                        <td>{{ column.index }}</td>
                        <td>{{ column.key }}</td>
                        <td>{{ column.value }}</td>
                        <td>{{ column.type }}</td>
                        <td>{{ column.error }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> -->
</template>

<script>
import Multiselect from '@vueform/multiselect'
import ScoreboardMapping from './ScoreboardMapping.vue';
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckIcon, ExclamationIcon } from '@heroicons/vue/outline'

export default {
    components: {
        Multiselect,
        ScoreboardMapping,
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        CheckIcon,
        ExclamationIcon,
    },

    props: {
        clientDecisionException: Object
    },

    data() {
        return {
            selected: null,
            selectedOptions: null,
            open: true
        }
    },

    computed: {
        selectedOptionsJoined() {
            return this.selectedOptions?.join(' ');
        },

        type() {
            return this.response?.data?.type
        }
    },

    methods: {
        submit() {
            let me = this
            const data = {
                action: 'createAlias',
                value: this.clientDecisionException.value
            }

            axios.post(this.clientDecisionException.action.endpoint, data)
                .then(function (res) {
                    console.log({
                        fn: 'createAlias',
                        response: res
                    });
                })
                .catch(function (err) {
                    me.output = err;
                });

        },
        createAlias() {
            const data = {
                action: 'createAlias',
                value: this.clientDecisionException.value
            }

            axios.post(this.response.data.action.endpoint, data)
                .then(function (res) {
                    console.log({
                        fn: 'createAlias',
                        response: res
                    });
                })
                .catch(function (err) {
                    me.output = err;
                });
        }
    }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
