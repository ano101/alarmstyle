<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";
import BlocksRenderer from "../../Components/BlocksRenderer.vue";

defineOptions({ layout: MainLayout })

const props = defineProps({
    page: {
        type: Object,
        required: true,
    },
})
</script>

<template>


    <BlocksRenderer :blocks="page.blocks || []" />
</template>
