<script setup>
import { computed } from 'vue'

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

const hasContent = computed(() => {
    return (props.landing && props.landing.content) ||
           (props.category && props.category.description)
})
</script>

<template>
    <section v-if="hasContent" class="mt-8">
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
