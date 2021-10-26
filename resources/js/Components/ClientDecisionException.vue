<template>
    <h1>Exception</h1>
    <p>{{ response.message }}</p>
    <code>
        {{ responsePretty }}
    </code>

    <p>{{ type }}</p>

    <dropdown>
        <option value="option1">Option1</option>
    </dropdown>
</template>

<script>
export default {
    props: ['response'],

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
    }
}
</script>
