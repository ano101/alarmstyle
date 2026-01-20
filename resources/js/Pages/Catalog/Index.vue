<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import CatalogSidebar from '@/Components/CatalogSidebar.vue'
import CatalogProducts from '@/Components/CatalogProducts.vue'
import CatalogLanding from '@/Components/CatalogLanding.vue'
import CatalogQuickLinks from '@/Components/CatalogQuickLinks.vue'
import CatalogActiveFilters from '@/Components/CatalogActiveFilters.vue'

defineOptions({ layout: MainLayout })

const page = usePage()

// всегда актуальные props
const category           = computed(() => page.props.category)
const categories         = computed(() => page.props.categories)
const items              = computed(() => page.props.items)
const attributes         = computed(() => page.props.attributes)
const selectedValueSlugs = computed(() => page.props.selectedValueSlugs)
const facets             = computed(() => page.props.facets)
const priceBounds        = computed(() => page.props.priceBounds)
const categorySlug       = computed(() => page.props.categorySlug)
const landing            = computed(() => page.props.landing)
const filters            = computed(() => page.props.filters || {})
const quickLinks         = computed(() => page.props.quickLinks || [])

const isLoading   = ref(false)
const catalogTop  = ref(null)
const showFilters = ref(false) // мобильная панель фильтров

function navigate(url) {
    showFilters.value = false

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}

function buildBasePath(customSlugs = null) {
    const slugs = customSlugs ?? selectedValueSlugs.value
    if (!slugs.length) {
        return categorySlug.value
    }
    return categorySlug.value + '/' + slugs.join('/')
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

/** снять один атрибутный фильтр по slug */
function removeAttributeFilter(slug) {
    const newSlugs = selectedValueSlugs.value.filter((s) => s !== slug)
    const basePath = buildBasePath(newSlugs)

    const query = { ...filters.value }

    const url = buildCatalogUrl(basePath, query)
    navigate(url)
}

/** сбросить только цену */
function clearPriceFilter() {
    const basePath = buildBasePath()

    const query = {
        ...filters.value,
        price_from: undefined,
        price_to: undefined,
    }

    const url = buildCatalogUrl(basePath, query)
    navigate(url)
}

/** полный сброс всех фильтров */
function resetAll() {
    const url = buildCatalogUrl(categorySlug.value, {})
    navigate(url)
}


function scrollToTop() {
    if (!catalogTop.value) return
    const rect = catalogTop.value.getBoundingClientRect()
    const top = rect.top + window.scrollY - 80
    window.scrollTo({
        top,
        behavior: 'smooth',
    })
}

function onStart() {
    isLoading.value = true
}

function onFinish() {
    isLoading.value = false
    scrollToTop()
}

const offStart  = ref(null)
const offFinish = ref(null)

onMounted(() => {
    offStart.value  = router.on('start', onStart)
    offFinish.value = router.on('finish', onFinish)
})

onBeforeUnmount(() => {
    if (offStart.value)  offStart.value()
    if (offFinish.value) offFinish.value()
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-2">Каталог</h1>
            </div>
        </div>
        <CatalogQuickLinks :links="quickLinks" />

    <CatalogActiveFilters
        :attributes="attributes"
        :selected-slugs="selectedValueSlugs"
        :filters="filters"
        @remove-attribute="removeAttributeFilter"
        @clear-price="clearPriceFilter"
        @reset-all="resetAll"
    />

    <div class="flex gap-8" ref="catalogTop">
        <CatalogSidebar
            :category="category"
            :categories="categories"
            :attributes="attributes"
            :facets="facets"
            :selected-slugs="selectedValueSlugs"
            :price-bounds="priceBounds"
            :filters="filters"
            :category-slug="categorySlug"
            :is-mobile-open="showFilters"
            @navigate="navigate"
            @close-mobile="showFilters = false"
        />
                            <CatalogProducts
                                :items="items"
                                :category-slug="categorySlug"
                                :selected-slugs="selectedValueSlugs"
                                :filters="filters"
                                :attributes="attributes"
                                :loading="isLoading"
                                @navigate="navigate"
                                @open-filters="showFilters = true"
                            />

                            <!-- LANDING -->
                            <CatalogLanding
                                v-if="items.current_page === 1"
                                :landing="landing"
                                :category="category"
                            />
    </div>
    </div><!-- Закрытие контейнера max-w-7xl -->

<!--    <div class="bg-slate-50/40">-->
<!--        <div-->
<!--            ref="catalogTop"-->
<!--            class="mx-auto"-->
<!--        >-->
<!--            &lt;!&ndash; мобильная панель сверху &ndash;&gt;-->
<!--            <div class="flex items-center justify-between mb-4 md:hidden">-->
<!--                <div class="text-xs text-slate-500">-->
<!--                    Найдено-->
<!--                    <span class="font-semibold text-slate-800">-->
<!--                        {{ items.total }}-->
<!--                    </span>-->
<!--                    товаров-->
<!--                </div>-->

<!--                <button-->
<!--                    type="button"-->
<!--                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full bg-slate-900 text-white text-xs font-medium tracking-wide shadow-sm active:scale-[0.97] transition"-->
<!--                    @click="showFilters = true"-->
<!--                >-->
<!--                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" />-->
<!--                    Фильтры-->
<!--                </button>-->
<!--            </div>-->

<!--            <div class="flex flex-col md:flex-row gap-6 lg:gap-10">-->
<!--                -->

<!--                <div class="flex-1 flex flex-col gap-6">-->
<!--                    &lt;!&ndash; ТОВАРЫ &ndash;&gt;-->
<!--                    <CatalogProducts-->
<!--                        :items="items"-->
<!--                        :category-slug="categorySlug"-->
<!--                        :selected-slugs="selectedValueSlugs"-->
<!--                        :filters="filters"-->
<!--                        :attributes="attributes"-->
<!--                        :loading="isLoading"-->
<!--                        @navigate="navigate"-->
<!--                    />-->

<!--                    &lt;!&ndash; LANDING &ndash;&gt;-->
<!--                    <CatalogLanding-->
<!--                        v-if="items.current_page === 1"-->
<!--                        :landing="landing"-->
<!--                        :category="category"-->
<!--                    />-->
<!--                </div>-->

<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</template>
