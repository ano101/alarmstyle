<script setup>
import MainLayout from "@/Layouts/MainLayout.vue"
import { motion } from 'motion-v'
import Image from "../../Components/ui/Image.vue";
import { Star, Shield, Clock, Wrench, ShoppingCart, Phone, Check, ChevronDown, Info } from 'lucide-vue-next'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import Button from "../../Components/ui/Button.vue";
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/splide/dist/css/splide.min.css';
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
    await new Promise(resolve => setTimeout(resolve, 600))

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
    <div>
        <h1>SSR TEST WORKS</h1>
        <p>This HTML is from server render</p>
    </div>
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
