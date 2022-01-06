<template>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Group
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Title
                </th>
                <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Lines
                </th> -->
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Value
                </th>
                <th scope="col" class="relative px-6 py-3">
                    Valid
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(slot, slotIndex) in slots" :key="slotIndex" :class="slotIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ slot.group }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ slot.title }}
                </td>
                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ slot.lines }}
                </td> -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ slot.value }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ slot.validation.error }}
                </td>
            </tr>
        </tbody>
    </table>
</template>


<script>
export default {
    props: {
        slots: Array
    }
}
</script>
