<script setup>
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
})

const expanded = ref(false)
// Всегда false до onMounted — важно для SSR hydration
const isMobile = ref(false)

const checkMobile = () => {
    isMobile.value = window.innerWidth < 640
    if (!isMobile.value) {
        expanded.value = false
    }
}

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkMobile)
})

const hasHidden = computed(() => props.items.length > 2)

// SSR + первый рендер: isMobile=false → показываем все (нет гидрации)
// После onMounted: если мобиле и > 2 пунктов — схлопываем
const visibleItems = computed(() => {
    const all = props.items.map((item, index) => ({ ...item, _index: index }))

    if (!isMobile.value || !hasHidden.value || expanded.value) {
        return all
    }

    return [
        { ...props.items[0], _index: 0 },
        { _ellipsis: true, _index: -1 },
        { ...props.items[props.items.length - 1], _index: props.items.length - 1 },
    ]
})
</script>

<template>
    <nav
        v-if="items.length"
        class="flex items-center gap-1.5 text-sm min-w-0 overflow-hidden"
        aria-label="Breadcrumb"
    >
        <template
            v-for="(item) in visibleItems"
            :key="item._ellipsis ? 'ellipsis' : (item.url || '') + ':' + item._index"
        >
            <!-- Троеточие -->
            <template v-if="item._ellipsis">
                <ChevronRight class="w-3.5 h-3.5 text-gray-300 shrink-0" />
                <button
                    type="button"
                    class="text-gray-400 hover:text-emerald-600 transition-colors px-0.5 shrink-0 leading-none tracking-widest"
                    aria-label="Показать весь путь"
                    @click="expanded = true"
                >
                    •••
                </button>
                <ChevronRight class="w-3.5 h-3.5 text-gray-300 shrink-0" />
            </template>

            <template v-else>
                <!-- Кликабельный пункт -->
                <Link
                    v-if="item.url && !item.current"
                    :href="item.url"
                    class="text-gray-500 hover:text-emerald-600 transition-colors whitespace-nowrap shrink-0"
                    preserve-scroll
                    preserve-state
                >
                    {{ item.label }}
                </Link>

                <!-- Текущий / некликабельный пункт -->
                <span
                    v-else
                    class="text-gray-900 font-medium truncate min-w-0"
                    :aria-current="item.current ? 'page' : undefined"
                    :title="item.label"
                >
                    {{ item.label }}
                </span>

                <!-- Разделитель -->
                <ChevronRight
                    v-if="item._index < items.length - 1"
                    class="w-3.5 h-3.5 text-gray-300 shrink-0"
                />
            </template>
        </template>
    </nav>
</template>
