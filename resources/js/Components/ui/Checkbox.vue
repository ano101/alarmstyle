<!-- Checkbox.vue -->
<script setup>
import { computed, useAttrs } from "vue";
import { CheckboxRoot, CheckboxIndicator } from "radix-vue";
import { Check } from "lucide-vue-next";
import { cn } from '@/utils';


defineOptions({
    inheritAttrs: false,
});

const attrs = useAttrs();

const rootClass = computed(() =>
    cn(
        "peer  bg-input-background data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50",
        attrs.class,
    ),
);
</script>

<template>
    <CheckboxRoot
        v-bind="attrs"
        data-slot="checkbox"
        :class="rootClass"
    >
        <CheckboxIndicator
            data-slot="checkbox-indicator"
            class="flex items-center justify-center text-current transition-none"
        >
            <Check class="size-3.5" />
        </CheckboxIndicator>
    </CheckboxRoot>
</template>
