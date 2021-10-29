<template>
    <h1>Exception</h1>
    <p>{{ response.message }}</p>
    <div v-if="response.data.type === 'dropdown' && response.data.options">
        <Multiselect
            mode="tags"
            v-model="selectedOptions"
            :options="response.data.options"
        />

        <button v-if="selectedOptionsJoined" @click="createAlias">Create alias '{{ selectedOptionsJoined }}'</button>
    </div>

    <div>
        <div v-if="response.data.type === 'screenshot-key-mapping'">
            <code>
                {{ response.data.mappings }}
            </code>
        </div>
    </div>
</template>

<script>
import Multiselect from '@vueform/multiselect'

export default {
    components: {
        Multiselect,
    },
    props: ['response'],

    data() {
        return {
            selected: null,
            selectedOptions: null
        }
    },

    computed: {
        responsePretty() {
            return JSON.stringify(this.response, null, 2);
        },

        selectedOptionsJoined() {
            return this.selectedOptions?.join(' ');
        },

        type() {
            return this.response?.data?.type
        }
    },

    methods: {
        createAlias() {
            const data = {
                action: 'createAlias',
                value: this.selectedOptionsJoined
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
