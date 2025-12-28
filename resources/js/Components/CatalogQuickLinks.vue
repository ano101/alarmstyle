<script setup>
import Button from "./ui/Button.vue";

const props = defineProps({
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div v-if="links.length" class="flex flex-wrap gap-3 mb-6">
        <Button
            v-for="(btn, i) in links"
            :key="i"
            :href="btn.href"
            variant="ghost"
            class="bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 transition-all duration-200"
        >
            {{ btn.label }}
        </Button>
    </div>
</template>
