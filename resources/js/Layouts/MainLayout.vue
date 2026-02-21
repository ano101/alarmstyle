<script setup>
import { Head, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Breadcrumbs from "../Components/Breadcrumbs.vue"
import CallbackModal from '@/Components/CallbackModal.vue'
import OrderModal from "@/Components/OrderModal.vue"
import Header from "@/Components/Header.vue"
import Footer from "@/Components/Footer.vue"
import MobileMenu from "@/Components/MobileMenu.vue"
import { useOrderModal } from "@/Composables/useOrderModal.js"
const { isOpen, selectedProduct } = useOrderModal()
const page = usePage()
const seo = computed(() => page.props.seo ?? {})
const title = computed(() => seo.value.title || 'Alarmstyle — автостудия')
const breadcrumbs = computed(() => page.props.breadcrumbs ?? [])
const jsonLd = computed(() => page.props.jsonLd ?? null)
const mobileMenuOpen = ref(false)
const callbackOpen = ref(false)
const navigate = (url) => {
    if (!url) return
    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}
</script>
<template>
    <div class="min-h-screen flex flex-col bg-slate-50 text-slate-900">
        <!-- SEO -->
        <Head>
            <title>{{ title }}</title>
            <meta
                v-if="seo.description"
                name="description"
                :content="seo.description"
            >
            <meta
                v-if="seo.keywords"
                name="keywords"
                :content="seo.keywords"
            >
            <meta
                name="robots"
                :content="seo.noindex ? 'noindex, nofollow' : 'index, follow'"
            >
            <link
                v-if="seo.canonical"
                rel="canonical"
                :href="seo.canonical"
            />
            <meta
                v-if="seo.og_title"
                property="og:title"
                :content="seo.og_title"
            >
            <meta
                v-if="seo.og_description"
                property="og:description"
                :content="seo.og_description"
            >
            <meta
                v-if="seo.og_image"
                property="og:image"
                :content="seo.og_image"
            >
            <meta
                v-if="seo.og_type"
                property="og:type"
                :content="seo.og_type"
            >
            <meta
                property="og:url"
                :content="seo.url || (typeof window !== 'undefined' ? window.location.href : '')"
            >
            <meta
                v-if="seo.twitter_card"
                name="twitter:card"
                :content="seo.twitter_card"
            >
            <meta
                v-if="seo.twitter_title"
                name="twitter:title"
                :content="seo.twitter_title"
            >
            <meta
                v-if="seo.twitter_description"
                name="twitter:description"
                :content="seo.twitter_description"
            >
            <meta
                v-if="seo.twitter_image"
                name="twitter:image"
                :content="seo.twitter_image"
            >
        </Head>
        <!-- Mobile Menu -->
        <MobileMenu
            v-model:open="mobileMenuOpen"
            @callback="callbackOpen = true"
        />
        <!-- Header -->
        <Header
            v-model:mobile-menu-open="mobileMenuOpen"
            v-model:callback-open="callbackOpen"
        />
        <!-- Контент -->
        <div class="min-h-screen pb-16 lg:pb-24 bg-gradient-to-br from-gray-50 to-white" style="padding-top: calc(var(--header-height, 112px) + 20px)">
            <!-- Breadcrumbs в контейнере -->
            <div v-if="breadcrumbs.length" class="max-w-7xl mx-auto px-4 sm:px-6 mb-4 sm:mb-6">
                <Breadcrumbs
                    :items="breadcrumbs"
                    @navigate="navigate"
                />
            </div>
            <!-- Slot без ограничения ширины для поддержки full-width блоков -->
            <slot />
        </div>
        <!-- Footer -->
        <Footer />
        <!-- модалка одна на весь проект -->
        <OrderModal
            v-model:open="isOpen"
            :product="selectedProduct"
        />
        <!-- Якорь под кнопку "Обратный звонок" -->
        <CallbackModal :open="callbackOpen" @close="callbackOpen = false" />
    </div>
    <!-- JSON-LD -->
    <component
        v-if="jsonLd"
        is="script"
        type="application/ld+json"
        v-html="jsonLd"
    />
</template>
