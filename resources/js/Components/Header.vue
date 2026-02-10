<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { Phone, Mail, MapPin, Search } from 'lucide-vue-next'
import axios from 'axios'
import Button from "./ui/Button.vue";

const props = defineProps({
    mobileMenuOpen: Boolean,
    callbackOpen: Boolean,
})

const emit = defineEmits(['update:mobileMenuOpen', 'update:callbackOpen'])

const page = usePage()
const settings = computed(() => page.props.settings ?? {})
const contacts = computed(() => settings.value.contacts ?? {})
const menus = computed(() => page.props.menus ?? {})
const headerMenu = computed(() => menus.value.header ?? [])
const popularSearches = computed(() => page.props.popularSearches ?? [])

const searchQuery = ref('')
const searchFocused = ref(false)
const searchResults = ref([])
const isSearching = ref(false)

// Debounce функция
let searchTimeout = null
const debounceSearch = (query) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(async () => {
        if (query.trim().length < 2) {
            searchResults.value = []
            return
        }

        isSearching.value = true
        try {
            const response = await axios.get('/api/search', {
                params: { q: query }
            })
            searchResults.value = response.data.results || []
        } catch (error) {
            console.error('Search error:', error)
            searchResults.value = []
        } finally {
            isSearching.value = false
        }
    }, 300)
}

// Следим за изменениями в поисковом запросе
watch(searchQuery, (newValue) => {
    if (newValue.trim().length >= 2) {
        debounceSearch(newValue)
    } else {
        searchResults.value = []
    }
})

// Проверка активного пункта меню
const isActiveLink = (href) => {
    if (!href) return false
    const currentPath = page.url
    // Для главной страницы
    if (href === '/') {
        return currentPath === '/'
    }
    // Для остальных страниц
    return currentPath.startsWith(href)
}


const handleSearchSelect = (query) => {
    searchQuery.value = query
    debounceSearch(query)
}

const handleProductClick = (product) => {
    searchFocused.value = false
    searchQuery.value = ''
    searchResults.value = []
    router.visit(product.url)
}

const viewAllResults = () => {
    if (searchQuery.value.trim()) {
        searchFocused.value = false
        router.visit('/category', {
            data: { search: searchQuery.value },
            preserveState: true,
        })
    }
}

// Close search dropdown on click outside
const handleClickOutside = (e) => {
    if (!e.target.closest('.search-container')) {
        searchFocused.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <!-- Search Overlay -->
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="searchFocused"
            @click="searchFocused = false"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
        />
    </Transition>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/50">
        <!-- Top bar -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between gap-4 text-sm">
                <div class="flex items-center gap-4 sm:gap-6">
                    <a
                        :href="`tel:${contacts.phone?.replace(/[^+0-9]/g, '')}`"
                        class="flex items-center gap-2 hover:opacity-80 transition"
                    >
                        <Phone class="w-4 h-4" />
                        <span class="hidden sm:inline">{{ contacts.phone }}</span>
                    </a>
                    <a
                        :href="`mailto:${contacts.email}`"
                        class="hidden lg:flex items-center gap-2 hover:opacity-80 transition"
                    >
                        <Mail class="w-4 h-4" />
                        <span>{{ contacts.email }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a
                            :href="contacts.whatsapp"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 transition-colors"
                            title="WhatsApp"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                        <a
                            :href="contacts.telegram"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 transition-colors"
                            title="Telegram"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center gap-2">
                        <MapPin class="w-4 h-4 flex-shrink-0" />
                        <span class="text-xs sm:text-sm">{{ contacts.address }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex items-center justify-between gap-4 sm:gap-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group flex-shrink-0">
                    <div class="text-left">
                        <div class="text-lg sm:text-xl font-bold text-gray-900">
                            ALARM<span class="text-emerald-600">STYLE</span>
                        </div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide hidden sm:block">
                            Охранные системы
                        </div>
                    </div>
                </Link>

                <!-- Search Bar - Desktop -->
                <div class="hidden lg:flex flex-1 max-w-xl relative search-container">
                    <div class="relative w-full">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600 pointer-events-none" />
                        <input
                            v-model="searchQuery"
                            @focus="searchFocused = true"
                            type="text"
                            placeholder="Поиск услуг..."
                            class="pl-12 h-11 bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl w-full outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                        >
                    </div>

                    <!-- Search Dropdown -->
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition-all duration-200"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-2"
                    >
                        <div
                            v-if="searchFocused"
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 z-50"
                        >
                            <!-- Search Results -->
                            <div v-if="searchResults.length > 0" class="mb-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-3">Результаты поиска</h3>
                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    <button
                                        v-for="product in searchResults"
                                        :key="product.id"
                                        @click="handleProductClick(product)"
                                        class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors text-left"
                                    >
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="w-16 h-16 object-cover rounded-lg flex-shrink-0"
                                        />
                                        <div v-else class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 truncate">{{ product.name }}</div>
                                            <div v-if="product.brand" class="text-sm text-emerald-600">{{ product.brand }}</div>
                                            <div v-if="product.category" class="text-xs text-gray-500">{{ product.category }}</div>
                                            <div v-if="product.price" class="text-sm font-bold text-gray-900 mt-1">
                                                {{ Number(product.price).toLocaleString('ru-RU') }} ₽
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <button
                                        @click="viewAllResults"
                                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                                    >
                                        Посмотреть все результаты →
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div v-else-if="isSearching" class="mb-4">
                                <div class="flex items-center justify-center py-8">
                                    <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- No Results -->
                            <div v-else-if="searchQuery.trim().length >= 2 && searchResults.length === 0 && !isSearching" class="mb-4">
                                <div class="text-center py-6">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Ничего не найдено</p>
                                </div>
                            </div>

                            <!-- Popular Searches -->
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Популярные запросы</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="(search, idx) in popularSearches"
                                    :key="idx"
                                    @click="handleSearchSelect(search)"
                                    class="px-4 py-2 bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200"
                                >
                                    {{ search }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Navigation - Desktop -->
                <nav class="hidden lg:flex items-center gap-4 xl:gap-6 flex-shrink-0">
                    <template v-for="item in headerMenu" :key="item.label">
                        <!-- Пункт без детей -->
                        <Link
                            v-if="!item.children?.length"
                            :href="item.href || '#'"
                            :class="[
                                'text-sm font-medium transition-colors',
                                isActiveLink(item.href)
                                    ? 'text-emerald-600 font-semibold'
                                    : 'text-gray-700 hover:text-emerald-600'
                            ]"
                            :target="item.newTab ? '_blank' : null"
                        >
                            {{ item.label }}
                        </Link>

                        <!-- Пункт с dropdown -->
                        <div
                            v-else
                            class="relative group"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 transition-colors"
                            >
                                {{ item.label }}
                                <svg
                                    class="w-4 h-4 ml-1 transform transition-transform duration-200 group-hover:rotate-180"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                class="pointer-events-none absolute left-0 top-full w-56 pt-3 opacity-0 translate-y-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto z-50"
                            >
                                <div class="bg-white rounded-xl shadow-2xl border border-gray-200 p-3">
                                    <Link
                                        v-for="child in item.children"
                                        :key="child.label"
                                        :href="child.href || '#'"
                                        :class="[
                                            'block w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium transition-colors',
                                            isActiveLink(child.href)
                                                ? 'bg-emerald-100 text-emerald-700 font-semibold'
                                                : 'text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'
                                        ]"
                                        :target="child.newTab ? '_blank' : null"
                                    >
                                        {{ child.label }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </template>

                    <Button
                        @click="emit('update:callbackOpen', true)"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md px-6 h-10 transition-colors duration-30"
                    >
                        <Phone className="w-4 h-4 mr-2" />
                        Обратный звонок
                    </Button>
                </nav>

                <!-- Mobile Menu Button -->
                <button
                    @click="emit('update:mobileMenuOpen', true)"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Search -->
            <div class="lg:hidden mt-4 search-container">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        v-model="searchQuery"
                        @focus="searchFocused = true"
                        type="text"
                        placeholder="Поиск..."
                        class="pl-12 h-11 bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl w-full outline-none focus:ring-1 focus:ring-emerald-500"
                    >

                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition-all duration-200"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-2"
                    >
                        <div
                            v-if="searchFocused"
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 z-50"
                        >
                            <!-- Search Results Mobile -->
                            <div v-if="searchResults.length > 0" class="mb-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-3">Результаты</h3>
                                <div class="space-y-2 max-h-80 overflow-y-auto">
                                    <button
                                        v-for="product in searchResults"
                                        :key="product.id"
                                        @click="handleProductClick(product)"
                                        class="w-full flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left"
                                    >
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
                                        />
                                        <div v-else class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-sm text-gray-900 truncate">{{ product.name }}</div>
                                            <div v-if="product.brand" class="text-xs text-emerald-600">{{ product.brand }}</div>
                                            <div v-if="product.price" class="text-sm font-bold text-gray-900 mt-0.5">
                                                {{ Number(product.price).toLocaleString('ru-RU') }} ₽
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <button
                                        @click="viewAllResults"
                                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                                    >
                                        Все результаты →
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State Mobile -->
                            <div v-else-if="isSearching" class="mb-4">
                                <div class="flex items-center justify-center py-6">
                                    <svg class="animate-spin h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- No Results Mobile -->
                            <div v-else-if="searchQuery.trim().length >= 2 && searchResults.length === 0 && !isSearching" class="mb-4">
                                <div class="text-center py-4">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Ничего не найдено</p>
                                </div>
                            </div>

                            <!-- Popular Searches Mobile -->
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Популярные</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="(search, idx) in popularSearches"
                                    :key="idx"
                                    @click="handleSearchSelect(search)"
                                    class="px-3 py-2 bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200"
                                >
                                    {{ search }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </header>
</template>
