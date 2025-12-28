<script setup>
import { computed, onMounted  } from 'vue'
import { Select,SelectItem,SelectContent,SelectTrigger,SelectValue } from "./ui/select.js";
import ProductCart from "./ProductCart.vue";
import Button from "./ui/Button.vue";
const props = defineProps({
    items: {
        type: Object,
        required: true, // paginator
    },
    categorySlug: {
        type: String,
        required: true,
    },
    selectedSlugs: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    attributes: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['navigate', 'open-filters'])

const sortOptions = [
    { value: 'popular_desc', label: 'По популярности' },
    { value: 'price_asc',    label: 'Сначала дешевле' },
    { value: 'price_desc',   label: 'Сначала дороже' },
]

const defaultSort = 'popular_desc'

const currentSort = computed(() => {
    const sort = props.filters?.sort
    const allowed = sortOptions.map(o => o.value)

    // если сортировка — нормальная строка из наших опций
    if (typeof sort === 'string' && allowed.includes(sort)) {
        return sort
    }

    // иначе по умолчанию
    return defaultSort
})


function buildBasePath(customSlugs = null) {
    const slugs = customSlugs ?? props.selectedSlugs
    if (!slugs.length) {
        return props.categorySlug
    }
    return props.categorySlug + '/' + slugs.join('/')
}

function buildCatalogUrl(path = '', query = {}) {
    let url = '/category'

    if (path) {
        url += '/' + path.replace(/^\/+/, '')
    }

    const qs = new URLSearchParams()

    Object.entries(query).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            qs.append(key, value)
        }
    })

    const q = qs.toString()
    if (q.length > 0) {
        url += '?' + q
    }

    return url
}

function sortUrl(value) {
    const basePath = buildBasePath()
    const query = { ...props.filters, sort: value }
    return buildCatalogUrl(basePath, query)
}

function navigateToSort(value) {
    const url = sortUrl(value)
    emit('navigate', url)
}

function goToPage(link) {
    if (!link.url || link.active) return
    emit('navigate', link.url)
}

function formatPrice(price) {
    if (price === null || price === undefined) return null
    return Math.round(price).toLocaleString('ru-RU') + ' ₽'
}


</script>

<template>
    <section class="flex-1">
        <div class="flex items-center justify-between gap-3 mb-6">
            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive bg-background text-foreground hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 has-[>svg]:px-3 lg:hidden border-2 border-gray-200 hover:border-emerald-600"
                @click="$emit('open-filters')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal w-5 h-5 mr-2" data-fg-b4ig99="1.34:81.464:/src/app/components/Catalog.tsx:498:21:19341:46:e:SlidersHorizontal::::::BJAT"><line x1="21" x2="14" y1="4" y2="4"></line><line x1="10" x2="3" y1="4" y2="4"></line><line x1="21" x2="12" y1="12" y2="12"></line><line x1="8" x2="3" y1="12" y2="12"></line><line x1="21" x2="16" y1="20" y2="20"></line><line x1="12" x2="3" y1="20" y2="20"></line><line x1="14" x2="14" y1="2" y2="6"></line><line x1="8" x2="8" y1="10" y2="14"></line><line x1="16" x2="16" y1="18" y2="22"></line></svg>
                Фильтры
            </button>

            <!-- Сортировка -->
            <Select
                :modelValue="currentSort"
                @update:modelValue="navigateToSort"
            >
                <SelectTrigger class="w-full sm:w-56 h-11 border-2 border-gray-200 rounded-xl">
                <SelectValue />
                </SelectTrigger>

                <SelectContent>
                    <SelectItem
                        v-for="option in sortOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </SelectContent>
            </Select>

        </div>

        <!-- GRID -->
        <!-- СКЕЛЕТЫ -->
        <div
            v-if="loading"
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6"
        >
            <div
                v-for="n in 8"
                :key="n"
                class="rounded-2xl border border-slate-100 bg-white overflow-hidden animate-pulse"
            >
                <div class="p-4 space-y-3">
                    <div class="h-4 bg-slate-200 rounded w-4/5"></div>
                    <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                    <div class="h-9 bg-slate-200/80 rounded-xl w-full mt-4"></div>
                </div>
            </div>
        </div>

        <!-- РЕАЛЬНЫЕ ТОВАРЫ -->
        <div
            v-else
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-8"
        >
            <template v-if="items.data && items.data.length">
                <ProductCart v-for="item in items.data" :key="item.id" :product="item"/>
            </template>

            <p
                v-else
                class="text-sm text-slate-500"
            >
                Товары не найдены.
            </p>
        </div>

        <!-- Пагинация -->
        <div
            v-if="items.links && items.links.length > 1"
            class="mt-6 flex flex-wrap items-center justify-center gap-2"
        >
            <Button
                v-for="link in items.links"
                :key="link.label + (link.url || '')"
                variant="outline"
                type="button"
                :disabled="!link.url"
                @click.prevent="goToPage(link)"
                class="border-2 rounded-xl text-xs sm:text-sm transition-colors flex items-center justify-center"
                :class="[
            // Числовые страницы — квадратные кнопки
            Number.isInteger(Number(link.label))
                ? 'h-10 w-10 p-0'
                : 'px-3 py-2',

            // Активная страница
            link.active
                ? 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600'
                : 'bg-white border-gray-200 text-gray-900 hover:border-emerald-600 hover:bg-emerald-50',

            // Отключенные (нет url) — как disabled
            !link.url ? 'opacity-50 cursor-default pointer-events-none' : '',
        ]"
            >
                <span v-html="link.label" />
            </Button>
        </div>


    </section>
</template>
