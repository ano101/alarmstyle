<script setup>
const props = defineProps({
    landing: {
        type: Object,
        default: null,
    },
    category: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <section class="mt-8">
        <div
            v-if="landing && landing.content"
            class="mb-6 prose max-w-none"
            v-html="landing.content"
        />
        <div
            v-else-if="category && category.description"
            class="mb-6 prose max-w-none text-sm text-gray-700"
            v-html="category.description"
        />
    </section>
</template>
