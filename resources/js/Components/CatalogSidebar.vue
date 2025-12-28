<script setup>
import { computed, ref, watch, nextTick } from 'vue'
import AttributeFilter from '@/Components/AttributeFilter.vue'
import Input from "./ui/Input.vue";

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    attributes: {
        type: Array,
        required: true,
    },
    facets: {
        type: Object,
        required: true,
    },
    selectedSlugs: {
        type: Array,
        required: true,
    },
    priceBounds: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categorySlug: {
        type: String,
        required: true,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['navigate', 'close-mobile'])

const minPrice = computed(() => props.priceBounds?.min ?? null)
const maxPrice = computed(() => props.priceBounds?.max ?? null)

const priceFrom = ref(props.filters.price_from ?? '')
const priceTo   = ref(props.filters.price_to ?? '')

// флаг, чтобы не триггерить авто-применение,
// когда мы просто синкаем значения из props.filters
const isSyncingFilters = ref(false)

watch(
    () => props.filters,
    async (f) => {
        isSyncingFilters.value = true
        priceFrom.value = f.price_from ?? ''
        priceTo.value   = f.price_to ?? ''
        await nextTick()
        isSyncingFilters.value = false
    },
    { deep: true },
)

// базовый URL: /category/{path}?query
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

function applyPriceFilter() {
    const query = { ...props.filters }

    query.price_from = priceFrom.value || undefined
    query.price_to   = priceTo.value || undefined

    let basePath = props.categorySlug
    if (props.selectedSlugs.length) {
        basePath += '/' + props.selectedSlugs.join('/')
    }

    const url = buildCatalogUrl(basePath, query)
    emit('navigate', url)
}

// вызывать фильтрацию после change/enter
function handlePriceChange() {
    if (isSyncingFilters.value) return
    applyPriceFilter()
}

function categoryUrl(slug) {
    return buildCatalogUrl(slug, props.filters)
}

// ----- подъём атрибутов с выбранными значениями наверх

function getValueSlug(value) {
    return (
        value.slug?.slug ??
        value.slug ??
        value.slug_string ??
        value.slug_value ??
        null
    )
}

const selectedSlugsSet = computed(() => new Set(props.selectedSlugs || []))

const sortedAttributes = computed(() => {
    return [...props.attributes].sort((a, b) => {
        const aHasSelected = (a.values || []).some(v =>
            selectedSlugsSet.value.has(getValueSlug(v)),
        )
        const bHasSelected = (b.values || []).some(v =>
            selectedSlugsSet.value.has(getValueSlug(v)),
        )

        if (aHasSelected === bHasSelected) return 0
        return aHasSelected ? -1 : 1
    })
})
</script>

<template>
    <!-- Мобильная панель фильтров -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobileOpen"
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                @click="emit('close-mobile')"
            />
        </Transition>

        <Transition
            enter-active-class="transition-transform duration-300"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="isMobileOpen"
                class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-white z-50 lg:hidden overflow-y-auto"
            >
                <!-- Шапка мобильной панели -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Фильтры</h2>
                    <button
                        type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
                        @click="emit('close-mobile')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Контент фильтров -->
                <div class="p-5 space-y-6">
                    <!-- Категории -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Категории</h3>
                        <div class="space-y-2">
                            <button
                                @click="emit('navigate', categoryUrl(categorySlug))"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 bg-emerald-100 text-emerald-700 font-medium"
                            >
                                {{ category.name }}
                            </button>
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="emit('navigate', categoryUrl(cat.slug?.slug ?? cat.slug ?? ''))"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                    </div>

                    <!-- Цена -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Цена</h3>
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <Input
                                    v-model="priceFrom"
                                    name="price_from"
                                    :min="minPrice !== null ? parseInt(minPrice) : undefined"
                                    type="number"
                                    :placeholder="minPrice !== null ? parseInt(minPrice) : undefined"
                                    class="h-10 border-2 border-gray-200 rounded-lg"
                                    @change="handlePriceChange"
                                    @keyup.enter="handlePriceChange"
                                />
                                <Input
                                    v-model="priceTo"
                                    name="price_to"
                                    :max="maxPrice !== null ? parseInt(maxPrice) : undefined"
                                    type="number"
                                    :placeholder="maxPrice !== null ? parseInt(maxPrice) : undefined"
                                    class="h-10 border-2 border-gray-200 rounded-lg"
                                    @change="handlePriceChange"
                                    @keyup.enter="handlePriceChange"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Атрибуты -->
                    <AttributeFilter
                        v-for="attribute in sortedAttributes"
                        :key="attribute.id"
                        :attribute="attribute"
                        :facets="facets"
                        :selected-slugs="selectedSlugs"
                        :category-slug="categorySlug"
                        :filters="filters"
                        @navigate="emit('navigate', $event)"
                    />
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Десктопная версия сайдбара -->
    <aside class="hidden lg:block w-72 flex-shrink-0">
        <div class="sticky top-36 space-y-6">
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Категории</h3>
                <div class="space-y-2">
                    <a @click.prevent="emit('navigate', categoryUrl(categorySlug))"
                       :href="categoryUrl(categorySlug)"
                       class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 bg-emerald-100 text-emerald-700 font-medium">
                        {{ category.name }}
                    </a>
                    <a v-for="cat in categories"
                       :key="cat.id"
                       :href="categoryUrl(cat.slug?.slug ?? cat.slug ?? '')"
                       @click.prevent="
                                emit(
                                    'navigate',
                                    categoryUrl(cat.slug?.slug ?? cat.slug ?? ''),
                                )
                            "
                       class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                        {{ cat.name }}
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Цена</h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <Input
                            v-model="priceFrom"
                            name="price_from"
                            :min="minPrice !== null ? parseInt(minPrice) : undefined"
                            type="number"
                            :placeholder="minPrice !== null ? parseInt(minPrice) : undefined"
                            class="h-10 border-2 border-gray-200 rounded-lg"
                            @change="handlePriceChange"
                            @keyup.enter="handlePriceChange"
                        />
                        <Input
                            v-model="priceTo"
                            name="price_to"
                            :max="maxPrice !== null ? parseInt(maxPrice) : undefined"
                            type="number"
                            :placeholder="maxPrice !== null ? parseInt(maxPrice) : undefined"
                            class="h-10 border-2 border-gray-200 rounded-lg"
                            @change="handlePriceChange"
                            @keyup.enter="handlePriceChange"
                        />
                    </div>
                </div>
            </div>

            <AttributeFilter
                v-for="attribute in sortedAttributes"
                :key="attribute.id"
                :attribute="attribute"
                :facets="facets"
                :selected-slugs="selectedSlugs"
                :category-slug="categorySlug"
                :filters="filters"
                @navigate="emit('navigate', $event)"
            />
        </div>
    </aside>
</template>
