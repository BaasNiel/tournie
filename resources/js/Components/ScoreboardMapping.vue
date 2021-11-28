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
                <div v-if="canvasBlock">
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

                    <BreezeLabel for="slotKey" value="Slot" />
                    <Multiselect
                        id="slotKey"
                        v-model="mapping.slotKey"
                        :options="mapping.slotKeys"
                    />

                    <BreezeButton
                        class="mt-5"
                        @click="saveSlot(canvasBlock)"
                    >
                        Save Slot
                    </BreezeButton>

                    <BreezeButton
                        class="mt-5"
                        @click="findTextFromCoordinates(canvasBlock)"
                    >
                        Find Text from Coordinates
                    </BreezeButton>

                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto overflow-y-scroll">
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
                // inputs
                x: 0,
                y: 0,
                width: 100,
                height: 100,

                // calculated
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
                cursor: 'default',
                dragX: 0,
                dragY: 0,
                drag: {
                    x: null,
                    y: null,
                    enabled: false,
                    lastEnabledAt: null
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
            const cursorMap = {
                'w-resize': 'col-resize',
                'e-resize': 'col-resize',
                'n-resize': 'row-resize',
                's-resize': 'row-resize',
                'ne-resize': 'nesw-resize',
                'sw-resize': 'nesw-resize',
                'nw-resize': 'nwse-resize',
                'se-resize': 'nwse-resize',
            }
            return {
                cursor: cursorMap[this.canvasBlock.cursor] ?? this.canvasBlock.cursor
            }
        },
    },

    watch: {
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
        },
        'canvasBlock.x': function (newValue, oldValue) {
            this.canvasBlockChanged('x', newValue, oldValue)
        },
        'canvasBlock.y': function (newValue, oldValue) {
            this.canvasBlockChanged('y', newValue, oldValue)
        },
        'canvasBlock.width': function (newValue, oldValue) {
            this.canvasBlockChanged('width', newValue, oldValue)
        },
        'canvasBlock.height': function (newValue, oldValue) {
            this.canvasBlockChanged('height', newValue, oldValue)
        },
    },

    methods: {
        canvasBlockChangedDraw(field, newValue, oldValue) {
            console.log('Console Block Diamaters changed')
            console.log(field)
            console.log(newValue)

            const me = this
            const top = me.canvasBlock.top
            const right = me.canvasBlock.right
            const bottom = me.canvasBlock.bottom
            const left = me.canvasBlock.left
            let lines = [
                'top: ' + top,
                'right: ' + right,
                'bottom: ' + bottom,
                'left: ' + left,
            ];

            me.canvasContext.beginPath();
            me.canvasContext.fillStyle = "white";
            lines.forEach((line, index) => {
                me.canvasContext.fillText(
                    line,
                    me.canvasBlock.right + 20,
                    me.canvasBlock.top + (index * 10)
                );
            })
            me.canvasContext.stroke();

        },

        canvasBlockChanged(field, newValue, oldValue) {
            if (typeof this.canvasBlockChangedDrawTimeout === 'number') {
                clearTimeout(this.canvasBlockChangedDrawTimeout);
                this.canvasBlockChangedDrawTimeout = undefined;
            }

            this.canvasBlockChangedDrawTimeout = setTimeout(() => {
                this.canvasBlockChangedDraw(field, newValue, oldValue)
                this.findTextFromCoordinates(this.canvasBlock);
            }, 1000)
        },

        screenshotCanvasMouseDown(e) {
            if (
                (this.mouse.x > this.canvasBlock.left) &&
                (this.mouse.y > this.canvasBlock.top) &&
                (this.mouse.x < this.canvasBlock.right) &&
                (this.mouse.y < this.canvasBlock.bottom)
            ) {
                this.canvasBlock.drag.x = this.mouse.x - this.canvasBlock.left;
                this.canvasBlock.drag.y = this.mouse.y - this.canvasBlock.top;
                this.canvasBlock.drag.enabled = true;
            }
        },

        screenshotCanvasMouseUp(e) {
            this.canvasBlock.drag.enabled = false;
            this.canvasBlock.drag.lastEnabledAt = Date.now();
        },

        canvasBlockMove() {
            this.canvasBlock.top = parseInt(this.mouse.y) - parseInt(this.canvasBlock.drag.y);
            this.canvasBlock.left = parseInt(this.mouse.x) - parseInt(this.canvasBlock.drag.x);
        },

        canvasBlockChangeCursor() {
            const margin = 5
            const x = this.mouse.x
            const y = this.mouse.y
            let cursor = 'default';

            if (
                (x > this.canvasBlock.left) &&
                (y > this.canvasBlock.top) &&
                (x < this.canvasBlock.right) &&
                (y < this.canvasBlock.bottom)
            ) {
                let margins = {
                    top: y < (this.canvasBlock.top + margin),
                    right: x > (this.canvasBlock.right - margin),
                    bottom: y > (this.canvasBlock.bottom - margin),
                    left: x < (this.canvasBlock.left + margin),
                }

                if (margins.top && margins.left) {
                    cursor = "nw-resize";
                } else if (margins.top && margins.right) {
                    cursor = "ne-resize";
                } else if (margins.bottom && margins.left) {
                    cursor = "sw-resize";
                } else if (margins.bottom && margins.right) {
                    cursor = "se-resize";
                } else if (margins.left) {
                    cursor = "w-resize";
                } else if (margins.right) {
                    cursor = "e-resize";
                } else if (margins.top) {
                    cursor = "n-resize";
                } else if (margins.bottom) {
                    cursor = "s-resize";
                } else {
                    cursor = "move";
                }
            }

            this.canvasBlock.cursor = cursor;
        },

        canvasBlockResize() {
            let directions = [];

            if (this.canvasBlock.cursor === "s-resize") {
                directions.push("down");
            }

            if (this.canvasBlock.cursor === "n-resize") {
                directions.push("up");
            }

            if (this.canvasBlock.cursor === "e-resize") {
                directions.push("right");
            }

            if (this.canvasBlock.cursor === "w-resize") {
                directions.push("left");
            }

            if (this.canvasBlock.cursor === "nw-resize") {
                directions.push("up");
                directions.push("left");
            }

            if (this.canvasBlock.cursor === "ne-resize") {
                directions.push("up");
                directions.push("right");
            }


            if (this.canvasBlock.cursor === "sw-resize") {
                directions.push("down");
                directions.push("left");
            }

            if (this.canvasBlock.cursor === "se-resize") {
                directions.push("down");
                directions.push("right");
            }

            directions.forEach((direction) => {
                this.canvasBlockResizeDirection(direction)
            })
        },

        canvasBlockResizeDirection(direction) {
            switch (direction) {
                case 'up':
                    let top = this.mouse.y;
                    let height = this.canvasBlock.bottom - top;
                    this.canvasBlock.top = top;
                    this.canvasBlock.height = height;
                    break;
                case 'right':
                    this.canvasBlock.width = this.mouse.x - this.canvasBlock.left;
                    break;
                case 'down':
                    this.canvasBlock.height = this.mouse.y - this.canvasBlock.top;
                    break;
                case 'left':
                    let left = this.mouse.x;
                    let width = this.canvasBlock.right - left;
                    this.canvasBlock.left = left;
                    this.canvasBlock.width = width;
                    break;
                default:
                    break;
            }
        },

        screenshotCanvasMouseMove(e) {
            this.mouse.x = e.offsetX;
            this.mouse.y = e.offsetY;

            if (this.canvasBlock.drag.enabled) {
                if (this.canvasBlock.cursor === 'move') {
                    this.canvasBlockMove();
                } else {
                    this.canvasBlockResize();
                }
            } else {
                this.canvasBlockChangeCursor();
            }
        },

        drawCanvasBlock() {
            const margin = 10
            let me = this;
            if (!me.canvasBlock) { return; };

            me.canvasContext.beginPath();
            me.canvasContext.strokeStyle = "white";
            me.canvasContext.rect(
                me.canvasBlock.left + (margin / 2),
                me.canvasBlock.top + (margin / 2),
                me.canvasBlock.width - margin,
                me.canvasBlock.height - margin,
            );

            me.canvasContext.fillStyle = "white";
            me.canvasContext.setLineDash([6, 3]);
            me.canvasContext.fillText(
                "Text: '"+me.canvasBlock.text+"'",
                me.canvasBlock.left + (margin / 2),
                me.canvasBlock.top + (margin / 2) - 5
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

            me.canvasBlock.x = me.canvasBlock.left;
            me.canvasBlock.y = me.canvasBlock.top;
            me.canvasBlock.right = me.canvasBlock.left + parseInt(me.canvasBlock.width);
            me.canvasBlock.bottom = me.canvasBlock.top + parseInt(me.canvasBlock.height);

            if (!me.canvasContext) { return; }
            if (!me.screenshotUrl) { return; }

            let image = new Image();
            image.src = me.screenshotUrl;
            image.onload = function() {
                me.height = image.height;
                me.width = image.width;
                me.canvasContext.drawImage(image, 0 ,0);

                // Queue the drawings
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
            axios.get('/screenshot/mapping/text/coordinates', {params: data})
                .then(function (res) {
                    me.mapping.slotKeys = res.data.slotKeys;
                    me.mapping.fieldTypes = res.data.fieldTypes;
                    const coordinates = {
                        x: res.data.textCoordinates.tl.x,
                        y: res.data.textCoordinates.tl.y,
                        width: (res.data.textCoordinates.tr.x - res.data.textCoordinates.tl.x),
                        height: (res.data.textCoordinates.bl.y - res.data.textCoordinates.tl.y),
                        text: me.form.text,
                    };

                    me.canvasBlock.x = coordinates.x;
                    me.canvasBlock.y = coordinates.y;
                    me.canvasBlock.left = me.canvasBlock.x;
                    me.canvasBlock.top = me.canvasBlock.y;
                    me.canvasBlock.width = coordinates.width;
                    me.canvasBlock.height = coordinates.height;
                    me.canvasBlock.text = coordinates.text;
                })
                .catch(function (err) {
                    me.output = err;
                });
        },

        findTextFromCoordinates: function(coordinates) {
            let me = this;

            // let me = this;
            const data = {
                screenshotPath: me.response?.data?.screenshotPath,
                anchorCoordinates: me.mapping.anchorCoordinates ?? null,
                textCoordinates: coordinates ?? me.textCoordinates,
            };

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();
            axios.get('/screenshot/mapping/text', {
                cancelToken: source.token,
                params: data
            }).then(function (res) {
                me.canvasBlock.foundText = null
                if (res.data?.strings?.strings && res.data.strings.strings.length) {
                    me.canvasBlock.foundText = res.data.strings.strings.join(", ")

                    me.canvasContext.beginPath();
                    me.canvasContext.fillStyle = "white";
                    res.data.strings.strings.forEach((line, index) => {
                        me.canvasContext.fillText(
                            line,
                            me.canvasBlock.left,
                            me.canvasBlock.bottom + (index * 15)
                        );
                    })
                    me.canvasContext.stroke();
                }
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
