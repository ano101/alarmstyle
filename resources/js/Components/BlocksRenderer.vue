<script setup>
import { defineAsyncComponent } from 'vue'

const props = defineProps({
    blocks: Array,
})

const resolveComponent = (type) => {
    if (!type) return null

    // products_slider → ProductsSliderBlock
    const name = type
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join('') + 'Block'

    return defineAsyncComponent(() =>
        import(`@/Components/Blocks/${name}.vue`)
    )
}
</script>


<template>
    <div class="space-y-6">
        <template v-for="(block, index) in blocks" :key="index">
            <component
                :is="resolveComponent(block.type)"
                v-if="resolveComponent(block.type)"
                :block="block"
            />
            <!-- Фолбэк для неизвестных/старых блоков -->
            <pre
                v-else
                class="text-xs bg-gray-50 p-2 rounded border border-dashed"
            >
Unknown block type: {{ block.type }}
{{ block }}
      </pre>
        </template>
    </div>
</template>
