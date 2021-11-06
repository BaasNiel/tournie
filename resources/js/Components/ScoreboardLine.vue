<template>
    <div class="grid grid-cols-11">
        <div>{{ fields.clan }}</div>
        <div>{{ fields.player }}</div>
        <div>{{ data.heroName }}</div>
        <div>{{ fields.level }}</div>
        <div>{{ fields.kills }}</div>
        <div>{{ fields.deaths }}</div>
        <div>{{ fields.assists }}</div>
        <div>{{ fields.net_worth }}</div>
        <div>{{ fields.last_hits }}</div>
        <div>{{ fields.denies }}</div>
        <div>{{ fields.gpm }}</div>
    </div>
</template>

<script>
export default {
    props: [ 'data' ],

    computed: {
        fields() {
            let fields = {};

            this.data?.stats?.forEach((stat) => {
                fields[stat.key] = stat.value;
            });

            return fields;
        }
    },
}
</script>
