<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

defineProps({
    open: Boolean,
})

const emit = defineEmits(['update:open', 'callback'])

const page = usePage()
const settings = computed(() => page.props.settings ?? {})
const contacts = computed(() => settings.value.contacts ?? {})
const menuItems = computed(() => page.props.menus?.mob_menu ?? [])
const hasMessengers = computed(() => contacts.value.whatsapp || contacts.value.telegram)

const openAccordions = ref({})

const toggleAccordion = (label) => {
    openAccordions.value[label] = !openAccordions.value[label]
}

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

const close = () => {
    emit('update:open', false)
}

const openCallback = () => {
    emit('callback')
    close()
}
</script>

<template>
    <!-- Backdrop -->
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open"
            @click="close"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] lg:hidden"
        />
    </Transition>

    <!-- Slide-in Menu -->
    <Transition
        enter-active-class="transition-transform duration-300 ease-out"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-300 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div
            v-if="open"
            class="fixed top-0 right-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl z-[70] lg:hidden overflow-y-auto"
        >
            <!-- Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-900 text-xl font-bold tracking-tight">
                            ALARM<span class="text-emerald-600">STYLE</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Охранные системы
                        </div>
                    </div>
                    <button
                        @click="close"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <div class="space-y-1">
                    <template v-for="item in menuItems" :key="item.label">
                        <!-- Accordion (item with children) -->
                        <div v-if="item.children && item.children.length > 0">
                            <button
                                @click="toggleAccordion(item.label)"
                                :class="[
                                    'w-full flex items-center justify-between px-4 py-3 rounded-lg text-[15px] font-medium transition-colors',
                                    item.children.some(c => isActiveLink(c.href))
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'text-gray-700 hover:bg-gray-50'
                                ]"
                            >
                                <span>{{ item.label }}</span>
                                <svg
                                    :class="['w-4 h-4 transition-transform duration-200', openAccordions[item.label] ? 'rotate-180' : '']"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <Transition
                                enter-active-class="transition-all duration-200 ease-out overflow-hidden"
                                enter-from-class="max-h-0 opacity-0"
                                enter-to-class="max-h-[500px] opacity-100"
                                leave-active-class="transition-all duration-200 ease-in overflow-hidden"
                                leave-from-class="max-h-[500px] opacity-100"
                                leave-to-class="max-h-0 opacity-0"
                            >
                                <div v-if="openAccordions[item.label]" class="pl-4 pr-2 py-2 space-y-1">
                                    <Link
                                        v-for="child in item.children"
                                        :key="child.label"
                                        :href="child.href"
                                        @click="close"
                                        :class="[
                                            'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors',
                                            isActiveLink(child.href)
                                                ? 'bg-emerald-50 text-emerald-700 font-medium'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                        ]"
                                    >
                                        {{ child.label }}
                                    </Link>
                                </div>
                            </Transition>
                        </div>

                        <!-- Regular link -->
                        <Link
                            v-else
                            :href="item.href"
                            @click="close"
                            :class="[
                                'w-full flex text-left px-4 py-3 rounded-lg text-[15px] font-medium transition-colors',
                                isActiveLink(item.href)
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            {{ item.label }}
                        </Link>
                    </template>
                </div>

                <!-- CTA Button -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button
                        @click="openCallback"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white h-11 rounded-lg font-medium transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Обратный звонок
                    </button>
                </div>

                <!-- Contact Info -->
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                    <a
                        v-if="contacts.phone"
                        :href="`tel:${contacts.phone?.replace(/[^+0-9]/g, '')}`"
                        class="flex items-center gap-3 text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-sm">{{ contacts.phone }}</span>
                    </a>

                    <a
                        v-if="contacts.email"
                        :href="`mailto:${contacts.email}`"
                        class="flex items-center gap-3 text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">{{ contacts.email }}</span>
                    </a>

                    <a
                        v-if="contacts.address"
                        href="https://yandex.ru/maps/-/CDdkMLVl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm">{{ contacts.address }}</span>
                    </a>

                    <div class="flex items-center gap-3 text-gray-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">Ежедневно с 9:00 до 21:00</span>
                    </div>

                    <!-- Messengers -->
                    <div v-if="hasMessengers" class="flex gap-2 pt-2">
                        <a
                            v-if="contacts.whatsapp"
                            :href="contacts.whatsapp"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            <span class="text-xs font-medium">WhatsApp</span>
                        </a>
                        <a
                            v-if="contacts.telegram"
                            :href="contacts.telegram"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                            <span class="text-xs font-medium">Telegram</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </Transition>
</template>
