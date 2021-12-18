<template>
    <div v-if="!mapping.anchorCoordinates" class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6">
            Anchor
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div>
                <label for="anchor-text" class="block text-sm font-medium text-gray-700">Find text</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                        type="text"
                        v-model="mapping.anchor.text"
                        name="anchor-text"
                        id="anchor-text"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                        placeholder="Search for text..."
                    />
                    <button
                        @click="findCoordinatesFromText('anchor')"
                        :disabled="!mapping.anchor.text.length"
                        type="button"
                        class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <SearchIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </button>
                </div>
                <div v-if="mapping.anchor.error" class="text-red-500 mt-1">{{ mapping.anchor.error }}</div>
            </div>
        </div>
        <div class="px-4 py-4 sm:px-6 flex justify-end">
            <button
                type="button"
                @click="saveSlot('ANCHOR')"
                :disabled="anchorSaveDisabled"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Save
            </button>
        </div>
    </div>
    <div v-else-if="mapping.slot.keys.length" class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6">
            Slots
            <span v-if="scoreboardMapping.slots">({{scoreboardMapping.slots.length}}/{{slotsTotal}})</span>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <label for="anchor-text" class="block text-sm font-medium text-gray-700">Find text</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input
                    type="text"
                    v-model="mapping.slot.text"
                    name="slot-text"
                    id="slot-text"
                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    placeholder="Search for text..."
                />
                <button
                    @click="findCoordinatesFromText('slot')"
                    :disabled="!mapping.slot.text.length"
                    type="button"
                    class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <SearchIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </button>
            </div>

            <div class="mt-2">
                <label for="slot-key" class="block text-sm font-medium text-gray-700">Type</label>
                <select
                    id="location"
                    name="location"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    v-model="mapping.slot.key"
                >
                    <option v-for="record in mapping.slot.keys" :key="record.key" :value="record.key">
                        {{ record.group }} / {{ record.title }}
                    </option>
                </select>
            </div>

            <!-- <div v-if="mapping.slot.keys" class="mt-1">
                <label for="slot-key" class="block text-sm font-medium text-gray-700">Type</label>
                <Multiselect
                    id="slot-key"
                    name="slot-key"
                    class="rounded-md shadow-sm"
                    v-model="mapping.slot.key"
                    :options="mapping.slot.keys"
                    label="title"
                    valueProp="key"
                />
            </div> -->
            <div v-if="mapping.slot.error" class="text-red-500 mt-1">{{ mapping.slot.error }}</div>
        </div>
        <div class="px-4 py-4 sm:px-6 flex justify-end">
            <button
                type="button"
                @click="saveSlot(mapping.slot.key)"
                :disabled="slotSaveDisabled"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Save
            </button>
        </div>
    </div>
    <div v-else class="bg-green-500 overflow-hidden shadow rounded-lg divide-y divide-gray-200">
        <div class="px-4 py-5 sm:px-6">
            Mapping complete!
        </div>
    </div>
    <div class="w-full overflow-y-scroll">
        <canvas
            ref="scoreboardCanvas"
            :style="canvasStyle"
            :width="canvasWidth"
            :height="canvasHeight"
            @mousemove="scoreboardCanvasMouseMove"
            @mousedown="scoreboardCanvasMouseDown"
            @mouseup="scoreboardCanvasMouseUp"
        />
    </div>

</template>

<script>
import Multiselect from '@vueform/multiselect';
import { SearchIcon, UsersIcon } from '@heroicons/vue/solid'
export default {
    components: {
        Multiselect,
        SearchIcon,
        UsersIcon,
    },

    props: ['response'],

    data() {
        return {
            // Canvas properties
            canvasWidth: 0,
            canvasHeight: 0,

            // Database state
            scoreboardMapping: {},

            // Client-side mapping
            mapping: {
                anchor: {
                    text: 'Radiant',
                    error: null,
                },

                slot: {
                    text: '',
                    error: '',
                    key: null,
                    keys: []
                },

                anchorCoordinates: null,
                fieldsCoordinates: [],

                slotKey: null,
                availableSlots: []
            },

            mouse: {
                x: 0,
                y: 0,
                cursor: 'default',
                drag: {
                    x: null,
                    y: null,
                    enabled: false
                }
            },

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
            },
            canvasBlockLines: [],

            // Temp (debug)
            refreshScoreboardCounter: 0,
            canvasBlockWatchCounter: 0,
        }
    },

    mounted() {
        this.refreshScoreboard();

        // Not sure why? But it works...
        this.refreshScoreboard();
    },

    computed: {
        slotsTotal() {
            const mapped = this.scoreboardMapping?.slots?.length || 0;
            const unmapped = this.mapping?.slot?.keys?.length || 0;
            return mapped + unmapped;
        },
        scoreboardUrl() {
            return this.response?.data?.urls?.image;
        },
        canvas: function () {
            return this.$refs?.scoreboardCanvas;
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
                cursor: cursorMap[this.mouse.cursor] ?? this.mouse.cursor
            }
        },
        anchorFindDisabled() {
            const input = this.mapping.anchor.text;

            return input === null || input.length === 0;
        },
        anchorSaveDisabled() {
            if (
                this.canvasBlockLines &&
                this.canvasBlockLines.length === 1 &&
                this.mapping.anchor.text === this.canvasBlockLines[0]
            ) {
                return false;
            }

            return true;
        },
        slotSaveDisabled() {
            return !this.mapping.slot.key
        }
    },

    watch: {
        scoreboardMapping: {
            deep: true,
            handler() {
                this.refreshScoreboard();
            }
        },

        canvasBlock: {
            deep: true,
            handler() {
                this.canvasBlockWatchCounter++;
                this.canvasBlockChanged();
            }
        },

        canvasBlockLines: function (newValue, oldValue) {
            this.canvasBlockLinesDraw()
        },
    },

    methods: {

        canvasBlockChanged() {
            let me = this;
            let hasMoved = false;

            if (me.canvasBlock.x !== me.canvasBlock.left) {
                me.canvasBlock.x = me.canvasBlock.left;
                hasMoved = true;
            }
            if (me.canvasBlock.y !== me.canvasBlock.top) {
                me.canvasBlock.y = me.canvasBlock.top;
                hasMoved = true;
            }
            if (me.canvasBlock.right !== (me.canvasBlock.left + parseInt(me.canvasBlock.width))) {
                me.canvasBlock.right = me.canvasBlock.left + parseInt(me.canvasBlock.width);
                hasMoved = true;
            }
            if (me.canvasBlock.bottom !== (me.canvasBlock.top + parseInt(me.canvasBlock.height))) {
                me.canvasBlock.bottom = me.canvasBlock.top + parseInt(me.canvasBlock.height);
                hasMoved = true;
            }

            if (hasMoved) {
                me.refreshScoreboard()
                return
            }

            if (typeof this.canvasBlockChangedDrawTimeout === 'number') {
                clearTimeout(this.canvasBlockChangedDrawTimeout);
                this.canvasBlockChangedDrawTimeout = undefined;
            }

            this.canvasBlockChangedDrawTimeout = setTimeout(() => {
                me.findLinesByCoordinates(me.canvasBlock);
            }, 500)
        },

        canvasBlockLinesDraw() {
            const me = this
            const left = me.canvasBlock.left + 10
            const top = me.canvasBlock.bottom + 10
            const count = me.canvasBlockLines.length

            if (count === 0) {
                return;
            }

            // Add box
            me.canvasContext.beginPath();
            me.canvasContext.fillStyle = "black";
            me.canvasContext.globalAlpha = 0.8;
            me.canvasContext.fillRect(
                left - 5,
                top - 10,
                me.canvasBlock.width - 10,
                (10 + (count * 10))
            );
            me.canvasContext.globalAlpha = 1;
            me.canvasContext.stroke();

            // Add lines
            if (me.canvasBlockLines.length) {
                me.canvasContext.beginPath();
                me.canvasContext.fillStyle = "white";
                me.canvasBlockLines.forEach((line, index) => {
                    me.canvasContext.fillText(
                        line,
                        left,
                        top + (index * 10)
                    );
                });
                me.canvasContext.stroke();
            }
        },

        scoreboardCanvasMouseDown(e) {
            if (
                (this.mouse.x > this.canvasBlock.left) &&
                (this.mouse.y > this.canvasBlock.top) &&
                (this.mouse.x < this.canvasBlock.right) &&
                (this.mouse.y < this.canvasBlock.bottom)
            ) {
                this.mouse.drag.x = this.mouse.x - this.canvasBlock.left;
                this.mouse.drag.y = this.mouse.y - this.canvasBlock.top;
                this.mouse.drag.enabled = true;
            }
        },

        scoreboardCanvasMouseUp(e) {
            this.mouse.drag.enabled = false;
        },

        canvasBlockMove() {
            this.canvasBlock.top = parseInt(this.mouse.y) - parseInt(this.mouse.drag.y);
            this.canvasBlock.left = parseInt(this.mouse.x) - parseInt(this.mouse.drag.x);
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

            this.mouse.cursor = cursor;
        },

        canvasBlockResize() {
            let directions = [];

            if (this.mouse.cursor === "s-resize") {
                directions.push("down");
            }

            if (this.mouse.cursor === "n-resize") {
                directions.push("up");
            }

            if (this.mouse.cursor === "e-resize") {
                directions.push("right");
            }

            if (this.mouse.cursor === "w-resize") {
                directions.push("left");
            }

            if (this.mouse.cursor === "nw-resize") {
                directions.push("up");
                directions.push("left");
            }

            if (this.mouse.cursor === "ne-resize") {
                directions.push("up");
                directions.push("right");
            }


            if (this.mouse.cursor === "sw-resize") {
                directions.push("down");
                directions.push("left");
            }

            if (this.mouse.cursor === "se-resize") {
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

        scoreboardCanvasMouseMove(e) {
            this.mouse.x = e.offsetX;
            this.mouse.y = e.offsetY;

            if (this.mouse.drag.enabled) {
                if (this.mouse.cursor === 'move') {
                    this.canvasBlockMove();
                } else {
                    this.canvasBlockResize();
                }
            } else {
                this.canvasBlockChangeCursor();
            }
        },

        drawCanvasBlock() {
            const margin = 0
            let me = this;
            if (!me.canvasBlock) { return; };

            me.canvasContext.beginPath();
            me.canvasContext.fillStyle = "white";
            me.canvasContext.globalAlpha = 0.2;
            me.canvasContext.fillRect(
                me.canvasBlock.left + (margin / 2),
                me.canvasBlock.top + (margin / 2),
                me.canvasBlock.width - margin,
                me.canvasBlock.height - margin,
            );
            me.canvasContext.globalAlpha = 1;
            me.canvasContext.stroke();

            me.canvasContext.beginPath();
            me.canvasContext.strokeStyle = "white";
            me.canvasContext.rect(
                me.canvasBlock.left + (margin / 2),
                me.canvasBlock.top + (margin / 2),
                me.canvasBlock.width - margin,
                me.canvasBlock.height - margin,
            );
            me.canvasContext.stroke();

            me.canvasContext.beginPath();
            me.canvasContext.fillStyle = "white";
            me.canvasContext.setLineDash([6, 3]);
            me.canvasContext.fillText(
                "Search Block",
                me.canvasBlock.left + (margin / 2),
                me.canvasBlock.top + (margin / 2) - 5
            );
            me.canvasContext.stroke();
        },

        drawScoreboardMapping() {
            let me = this;
            const slots = me.scoreboardMapping?.slots ?? [];
            const colorMap = {
                ANCHOR: "lightblue",
                default: "white"
            };

            slots.forEach((slot) => {
                let color = colorMap[slot.key] ?? colorMap['default']
                let coordinates = {
                    top: slot.top,
                    left: slot.left,
                    width: slot.width,
                    height: slot.height,
                }

                if (slot.key !== 'ANCHOR') {
                    coordinates.top = me.mapping.anchorCoordinates.top + slot.offset_y;
                    coordinates.left = me.mapping.anchorCoordinates.left + slot.offset_x;
                }

                me.canvasContext.beginPath();
                me.canvasContext.setLineDash([2]);
                me.canvasContext.strokeStyle = color;
                me.canvasContext.rect(
                    coordinates.left,
                    coordinates.top,
                    coordinates.width,
                    coordinates.height,
                );

                me.canvasContext.fillStyle = color;
                me.canvasContext.fillText(
                    slot.key,
                    coordinates.left,
                    coordinates.top - 5
                );
                me.canvasContext.stroke();
            });
        },

        refreshScoreboard() {
            let me = this;

            me.refreshScoreboardCounter++;

            me.canvasBlock.x = me.canvasBlock.left;
            me.canvasBlock.y = me.canvasBlock.top;
            me.canvasBlock.right = me.canvasBlock.left + parseInt(me.canvasBlock.width);
            me.canvasBlock.bottom = me.canvasBlock.top + parseInt(me.canvasBlock.height);

            if (!me.canvasContext) { return; }
            if (!me.scoreboardUrl) { return; }

            let image = new Image();
            image.src = me.scoreboardUrl;
            image.onload = function() {
                me.canvasWidth = image.width;
                me.canvasHeight = image.height;
                me.canvasContext.drawImage(image, 0 ,0);

                // Queue the drawings
                me.drawScoreboardMapping();
                me.drawCanvasBlock();
            };
        },

        updateCanvasBlock(coordinates) {
            // To-do: Check if this trigger's the watcher multiple times
            this.canvasBlock.x = coordinates.x;
            this.canvasBlock.y = coordinates.y;
            this.canvasBlock.left = this.canvasBlock.x;
            this.canvasBlock.top = this.canvasBlock.y;
            this.canvasBlock.width = coordinates.width;
            this.canvasBlock.height = coordinates.height;
            this.canvasBlock.text = coordinates.text;
        },

        findCoordinatesFromText(type) {
            let me = this;
            const params = {
                scoreboardPath: me.response?.data?.scoreboardPath,
                text: me.mapping[type].text
            };

            me.mapping[type].error = null;
            axios.get('/scoreboard/mapping/coordinates-from-text', {params: params})
                .then(function (res) {
                    const data = res.data?.data;

                    me.updateCanvasBlock({
                        x: data.coordinates.tl.x,
                        y: data.coordinates.tl.y,
                        width: (data.coordinates.tr.x - data.coordinates.tl.x),
                        height: (data.coordinates.bl.y - data.coordinates.tl.y),
                        text: me.mapping[type].text,
                    });
                })
                .catch(function (err) {
                    const data = err.response?.data?.data;
                    me.mapping[type].error = data.error;
                });
        },

        findLinesByCoordinates: function(coordinates) {
            let me = this;

            const data = {
                scoreboardPath: me.response?.data?.scoreboardPath,
                anchorCoordinates: me.mapping.anchorCoordinates ?? null,
                coordinates: coordinates
            };

            me.mapping.slot.key = null;
            axios.get('/scoreboard/mapping/lines-from-coordinates', {
                params: data
            }).then(function (response) {
                const data = response.data.data;
                me.canvasBlockLines = data.lines;
                me.mapping.slot.keys = data.keys;
            })
            .catch(function (err) {
                me.output = err;
            });
        },

        saveSlot(slotKey) {
            let me = this;

            const data = {
                scoreboardPath: me.response?.data?.scoreboardPath,
                anchorCoordinates: me.mapping.anchorCoordinates ?? null,
                slotCoordinates: me.canvasBlock,
                slotKey: slotKey ?? me.mapping.slotKey,
            };

            me.mapping.slot.key = null;
            axios.post('/scoreboard/mapping/slot', data)
                .then(function (response) {
                    const data = response.data.data;
                    me.mapping.anchorCoordinates = data.anchorCoordinates;
                    me.scoreboardMapping = data.scoreboardMapping;
                    me.mapping.slot.keys = data.keys;
                })
                .catch(function (err) {
                    me.output = err;
                });
        },
    }
}
</script>
