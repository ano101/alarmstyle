<script setup>
import { computed } from 'vue'

const props = defineProps({
  block: {
    type: Object,
    required: true
  }
})

const data = computed(() => props.block.data || {})
const url = computed(() => data.value.url || '')
const alt = computed(() => data.value.alt || '')
const width = computed(() => data.value.width || 'container')

const wrapperClass = computed(() => {
  return width.value === 'full'
    ? 'w-full px-4 sm:px-6 py-6 sm:py-8'
    : 'max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8'
})
</script>

<template>
  <div :class="wrapperClass">
    <img
      v-if="url"
      :src="url"
      :alt="alt"
      class="w-full h-auto rounded-lg"
      loading="lazy"
    />
  </div>
</template>

