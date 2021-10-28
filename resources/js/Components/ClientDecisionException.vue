<template>
    <h1>Exception</h1>
    <p>{{ response.message }}</p>
    <p>{{ response.data.type }}</p>
    <div v-if="response.data.type === 'dropdown' && response.data.options">
        <select v-model="selected">
            <option v-for="(option, key) in response.data.options" :key="key">
                {{ option }}
            </option>
        </select>

        <button v-if="selected" @click="createAlias">Create alias '{{ selected }}'</button>
    </div>

    <div>
        <div v-if="response.data.type === 'screenshot-key-mapping'">
            {{ response.data.options }}
        </div>
    </div>
</template>

<script>
export default {
    props: ['response'],

    data() {
        return {
            selected: null,
        }
    },
    // props: {
    //     response: {
    //         type: Object,
    //         default: {}
    //     }
    // },

    computed: {
        responsePretty() {
            return JSON.stringify(this.response, null, 2);
        },

        type() {
            return this.response?.data?.type
        }
    },

    methods: {
        createAlias() {
            console.log('createAlias: '+this.selected)
            const data = {
                action: 'createAlias',
                value: this.selected
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
