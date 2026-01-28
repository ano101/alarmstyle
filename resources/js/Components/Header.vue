<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { Phone, Mail, MapPin, Search } from 'lucide-vue-next'

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

const searchQuery = ref('')
const searchFocused = ref(false)

const popularSearches = [
    'Автосигнализация',
    'GPS-трекер',
    'Автозапуск',
    'Видеорегистратор'
]

const handleSearchSelect = (query) => {
    searchQuery.value = query
    searchFocused.value = false
    router.visit('/uslugi', {
        preserveScroll: true,
        preserveState: true,
    })
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
                            class="text-sm font-medium text-gray-700 hover:text-emerald-600 transition-colors"
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
                                        class="block w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
                                        :target="child.newTab ? '_blank' : null"
                                    >
                                        {{ child.label }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button
                        @click="emit('update:callbackOpen', true)"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md px-6 h-10 rounded-lg transition-colors duration-300 flex items-center font-medium"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Обратный звонок
                    </button>
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
