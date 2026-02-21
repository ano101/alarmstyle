<script setup>
import MainLayout from "@/Layouts/MainLayout.vue"
import { motion } from 'motion-v'
import Image from "../../Components/ui/Image.vue";
import { Star, Shield, Clock, Wrench, ShoppingCart, Phone, Check, ChevronDown, Info } from 'lucide-vue-next'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import Button from "../../Components/ui/Button.vue";
import { Splide, SplideSlide } from '@splidejs/vue-splide';
// GLightbox импортируется динамически в onMounted (не работает в SSR)
import 'glightbox/dist/css/glightbox.min.css';
import { Tooltip, TooltipTrigger, TooltipContent } from "../../Components/ui/Tooltip.vue";
import OrderModal from "@/Components/OrderModal.vue"
import CallbackModal from "@/Components/CallbackModal.vue"
import { useOrderModal } from "@/Composables/useOrderModal.js"
import { useRoute } from '@/Composables/useRoute'

const route = useRoute()
defineOptions({ layout: MainLayout })

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    brand: {
        type: String,
        required: false,
    },
    attributeFeature: {
        type: Array,
        required: false,
    }
})

const mainSplide = ref(null)
const thumbsSplide = ref(null)

const galleryImages = computed(() => {
    return props.product.gallery || [props.product.image]
})

// Модалки
const { isOpen: isOrderOpen, selectedProduct, open: openOrder } = useOrderModal()
const callbackOpen = ref(false)

const openOrderModal = () => {
    openOrder(props.product)
}

const openCallbackModal = () => {
    callbackOpen.value = true
}

// Состояние для аккордеонов атрибутов
const openGroups = ref(new Set())

const toggleGroup = (index) => {
    if (openGroups.value.has(index)) {
        openGroups.value.delete(index)
    } else {
        openGroups.value.add(index)
    }
    // Триггер реактивности
    openGroups.value = new Set(openGroups.value)
}

const isGroupOpen = (index) => {
    return openGroups.value.has(index)
}

const mainOptions = {
    type: 'fade',
    rewind: true,
    pagination: false,
    arrows: true,
    cover: true,
    lazyLoad: 'nearby',
    preloadPages: 1,
    speed: 400,
    breakpoints: {
        1024: {
            heightRatio: 0.75,
        },
    },
}

const thumbsOptions = {
    type: 'slide',
    perPage: 5,
    perMove: 1,
    gap: 8,
    rewind: true,
    pagination: false,
    arrows: true,
    isNavigation: true,
    cover: true,
    focus: 0,
    trimSpace: false,
    lazyLoad: false,
    speed: 300,
    breakpoints: {
        1024: {
            perPage: 4,
            gap: 8,
        },
        768: {
            perPage: 4,
            gap: 6,
        },
        640: {
            perPage: 4,
            gap: 6,
            arrows: false,
        },
    },
}


let lightbox = null

onMounted(async () => {
    // Sync main and thumbnails
    if (mainSplide.value && thumbsSplide.value) {
        mainSplide.value.sync(thumbsSplide.value.splide)
    }

    // Ждем завершения анимаций motion-v перед инициализацией GLightbox
    await new Promise(resolve => setTimeout(resolve, 200))

    // Динамический импорт GLightbox (не работает в SSR - нет window)
    const GLightbox = (await import('glightbox')).default

    // Initialize GLightbox
    lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: false,
        zoomable: true,
        draggable: true,
        openEffect: 'fade',
        closeEffect: 'fade',
        slideEffect: 'slide',
        moreLength: 0,
    })
})

onBeforeUnmount(() => {
    if (lightbox) {
        lightbox.destroy()
    }
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
        <motion.div
            :initial="{ opacity: 0, scale: 0.95 }"
            :animate="{ opacity: 1, scale: 1 }"
            :transition="{ duration: 0.3 }"
            class="space-y-6 h-fit"
        >
            <!-- Main Gallery -->
            <div class="rounded-2xl overflow-hidden bg-white shadow-xl border border-gray-200 aspect-square lg:aspect-auto">
                <Splide
                    ref="mainSplide"
                    :options="mainOptions"
                    class="product-gallery-main h-full"
                >
                    <SplideSlide v-for="(image, index) in galleryImages" :key="index">
                        <a
                            :href="route('images.show', { path: image, preset: 'product.lightbox_2x' })"
                            class="glightbox block cursor-zoom-in group"
                            :data-gallery="'product-gallery'"
                            :data-title="`${product.name} ${index + 1}`"
                        >
                            <Image
                                :src="image"
                                preset="product.card"
                                :alt="`${product.name} ${index + 1}`"
                                class="w-full h-full object-contain p-4 group-hover:opacity-90 transition-opacity"
                            />
                        </a>
                    </SplideSlide>
                </Splide>
            </div>

            <!-- Thumbnail Gallery -->
            <div v-if="galleryImages.length > 1">
                <Splide
                    ref="thumbsSplide"
                    :options="thumbsOptions"
                    class="product-gallery-thumbs"
                >
                    <SplideSlide v-for="(image, index) in galleryImages" :key="index">
                        <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all aspect-square">
                            <Image
                                :src="image"
                                preset="product.thumbnail"
                                :alt="`${product.name} ${index + 1}`"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </SplideSlide>
                </Splide>
            </div>
        </motion.div>

        <motion.div
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.3, delay: 0.1 }"
        >
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-4" v-if="brand">
                <span class="text-sm font-medium text-emerald-700">{{ brand }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4 sm:mb-6">{{ product.name }}</h1>
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-3 mb-8 sm:mb-10">
                <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900">{{ Math.round(product.price).toLocaleString('ru-RU') }} ₽</div>
                <div class="text-base sm:text-lg text-gray-500">с установкой</div>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-6 sm:mb-10">
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Shield class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 mx-auto mb-1 sm:mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Гарантия 1 год</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Clock class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 mx-auto mb-1 sm:mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Установка 2-3 часа</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Wrench class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 mx-auto mb-1 sm:mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Бесплатная настройка</div>
                </div>
            </div>
            <div class="flex gap-4 mb-12">
                <Button
                    @click="openOrderModal"
                    size="lg"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 h-14"
                >
                    <ShoppingCart class="w-5 h-5 mr-2" />
                    Заказать установку
                </Button>
                <Button
                    @click="openCallbackModal"
                    size="lg"
                    variant="outline"
                    class="border-2 border-gray-300 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 h-14 px-6 transition-all duration-200"
                >
                    <Phone class="w-5 h-5" />
                </Button>

            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6" v-if="attributeFeature.length > 0">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Основные особенности</h3>
                <div class="space-y-3">
                    <div v-for="(feature, index) in attributeFeature" :key="index" class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <Check class="w-4 h-4 text-emerald-600" />
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed">{{feature}}</p>
                    </div>
                </div>
            </div>
        </motion.div>
        </div><!-- Закрытие grid lg:grid-cols-2 -->

        <!-- Технические характеристики на всю ширину -->
        <motion.div
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.3, delay: 0.2 }"
            class="mt-16"
        >
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Технические характеристики</h2>
            <div class="space-y-3">
                <div v-for="(group, index) in product.attributeGroups" :key="index" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button
                        @click="toggleGroup(index)"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors"
                    >
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ group.name }}</h3>
                        <ChevronDown
                            class="w-5 h-5 text-gray-400 transition-transform duration-300 ease-in-out"
                            :class="{ 'rotate-180': isGroupOpen(index) }"
                        />
                    </button>
                    <Transition
                        enter-active-class="transition-all duration-300 ease-in-out"
                        leave-active-class="transition-all duration-300 ease-in-out"
                        enter-from-class="grid-rows-[0fr] opacity-0"
                        enter-to-class="grid-rows-[1fr] opacity-100"
                        leave-from-class="grid-rows-[1fr] opacity-100"
                        leave-to-class="grid-rows-[0fr] opacity-0"
                    >
                        <div v-show="isGroupOpen(index)" class="grid overflow-hidden">
                            <div class="min-h-0 border-t border-gray-200">
                                <div class="grid sm:grid-cols-2 xl:grid-cols-4">
                                    <div v-for="(attribute, key) in group.attributes"
                                        :key="key"
                                        class="flex flex-col gap-1 px-4 py-3 border-b border-r border-gray-100 last:border-r-0"
                                    >
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs text-gray-500">{{attribute.name}}</span>
                                            <Tooltip v-if="attribute.helper_text">
                                                <TooltipTrigger asChild>
                                                    <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                                        <Info class="w-3.5 h-3.5" />
                                                    </button>
                                                </TooltipTrigger>
                                                <TooltipContent side="top" class="max-w-xs bg-gray-900 text-white p-3 rounded-lg">
                                                    <p class="text-xs leading-relaxed">{{attribute.helper_text}}</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{attribute.value}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </motion.div>

        <!-- Описание товара на всю ширину -->
        <motion.div
            v-if="product.description"
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.3, delay: 0.25 }"
            class="mt-12 bg-white rounded-2xl p-8 sm:p-12 border border-gray-200"
        >
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Описание</h2>
            <div class="prose prose-gray max-w-none text-gray-700 leading-relaxed" v-html="product.description"></div>
        </motion.div>
    </div><!-- Закрытие контейнера max-w-7xl -->

    <!-- Модалки -->
    <OrderModal
        v-model:open="isOrderOpen"
        :product="selectedProduct"
    />
    <CallbackModal :open="callbackOpen" @close="callbackOpen = false" />
</template>

<style>
.product-gallery-main {
    height: 100%;
}

.product-gallery-main :deep(.splide__track) {
    border-radius: 1rem;
    height: 100%;
}

.product-gallery-main :deep(.splide__list) {
    height: 100%;
}

.product-gallery-main :deep(.splide__slide) {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.product-gallery-main :deep(.splide__arrow) {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    transition: all 0.2s;
}

.product-gallery-main :deep(.splide__arrow:hover) {
    background: #059669;
    color: white;
}

.product-gallery-main :deep(.splide__arrow svg) {
    fill: currentColor;
}

/* Убираем ВСЕ стандартные стили Splide для thumbnails */
.product-gallery-thumbs {
    height: auto !important;
    min-height: auto !important;
}

.product-gallery-thumbs :deep(.splide) {
    height: auto !important;
    min-height: auto !important;
}

.product-gallery-thumbs :deep(.splide__track) {
    padding: 0 !important;
    height: auto !important;
    min-height: auto !important;
}

.product-gallery-thumbs :deep(.splide__list) {
    margin: 0 !important;
    height: auto !important;
    min-height: auto !important;
    display: flex !important;
    align-items: stretch !important;
}

.product-gallery-thumbs :deep(.splide__slide) {
    opacity: 0.6;
    transition: opacity 0.3s;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    height: auto !important;
    min-height: auto !important;
}

.product-gallery-thumbs :deep(.splide__track--nav > .splide__list > .splide__slide.is-active),
.product-gallery-thumbs :deep(.splide__track--nav > .splide__list > .splide__slide.is-active.is-active) {
    border: 0 !important;
    outline: none !important;
    box-shadow: none !important;
    opacity: 1;
}

.product-gallery-thumbs :deep(.splide__track--nav > .splide__list > .splide__slide.is-active) .cursor-pointer {
    border-color: #34d399 !important;
    box-shadow: 0 0 0 2px rgba(52, 211, 153, 0.3);
}

/* Убираем все стандартные фокусные стили */
.product-gallery-thumbs :deep(.splide__slide:focus),
.product-gallery-thumbs :deep(.splide__slide:focus-visible) {
    outline: none !important;
    border: none !important;
    box-shadow: none !important;
}

.product-gallery-thumbs :deep(.splide__slide:focus) .cursor-pointer,
.product-gallery-thumbs :deep(.splide__slide:focus-visible) .cursor-pointer {
    outline: none !important;
}

/* Стрелки для thumbnails */
.product-gallery-thumbs :deep(.splide__arrow) {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    box-shadow: 0 2px 4px -1px rgb(0 0 0 / 0.1);
    transition: all 0.2s;
}

.product-gallery-thumbs :deep(.splide__arrow:hover) {
    background: #059669;
    color: white;
}

.product-gallery-thumbs :deep(.splide__arrow svg) {
    fill: currentColor;
}

@media (max-width: 640px) {
    .product-gallery-thumbs :deep(.splide__arrow) {
        display: none;
    }
}

/* GLightbox кастомизация */
:deep(.glightbox-container) {
    background: rgba(0, 0, 0, 0.95) !important;
}

:deep(.gslide-image img) {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

:deep(.gslide-media) {
    display: flex;
    align-items: center;
    justify-content: center;
}

</style>
