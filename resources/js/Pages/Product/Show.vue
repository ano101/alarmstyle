<script setup>
import MainLayout from "@/Layouts/MainLayout.vue"
import { motion } from 'motion-v'
import Image from "../../Components/ui/Image.vue";
import { Star, Shield, Clock, Wrench, ShoppingCart, Phone } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import Button from "../../Components/ui/Button.vue";
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/splide/dist/css/splide.min.css';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';
import { nextTick } from 'vue'

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
})

const mainSplide = ref(null)
const thumbsSplide = ref(null)

const galleryImages = computed(() => {
    return props.product.gallery || [props.product.image]
})

const mainOptions = {
    type: 'fade',
    rewind: true,
    pagination: false,
    arrows: true,
    heightRatio: 0.75,
    cover: true,
}

const thumbsOptions = {
    fixedWidth: 100,
    fixedHeight: 100,
    gap: 12,
    rewind: true,
    pagination: false,
    arrows: false,
    isNavigation: true,
    cover: true,

    // было: focus: 'center',
    focus: 0,

    // было: trimSpace: false,
    trimSpace: true,

    autoHeight: true,
    breakpoints: {
        640: {
            fixedWidth: 70,
            fixedHeight: 70,
        },
    },
}


let lightbox = null

onMounted(() => {
    // Sync main and thumbnails
    if (mainSplide.value && thumbsSplide.value) {
        mainSplide.value.sync(thumbsSplide.value.splide)
    }


    // Initialize PhotoSwipe
    lightbox = new PhotoSwipeLightbox({
        gallery: '#product-gallery',
        children: 'a',
        pswpModule: () => import('photoswipe'),
        padding: { top: 30, bottom: 30, left: 20, right: 20 },
        bgOpacity: 0.9,
    })
    lightbox.init()
})
</script>

<template>
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
        <motion.div
            :initial="{ opacity: 0, scale: 0.95 }"
            :animate="{ opacity: 1, scale: 1 }"
            :transition="{ duration: 0.5 }"
            class="space-y-6 h-fit"
        >
            <!-- Rating Badge - без absolute -->
            <div class="flex justify-end">
                <div class="bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 flex items-center gap-2 shadow-lg border border-gray-200">
                    <Star class="w-5 h-5 fill-yellow-400 text-yellow-400" />
                    <span class="font-medium text-gray-900">{{ product.rating }}</span>
                </div>
            </div>

            <!-- Main Gallery -->
            <div id="product-gallery" class="rounded-2xl overflow-hidden bg-white shadow-xl border border-gray-200">
                <Splide
                    ref="mainSplide"
                    :options="mainOptions"
                    class="product-gallery-main"
                >
                    <SplideSlide v-for="(image, index) in galleryImages" :key="index">
                        <a
                            :href="image"
                            :data-pswp-width="1200"
                            :data-pswp-height="900"
                            target="_blank"
                            rel="noreferrer"
                            class="block cursor-zoom-in"
                        >
                            <Image
                                :src="image"
                                preset="product.card"
                                :alt="`${product.name} ${index + 1}`"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            />
                        </a>
                    </SplideSlide>
                </Splide>
            </div>

            <!-- Thumbnail Gallery -->
            <div v-if="galleryImages.length > 1" class="pb-4">
                <Splide
                    ref="thumbsSplide"
                    :options="thumbsOptions"
                    class="product-gallery-thumbs"
                >
                    <SplideSlide v-for="(image, index) in galleryImages" :key="index">
                        <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all">
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
            :transition="{ duration: 0.5, delay: 0.2 }"
        >
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-4">
                <span class="text-sm font-medium text-emerald-700">{{ brand }}</span>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ product.name }}</h1>
            <div class="flex items-baseline gap-3 mb-10">
                <div class="text-5xl font-bold text-gray-900">
                    {{product.price.toLocaleString()}}₽
                </div>
                <div class="text-lg text-gray-500">
                    c установкой
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-10">
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Shield class="w-6 h-6 text-emerald-600 mx-auto mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Гаратия 1 год</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Clock class="w-6 h-6 text-emerald-600 mx-auto mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Установк 2-3 часа</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
                    <Wrench class="w-6 h-6 text-emerald-600 mx-auto mb-2" />
                    <div class="text-sm text-gray-700 font-medium">Бесплатная настройка</div>
                </div>
            </div>
            <div class="flex gap-4 mb-12">
                <Button
                    size="lg"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 h-14"
                >
                    <ShoppingCart class="w-5 h-5 mr-2" />
                    Заказать установку
                </Button>
                <Button
                    size="lg"
                    variant="outline"
                    class="border-2 border-gray-300 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 h-14 px-6 transition-all duration-200"
                >
                    <Phone className="w-5 h-5" />
                </Button>

            </div>
        </motion.div>
    </div>
    <motion.div
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ duration: 0.5, delay: 0.4 }"
        class="mt-16"
    >
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Технические характеристики</h2>\
        <div class="space-y-3">

        </div>
    </motion.div>
</template>

<style scoped>
.product-gallery-main :deep(.splide__track) {
    border-radius: 1rem;
}

.product-gallery-main :deep(.splide__slide) {
    display: flex;
    align-items: center;
    justify-content: center;
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
    align-items: center !important;
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

.product-gallery-thumbs :deep(.splide__slide.is-active) {
    opacity: 1;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

.product-gallery-thumbs :deep(.splide__slide.is-active) .cursor-pointer {
    border-color: #059669;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2);
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
.product-gallery-thumbs :deep(.splide__list) {
    justify-content: flex-start !important;
}
</style>
