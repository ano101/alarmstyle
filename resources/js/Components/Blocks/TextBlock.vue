<script setup>
const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const data = props.block.data || {}
</script>

<template>
    <p class="leading-relaxed">
        {{ data.content }}
    </p>
</template>
