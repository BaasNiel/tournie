<template>
    <BreezeButton @click="refreshScreenshot">
        Refresh Screenshot
    </BreezeButton>

    <div>
        <BreezeLabel for="text" value="Text" />
        <BreezeInput
            id="text"
            type="text"
            class="mt-1 block w-full"
            v-model="form.text"
            required
            autofocus
        />
        <BreezeButton @click="findText">
            Find Text
        </BreezeButton>
    </div>

    <div v-if="textCoordinates">


        <h1>Found coordinates for "{{ form.text }}"</h1>
        x: {{ textCoordinates.x }} <br/>
        y: {{ textCoordinates.y }} <br/>
        width: {{ textCoordinates.width }} <br/>
        height: {{ textCoordinates.height }} <br/>


        <BreezeButton @click="undoImage()">
            Undo Image
        </BreezeButton>

        <div v-if="!mapping.anchorCoordinates">
            <BreezeButton @click="saveAnchor(textCoordinates)">
                Save as Anchor
            </BreezeButton>
        </div>
        <div v-else>

            <BreezeLabel for="x" value="x" />
            <BreezeInput
                id="x"
                type="text"
                v-model="textCoordinates.x"
            />

            <BreezeLabel for="y" value="y" />
            <BreezeInput
                id="y"
                type="text"
                v-model="textCoordinates.y"
            />

            <BreezeLabel for="width" value="Width" />
            <BreezeInput
                id="width"
                type="text"
                v-model="textCoordinates.width"
            />
            <BreezeLabel for="height" value="height" />
            <BreezeInput
                id="height"
                type="text"
                v-model="textCoordinates.height"
            />

            <BreezeLabel for="fieldType" value="Field Type" />
            <Multiselect
                id="fieldType"
                v-model="mapping.fieldType"
                :options="mapping.fieldTypes"
            />

            <BreezeButton @click="saveField(textCoordinates)">
                Save as Field
            </BreezeButton>
        </div>
    </div>

    <canvas
        ref="screenshotCanvas"
        :width="width"
        :height="height"
        @mousemove="showCoordinates"
    />
</template>

<script>
import Multiselect from '@vueform/multiselect';
import BreezeButton from '@/Components/Button.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
export default {
    components: {
        Multiselect,
        BreezeButton,
        BreezeInput,
        BreezeLabel,
    },
    props: ['response'],

    data() {
        return {
            x: 0,
            y: 0,
            width: 0,
            height: 0,
            form: {
                text: 'Radiant'
            },
            textCoordinates: null,

            mapping: {
                anchorCoordinates: null,
                fieldsCoordinates: [],
                fieldType: null,
                fieldTypes: [],
            },

            canvasImages: []
        }
    },

    computed: {
        screenshotUrl() {
            return this.response?.data?.urls?.image;
        },
        responsePretty() {
            return JSON.stringify(this.response, null, 2)
        }
    },

    watch: {
        textCoordinates: {
            deep: true,
            handler() {
                this.refreshScreenshot();
            }
        },
        'mapping.anchorCoordinates': {
            deep: true,
            handler() {
                this.refreshScreenshot();
            }
        },
        'mapping.fieldsCoordinates': {
            deep: true,
            handler() {
                this.refreshScreenshot();
            }
        },
    },

    methods: {
        undoImage() {
            let me = this;
            let canvas = me.$refs.screenshotCanvas;
            let context = canvas.getContext("2d");
            if (me.canvasImages.length > 0) {
                let image = me.canvasImages.shift();
                context.putImageData(image, 0, 0);
            }
        },

        showCoordinates(e) {
            this.x = e.offsetX;
            this.y = e.offsetY;
        },

        drawTextCoordinates(canvas) {
            let me = this;
            let context = canvas.getContext("2d");

            if (!me.textCoordinates) { return; }

            context.beginPath();
            context.strokeStyle = "green";
            context.rect(
                me.textCoordinates.x,
                me.textCoordinates.y,
                me.textCoordinates.width,
                me.textCoordinates.height,
            );

            context.fillStyle = "green"
            context.fillText(
                "Text: '"+me.textCoordinates.text+"'",
                me.textCoordinates.x,
                me.textCoordinates.y - 5
            );
            context.stroke();
        },

        drawAnchorCoordinates(canvas) {
            let me = this;
            let context = canvas.getContext("2d");

            if (!me.mapping.anchorCoordinates) { return; }

            context.beginPath();
            context.strokeStyle = "red";
            context.rect(
                me.mapping.anchorCoordinates.x,
                me.mapping.anchorCoordinates.y,
                me.mapping.anchorCoordinates.width,
                me.mapping.anchorCoordinates.height,
            );

            context.fillStyle = "red"
            context.fillText(
                "Anchor: '"+me.mapping.anchorCoordinates.text+"'",
                me.mapping.anchorCoordinates.x,
                me.mapping.anchorCoordinates.y - 5
            );
            context.stroke();
        },

        drawFieldsCoordinates(canvas) {
            let me = this;
            let context = canvas.getContext("2d");

            if (!me.mapping.anchorCoordinates) { return; }
            if (!me.mapping.fieldsCoordinates) { return; }

            me.mapping.fieldsCoordinates.forEach((fieldCoordinates) => {
                context.beginPath();
                context.strokeStyle = "red";
                context.rect(
                    me.mapping.anchorCoordinates.x + fieldCoordinates.x,
                    me.mapping.anchorCoordinates.y + fieldCoordinates.y,
                    fieldCoordinates.width,
                    fieldCoordinates.height,
                );

                context.fillStyle = "red"
                context.fillText(
                    "Field Type: '"+fieldCoordinates.fieldType+"'",
                    me.mapping.anchorCoordinates.x + fieldCoordinates.x,
                    me.mapping.anchorCoordinates.y + fieldCoordinates.y - 5
                );
                context.stroke();

            });

        },

        refreshScreenshot() {
            let me = this;
            let canvas = me.$refs.screenshotCanvas;
            if (!canvas) { return; }
            if (!me.screenshotUrl) { return; }

            let context = canvas.getContext("2d");

            let image = new Image();
            image.src = me.screenshotUrl;
            image.onload = function() {
                me.height = image.height;
                me.width = image.width;
                context.drawImage(image, 0 ,0);

                // Queue the drawings
                me.drawTextCoordinates(canvas);
                me.drawAnchorCoordinates(canvas);
                me.drawFieldsCoordinates(canvas);
            };
        },

        findText() {
            let me = this;
            const data = {
                screenshotPath: me.response?.data?.data?.screenshotPath,
                text: me.form.text,
            };

            axios.get('/screenshot/mapping/text', {params: data})
                .then(function (res) {
                    me.mapping.fieldTypes = res.data.fieldTypes;
                    me.textCoordinates = {
                        x: res.data.coordinates.tl.x,
                        y: res.data.coordinates.tl.y,
                        width: (res.data.coordinates.tr.x - res.data.coordinates.tl.x),
                        height: (res.data.coordinates.bl.y - res.data.coordinates.tl.y),
                        text: me.form.text,
                    };
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        saveAnchor(textCoordinates) {
            let me = this;
            const data = {
                screenshotPath: me.response?.data?.data?.screenshotPath,
                textCoordinates: textCoordinates
            };

            axios.post('/screenshot/mapping/anchor', data)
                .then(function (response) {
                    me.mapping.anchorCoordinates = response.data.textCoordinates;
                    me.textCoordinates = null;
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        saveField(textCoordinates) {
            let me = this;

            const data = {
                screenshotPath: me.response?.data?.data?.screenshotPath,
                anchorCoordinates: me.mapping.anchorCoordinates,
                textCoordinates: textCoordinates,
                fieldType: me.mapping.fieldType
            };

            axios.post('/screenshot/mapping/field', data)
                .then(function (response) {
                    me.mapping.fieldsCoordinates.push(response.data.textCoordinates);
                    me.textCoordinates = null;
                })
                .catch(function (err) {
                    me.output = err;
                });
        },
    }
}
</script>
