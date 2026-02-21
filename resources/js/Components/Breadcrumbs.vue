<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <nav
        v-if="items.length"
        class="flex items-center gap-2 text-sm"
        aria-label="Breadcrumb"
    >
        <div
            v-for="(item, index) in items"
            :key="(item.url || '') + ':' + index"
            class="flex items-center gap-2"
        >

            <!-- Кликабельный пункт как Link -->
            <Link
                v-if="item.url && !item.current"
                :href="item.url"
                class="text-gray-600 hover:text-emerald-600 transition-colors"
                preserve-scroll
                preserve-state
            >
                {{ item.label }}
            </Link>

            <!-- Некликабельный / текущий пункт -->
            <span
                v-else
                class="text-gray-900 font-medium"
                :aria-current="item.current ? 'page' : undefined"
            >
                {{ item.label }}
            </span>

            <!-- Иконка между пунктами -->
            <ChevronRight
                v-if="index < items.length - 1"
                class="w-4 h-4 text-gray-400"
            />
        </div>
    </nav>
</template>
