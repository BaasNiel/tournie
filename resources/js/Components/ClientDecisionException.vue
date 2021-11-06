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

    <scoreboard-mapping
        v-if="response.data.type === 'mapping'"
        :response="response"
    />

    <div v-if="response.data.type === 'screenshot-key-mapping'">

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
    </div>
</template>

<script>
import Multiselect from '@vueform/multiselect'
import ScoreboardMapping from './ScoreboardMapping.vue';

export default {
    components: {
        Multiselect,
        ScoreboardMapping
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
