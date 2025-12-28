<!-- Textarea.vue -->
<template>
  <textarea
      data-slot="textarea"
      :class="textareaClass"
      v-bind="attrs"
      :value="modelValue"
      @input="onInput"
  />
</template>

<script setup>
import { computed } from 'vue'
import { cn } from '@/utils'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'input'])

import { useAttrs } from 'vue'
const attrs = useAttrs()

const textareaClass = computed(() =>
    cn(
        'resize-none border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 aria-invalid:border-destructive flex field-sizing-content min-h-16 w-full rounded-md border bg-input-background px-3 py-2 text-base transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        attrs.class
    )
)

function onInput(event) {
    emit('update:modelValue', event.target.value)
    emit('input', event)
}
</script>
