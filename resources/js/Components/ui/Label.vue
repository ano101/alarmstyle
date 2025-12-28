<!-- Label.vue -->
<script setup>
import { useAttrs } from "vue";
import { Label as LabelPrimitive } from "radix-vue";
import { cn } from "@/utils";

defineOptions({
    inheritAttrs: false,
});

const attrs = useAttrs();

const props = defineProps({
    // аналог htmlFor из React
    forHtml: {
        type: String,
        default: undefined,
    },
});
</script>

<template>
    <LabelPrimitive
        v-bind="attrs"
        data-slot="label"
        :for="props.forHtml || attrs.for"
        :class="cn(
      'flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50',
      attrs.class
    )"
    >
        <!-- теперь всё, что внутрь <Label> кладёшь, будет выводиться -->
        <slot />
    </LabelPrimitive>
</template>
