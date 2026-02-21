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
            class="mb-6 prose mb-6 mt-12 bg-white rounded-2xl p-8 sm:p-12 border border-gray-200"
            v-html="landing.content"
        />
        <div
            v-else-if="category && category.description"
            class="mb-6 prose mb-6 mt-12 bg-white rounded-2xl p-8 sm:p-12 border border-gray-200"
            v-html="category.description"
        />
    </section>
</template>
