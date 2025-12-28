<script setup>
import { computed } from 'vue'
import Button from "./ui/Button.vue";
import { X } from 'lucide-vue-next';
const props = defineProps({
    /** Массив атрибутов с их значениями */
    attributes: {
        type: Array,
        default: () => [],
    },
    /** Массив выбранных slug'ов атрибутов */
    selectedSlugs: {
        type: Array,
        default: () => [],
    },
    /** Фильтры (price_from, price_to, sort, etc.) */
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const emit = defineEmits(['remove-attribute', 'clear-price', 'reset-all'])

function getValueSlug(value) {
    return (
        value.slug?.slug ??
        value.slug ??
        value.slug_string ??
        value.slug_value ??
        null
    )
}

/**
 * Чипы выбранных фильтров:
 *  - по атрибутам
 *  - по цене
 */
const selectedChips = computed(() => {
    const chips = []

    const selectedSet = new Set(props.selectedSlugs || [])

    // атрибуты
    for (const attr of props.attributes || []) {
        for (const val of attr.values || []) {
            const slug = getValueSlug(val)
            if (!slug) continue
            if (!selectedSet.has(slug)) continue

            chips.push({
                type: 'attribute',
                key: slug,
                label: `${attr.name}: ${val.value}`,
            })
        }
    }

    // цена
    const from = props.filters.price_from
    const to   = props.filters.price_to

    if (from || to) {
        let label = 'Цена: '
        if (from && to) {
            label += `${Number(from).toLocaleString('ru-RU')}–${Number(to).toLocaleString('ru-RU')} ₽`
        } else if (from) {
            label += `от ${Number(from).toLocaleString('ru-RU')} ₽`
        } else if (to) {
            label += `до ${Number(to).toLocaleString('ru-RU')} ₽`
        }

        chips.push({
            type: 'price',
            key: 'price',
            label,
        })
    }

    return chips
})

/** клик по чипу */
function removeChip(chip) {
    if (chip.type === 'attribute') {
        emit('remove-attribute', chip.key)
    } else if (chip.type === 'price') {
        emit('clear-price')
    }
}

function handleResetAll() {
    emit('reset-all')
}
</script>

<template>
    <!-- Чипы выбранных фильтров -->
    <div class="bg-white rounded-xl p-4 mb-6 border border-gray-200" v-show="selectedChips.length">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Активные фильтры:</span>
            <Button @click="handleResetAll" size="sm" variant="ghost">Очистить все</Button>
        </div>
        <div class="flex flex-wrap gap-2">
            <div
                v-for="chip in selectedChips"
                :key="chip.type + ':' + chip.key"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium"
            >
                {{ chip.label }}
                <button
                    @click="removeChip(chip)"
                    class="hover:bg-emerald-200 rounded-full p-0.5 transition"
                >
                    <X class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>
    </div>
</template>

