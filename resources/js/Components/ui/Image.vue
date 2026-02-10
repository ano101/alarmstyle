<script setup>
import { useRoute } from '@/Composables/useRoute'

const route = useRoute()
const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    // пресет 1x, например "product.card"
    preset: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <img
        :src="route('images.show', { path: src, preset })"
        :srcset="[
      route('images.show', { path: src, preset }) + ' 1x',
      route('images.show', { path: src, preset: preset + '_2x' }) + ' 2x',
    ].join(', ')"
        :alt="alt"
        loading="lazy"
        v-bind="$attrs"
    />
</template>
