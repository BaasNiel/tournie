<template>

    <div class="container mx-auto w-full">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <BreezeLabel for="text" value="Text" />
                <BreezeInput
                    id="text"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.text"
                    required
                />
                <BreezeButton @click="findTextCoordinates" class="mt-5">
                    Find Text
                </BreezeButton>
            </div>
            <div>
                <div v-if="textCoordinates">
                    <BreezeLabel for="x" value="x" />
                    <BreezeInput
                        id="x"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="textCoordinates.x"
                    />

                    <BreezeLabel for="y" value="y" />
                    <BreezeInput
                        id="y"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="textCoordinates.y"
                    />

                    <BreezeLabel for="width" value="Width" />
                    <BreezeInput
                        id="width"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="textCoordinates.width"
                    />
                    <BreezeLabel for="height" value="height" />
                    <BreezeInput
                        id="height"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="textCoordinates.height"
                    />

                    <BreezeLabel for="slotKey" value="Slot" />
                    <Multiselect
                        id="slotKey"
                        v-model="mapping.slotKey"
                        :options="mapping.slotKeys"
                    />

                    <BreezeButton
                        class="mt-5"
                        @click="saveSlot(textCoordinates)"
                    >
                        Save Slot
                    </BreezeButton>

                    <BreezeButton
                        class="mt-5"
                        @click="findTextFromCoordinates()"
                    >
                        Find Text from Coordinates
                    </BreezeButton>

                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto overflow-y-scroll">
        <div>
            <BreezeLabel for="x" value="x" />
            <BreezeInput
                id="x"
                type="text"
                class="mt-1 block w-full"
                v-model="canvasBlock.x"
            />

            <BreezeLabel for="y" value="y" />
            <BreezeInput
                id="y"
                type="text"
                class="mt-1 block w-full"
                v-model="canvasBlock.y"
            />

            <BreezeLabel for="width" value="Width" />
            <BreezeInput
                id="width"
                type="text"
                class="mt-1 block w-full"
                v-model="canvasBlock.width"
            />
            <BreezeLabel for="height" value="height" />
            <BreezeInput
                id="height"
                type="text"
                class="mt-1 block w-full"
                v-model="canvasBlock.height"
            />
            <BreezeButton
                class="mt-5"
                @click="findTextFromCoordinates(canvasBlock)"
            >
                Find Text from Coordinates
            </BreezeButton>
        </div>
        <canvas
            ref="screenshotCanvas"
            :style="canvasStyle"
            :width="width"
            :height="height"
            @mousemove="screenshotCanvasMouseMove"
            @mousedown="screenshotCanvasMouseDown"
            @mouseup="screenshotCanvasMouseUp"
        />
    </div>

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

                slotKey: null,
                slotKeys: [],
            },

            mouse: {
                x: 0,
                y: 0,
            },
            canvasImages: [],
            canvasBlockSnapshot: null,
            canvasBlock: {
                x: 0,
                y: 0,
                width: 100,
                height: 100,
                cursor: 'default',
                dragX: 0,
                dragY: 0,
                drag: {
                    x: null,
                    y: null
                }
            },
        }
    },

    mounted() {
        this.refreshScreenshot();

        // Not sure why? But it works...
        this.refreshScreenshot();
    },

    computed: {
        screenshotUrl() {
            return this.response?.data?.urls?.image;
        },
        canvas: function () {
            return this.$refs?.screenshotCanvas;
        },
        canvasContext: function () {
            return this.canvas?.getContext('2d');
        },
        canvasStyle() {
            return {
                cursor: this.canvasBlock.cursor
            }
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
        canvasBlock: {
            deep: true,
            handler() {
                this.refreshScreenshot();
            }
        }
    },

    methods: {
        screenshotCanvasMouseDown(e) {
            const x = this.mouse.x;
            const y = this.mouse.y;
            if (
                (x > this.canvasBlock.x) &&
                (y > this.canvasBlock.y) &&
                (x < this.canvasBlock.x + this.canvasBlock.width) &&
                (y < this.canvasBlock.y + this.canvasBlock.height)
            ) {
                // x = mouse.x =
                this.canvasBlock.drag.x = this.mouse.x - this.canvasBlock.x;
                this.canvasBlock.drag.y = this.mouse.y - this.canvasBlock.y;
                this.canvasBlock.drag.enabled = true;
            }
        },

        screenshotCanvasMouseUp(e) {
            this.canvasBlock.drag.enabled = false;
        },

        screenshotCanvasMouseMove(e) {
            this.mouse.x = e.offsetX;
            this.mouse.y = e.offsetY;

            if (this.canvasBlock.drag.enabled) {
                this.canvasBlock.x = this.mouse.x - this.canvasBlock.drag.x;
                this.canvasBlock.y = this.mouse.y - this.canvasBlock.drag.y;
                return;
            }

            // Over the block
            if (
                (this.mouse.x > this.canvasBlock.x) &&
                (this.mouse.y > this.canvasBlock.y) &&
                (this.mouse.x < this.canvasBlock.x + this.canvasBlock.width) &&
                (this.mouse.y < this.canvasBlock.y + this.canvasBlock.height)
            ) {
                const margin = 10;
                if (this.mouse.x < (this.canvasBlock.x + margin)) {
                    this.canvasBlock.cursor = "w-resize";
                } else {
                    this.canvasBlock.cursor = "pointer";
                }

                return;
            }


            this.canvasBlock.cursor = "default";
        },

        drawCanvasBlock() {
            let me = this;
            if (!me.canvasBlock) { return; };

            me.canvasContext.beginPath();
            me.canvasContext.strokeStyle = "pink";
            me.canvasContext.rect(
                me.canvasBlock.x,
                me.canvasBlock.y,
                me.canvasBlock.width,
                me.canvasBlock.height,
            );

            me.canvasContext.fillStyle = "pink";
            me.canvasContext.setLineDash([6, 3]);
            me.canvasContext.fillText(
                "Block: '"+me.canvasBlock.text+"'",
                me.canvasBlock.x,
                me.canvasBlock.y - 5
            );
            me.canvasContext.stroke();
        },

        drawTextCoordinates() {
            let me = this;
            if (!me.textCoordinates) { return; };

            me.canvasContext.beginPath();
            me.canvasContext.strokeStyle = "green";
            me.canvasContext.rect(
                me.textCoordinates.x,
                me.textCoordinates.y,
                me.textCoordinates.width,
                me.textCoordinates.height,
            );

            me.canvasContext.fillStyle = "green";
            me.canvasContext.setLineDash([6, 3]);
            me.canvasContext.fillText(
                "Text: '"+me.textCoordinates.text+"'",
                me.textCoordinates.x,
                me.textCoordinates.y - 5
            );
            me.canvasContext.stroke();
        },

        drawFieldsCoordinates() {
            let me = this;

            if (!me.mapping.anchorCoordinates) { return; }
            if (!me.mapping.fieldsCoordinates) { return; }

            const colorMap = {
                ANCHOR: "lightblue",
                default: "white"
            };
            me.mapping.fieldsCoordinates.forEach((fieldCoordinates) => {
                let color = colorMap[fieldCoordinates.slotKey] ?? colorMap['default']

                let x = fieldCoordinates.x;
                let y = fieldCoordinates.y;
                if (fieldCoordinates.slotKey !== 'ANCHOR') {
                    x += me.mapping.anchorCoordinates.x;
                    y += me.mapping.anchorCoordinates.y;
                }

                me.canvasContext.beginPath();
                me.canvasContext.setLineDash([2]);
                me.canvasContext.strokeStyle = color;
                me.canvasContext.rect(
                    x,
                    y,
                    fieldCoordinates.width,
                    fieldCoordinates.height,
                );

                me.canvasContext.fillStyle = color;
                me.canvasContext.fillText(
                    "Field Type: '"+fieldCoordinates.slotKey+"'",
                    x,
                    y - 5
                );
                me.canvasContext.stroke();

            });

        },

        refreshScreenshot() {
            let me = this;
            if (!me.canvasContext) { return; }
            if (!me.screenshotUrl) { return; }

            let image = new Image();
            image.src = me.screenshotUrl;
            image.onload = function() {
                me.height = image.height;
                me.width = image.width;
                me.canvasContext.drawImage(image, 0 ,0);

                // Queue the drawings
                me.drawTextCoordinates();
                me.drawFieldsCoordinates();
                me.drawCanvasBlock();
            };
        },

        findTextCoordinates() {
            let me = this;
            const data = {
                screenshotPath: me.response?.data?.screenshotPath,
                anchorCoordinates: me.mapping.anchorCoordinates ?? null,
                text: me.form.text,
            };

            me.mapping.fieldType = [];
            me.mapping.fieldTypes = null;
            me.textCoordinates = null;
            axios.get('/screenshot/mapping/text/coordinates', {params: data})
                .then(function (res) {
                    me.mapping.slotKeys = res.data.slotKeys;
                    me.mapping.fieldTypes = res.data.fieldTypes;
                    me.textCoordinates = {
                        x: res.data.textCoordinates.tl.x,
                        y: res.data.textCoordinates.tl.y,
                        width: (res.data.textCoordinates.tr.x - res.data.textCoordinates.tl.x),
                        height: (res.data.textCoordinates.bl.y - res.data.textCoordinates.tl.y),
                        text: me.form.text,
                    };
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        findTextFromCoordinates(coordinates) {
            let me = this;
            const data = {
                screenshotPath: me.response?.data?.screenshotPath,
                anchorCoordinates: me.mapping.anchorCoordinates ?? null,
                textCoordinates: coordinates ?? me.textCoordinates,
            };

            axios.get('/screenshot/mapping/text', {params: data})
                .then(function (res) {
                    console.log({
                        res: res
                    })
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        saveSlot(textCoordinates) {
            let me = this;

            const data = {
                screenshotPath: me.response?.data?.screenshotPath,
                anchorCoordinates: me.mapping.anchorCoordinates,
                textCoordinates: textCoordinates,
                slotKey: me.mapping.slotKey,
            };

            axios.post('/screenshot/mapping/slot', data)
                .then(function (response) {
                    me.mapping.fieldsCoordinates.push(response.data.textCoordinates);
                    me.textCoordinates = null;

                    if (response.data.anchorCoordinates) {
                        me.mapping.anchorCoordinates = response.data.anchorCoordinates;
                    }
                })
                .catch(function (err) {
                    me.output = err;
                });
        },
    }
}
</script>
