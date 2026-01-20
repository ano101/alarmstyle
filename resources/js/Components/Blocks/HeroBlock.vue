<script setup>
import { computed } from 'vue'
import { motion } from "motion-v"
import { Shield, Phone, Clock, Award } from 'lucide-vue-next'
import Button from "@/Components/ui/Button.vue"
import OrderModal from "@/Components/OrderModal.vue"
import { useOrderModal } from "@/Composables/useOrderModal.js"

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const { isOpen: isOrderOpen, selectedProduct, open: openOrder } = useOrderModal()

const data = props.block.data || {}

// Настройки из админки
const width = computed(() => data.width || 'full')
const title = computed(() => data.title || 'Установка автосигнализаций')
const description = computed(() => data.description || 'Защитите свой автомобиль с помощью современных охранных систем. Гарантия качества и профессиональный монтаж.')
const background = computed(() => data.background || null)
const buttonText = computed(() => data.button_text || 'Записаться на установку')
const buttonUrl = computed(() => data.button_url || '#')

// Изображение по умолчанию
const imageSrc = background.value || 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=80'

</script>

<template>
    <section
        class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-gray-50 via-white to-emerald-50/30 pt-32 sm:pt-20"
    >
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 -right-48 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl" />
            <div class="absolute bottom-1/4 -left-48 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl" />
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-32 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Text content -->
                <motion.div
                    :initial="{ opacity: 0, y: 20 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :inViewOptions="{ once: true }"
                    :transition="{ duration: 0.6 }"
                    class="order-2 lg:order-1"
                >
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100/80 backdrop-blur-sm rounded-full mb-6 sm:mb-8"
                    >
                        <Shield class="w-4 h-4 text-emerald-600" />
                        <span class="text-sm font-medium text-emerald-700">
              Профессиональная установка
            </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                        Установка
                        <span
                            class="block text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-emerald-600 mt-2"
                        >
              автосигнализаций
            </span>
                    </h1>

                    <p class="text-lg sm:text-xl text-gray-600 mb-8 sm:mb-10 max-w-lg leading-relaxed">
                        Защитите свой автомобиль с помощью современных охранных систем.
                        Гарантия качества и профессиональный монтаж.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-12 sm:mb-16">
                        <Button
                            size="lg"
                            @click="openOrder()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 text-base px-8 h-12 sm:h-14 w-full sm:w-auto"
                        >
                            <Phone class="w-5 h-5 mr-2" />
                            Записаться на установку
                        </Button>

                        <Button
                            variant="outline"
                            size="lg"
                            class="border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 text-base px-8 h-12 sm:h-14 w-full sm:w-auto"
                        >
                            Каталог систем
                        </Button>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-3 gap-3 sm:gap-4">
                        <div
                            class="flex flex-col items-center p-3 sm:p-5 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 transition-colors"
                        >
                            <Clock class="w-6 sm:w-8 h-6 sm:h-8 text-emerald-600 mb-2 sm:mb-3" />
                            <span class="text-xs sm:text-sm text-gray-700 text-center font-medium">
                Быстро и качественно
              </span>
                        </div>

                        <div
                            class="flex flex-col items-center p-3 sm:p-5 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 transition-colors"
                        >
                            <Award class="w-6 sm:w-8 h-6 sm:h-8 text-emerald-600 mb-2 sm:mb-3" />
                            <span class="text-xs sm:text-sm text-gray-700 text-center font-medium">
                Гарантия и сертификат
              </span>
                        </div>

                        <div
                            class="flex flex-col items-center p-3 sm:p-5 rounded-xl bg-white border border-gray-200 hover:border-emerald-500 transition-colors"
                        >
                            <Shield class="w-6 sm:w-8 h-6 sm:h-8 text-emerald-600 mb-2 sm:mb-3" />
                            <span class="text-xs sm:text-sm text-gray-700 text-center font-medium">
                Точно в срок
              </span>
                        </div>
                    </div>
                </motion.div>

                <!-- Image -->
                <motion.div
                    :initial="{ opacity: 0, scale: 0.95 }"
                    :whileInView="{ opacity: 1, scale: 1 }"
                    :inViewOptions="{ once: true }"
                    :transition="{ duration: 0.6, delay: 0.2 }"
                    class="relative order-1 lg:order-2"
                >
                    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl">
                        <img
                            :src="imageSrc"
                            alt="Modern car interior"
                            class="w-full aspect-[4/3] object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-gray-900/20 to-transparent" />

                        <!-- Floating card -->
                        <div
                            class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6 right-4 sm:right-6
             bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl
             p-4 sm:p-6 shadow-2xl border border-white/20"
                        >
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                                <div>
                                    <div class="text-sm text-gray-500 mb-1">От 3000₽</div>
                                    <div class="font-bold text-gray-900 text-base sm:text-lg">
                                        Установка автосигнализации
                                    </div>
                                </div>

                                <Button
                                    @click="openOrder()"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md px-4 sm:px-6 w-full sm:w-auto"
                                >
                                    Подробнее
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative elements -->
                    <div
                        class="absolute -top-4 sm:-top-6 -right-4 sm:-right-6 w-24 sm:w-32 h-24 sm:h-32
           bg-gradient-to-br from-emerald-500 to-emerald-600
           rounded-2xl sm:rounded-3xl -z-10 rotate-12 opacity-80"
                    />
                    <div
                        class="absolute -bottom-4 sm:-bottom-6 -left-4 sm:-left-6 w-24 sm:w-32 h-24 sm:h-32
           bg-gradient-to-br from-emerald-400 to-emerald-500
           rounded-2xl sm:rounded-3xl -z-10 -rotate-12 opacity-80"
                    />
                </motion.div>
            </div>
        </div>
    </section>

    <!-- Модалка оформления заказа -->
    <OrderModal
        :open="isOrderOpen"
        @update:open="(val) => isOrderOpen = val"
        :product="selectedProduct"
    />
</template>
