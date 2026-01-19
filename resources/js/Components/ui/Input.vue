<!-- Input.vue -->
<script setup>
import { computed, useAttrs } from "vue";
import { cn } from "@/utils";

defineOptions({
    inheritAttrs: false,
});

const attrs = useAttrs();

const props = defineProps({
    type: {
        type: String,
        default: "text",
    },
    className: {
        type: String,
        default: "",
    },
    modelValue: {
        type: [String, Number],
        default: undefined,
    },
});

const emit = defineEmits(['update:modelValue']);

// Определяем, используется ли v-model
const isUsingVModel = computed(() => props.modelValue !== undefined);

const handleInput = (e) => {
    // Только emit если используется v-model
    if (isUsingVModel.value) {
        emit('update:modelValue', e.target.value);
    }
    // Если есть внешний @input обработчик, вызываем его
    if (attrs.onInput) {
        attrs.onInput(e);
    }
};
</script>

<template>
    <input
        v-bind="attrs"
        :type="props.type"
        :value="isUsingVModel ? modelValue : attrs.value"
        @input="handleInput"
        data-slot="input"
        :class="cn(
      'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground border-input flex h-9 w-full min-w-0 rounded-md border px-3 py-1 text-base bg-input-background transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
      'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
      'aria-invalid:ring-destructive/20 aria-invalid:border-destructive',
      props.className
    )"
    />
</template>
