<script setup>
import { computed } from 'vue'

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const data = props.block.data || {}
const width = computed(() => data.width || 'container')

const wrapperClass = computed(() => {
  return width.value === 'full'
    ? 'w-full px-4 sm:px-6'
    : 'max-w-7xl mx-auto px-4 sm:px-6'
})
</script>

<template>
    <div :class="wrapperClass" class="py-8">
        <p class="leading-relaxed">
            {{ data.content }}
        </p>
    </div>
</template>
