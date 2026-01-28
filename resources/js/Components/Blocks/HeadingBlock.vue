<script setup>
import { computed } from 'vue'

const props = defineProps({
  block: {
    type: Object,
    required: true
  }
})

const data = computed(() => props.block.data || {})
const text = computed(() => data.value.text || '')
const level = computed(() => data.value.level || 'h2')
const width = computed(() => data.value.width || 'container')

const wrapperClass = computed(() => {
  return width.value === 'full'
    ? 'w-full px-4 sm:px-6'
    : 'max-w-7xl mx-auto px-4 sm:px-6'
})

const headingClasses = {
  h1: 'text-4xl md:text-5xl lg:text-6xl font-bold',
  h2: 'text-3xl md:text-4xl lg:text-5xl font-bold',
  h3: 'text-2xl md:text-3xl lg:text-4xl font-semibold',
  h4: 'text-xl md:text-2xl lg:text-3xl font-semibold'
}

const headingClass = computed(() => headingClasses[level.value] || headingClasses.h2)
</script>

<template>
  <div :class="wrapperClass" class="py-6 sm:py-8">
    <component
      :is="level"
      :class="headingClass"
      class="text-gray-900"
    >
      {{ text }}
    </component>
  </div>
</template>

