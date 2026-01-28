<template>
    <section class="py-12 sm:py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Header -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                :transition="{ duration: 0.6 }"
                class="text-center mb-8 sm:mb-12"
            >
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">{{ title }}</h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </motion.div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <motion.div
                    v-for="(service, index) in services"
                    :key="service.title"
                    :initial="{ opacity: 0, y: 20 }"
                    :while-in-view="{ opacity: 1, y: 0 }"
                    :viewport="{ once: true }"
                    :transition="{ delay: index * 0.1 }"
                    class="group relative bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg"
                >
                    <div
                        class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-300 shadow-md"
                    >
                        <component :is="service.icon" class="w-8 h-8 text-white" />
                    </div>

                    <h3 class="font-bold text-gray-900 mb-2 text-lg">
                        {{ service.title }}
                    </h3>

                    <div class="text-2xl font-bold text-emerald-600 mb-5">
                        {{ service.price }}
                    </div>

                    <ul class="space-y-2 mb-6">
                        <li
                            v-for="(feature, i) in service.features"
                            :key="i"
                            class="flex items-center gap-2 text-sm text-gray-600"
                        >
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full" />
                            {{ feature }}
                        </li>
                    </ul>

                    <Button
                        variant="outline"
                        class="w-full border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200"
                        @click="openOrder(service)"
                    >
                        Подробнее
                    </Button>
                </motion.div>
            </div>
        </div>

        <!-- Order modal -->
        <OrderModal
            :open="isOrderOpen"
            :product="selectedProduct"
            @update:open="(val) => isOrderOpen = val"
        />
    </section>
</template>

<script setup>
import { computed } from "vue"
import { motion } from "motion-v"
import {
    Car,
    Radio,
    Satellite,
    Lock,
    Camera,
    MapPin,
    Shield,
    Key,
    Monitor,
    Speaker,
} from "lucide-vue-next"
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

const data = computed(() => props.block?.data || {})

const title = computed(() => data.value.title || "Наши услуги")
const description = computed(
    () =>
        data.value.description ||
        "Профессиональная установка автомобильного оборудования с гарантией качества"
)

// Маппинг иконок из строковых идентификаторов в Vue компоненты
const iconMap = {
    car: Car,
    radio: Radio,
    satellite: Satellite,
    lock: Lock,
    camera: Camera,
    "map-pin": MapPin,
    shield: Shield,
    key: Key,
    monitor: Monitor,
    speaker: Speaker,
}

// Преобразуем данные из админки в формат с компонентами иконок
const services = computed(() => {
    if (data.value.services && Array.isArray(data.value.services)) {
        return data.value.services.map((service) => ({
            icon: iconMap[service.icon] || Car,
            title: service.title,
            price: service.price,
            features: service.features || [],
        }))
    }

    // Дефолтные данные, если не заполнено в админке
    return [
        {
            icon: Car,
            title: "Установка автосигнализации",
            price: "От 3000₽",
            features: ["Диалоговый код", "Защита от сканера", "GSM-модуль"],
        },
        {
            icon: Radio,
            title: "Установка фароискателя",
            price: "От 2000₽",
            features: ["GPS/ГЛОНАСС", "Онлайн мониторинг", "История поездок"],
        },
        {
            icon: Satellite,
            title: "Установка парктроника",
            price: "От 3000₽",
            features: ["4-8 датчиков", "Звуковой сигнал", "LED-дисплей"],
        },
        {
            icon: Lock,
            title: "Центральный замок",
            price: "От 2500₽",
            features: ["Комфорт открытия", "Защита от угона", "Бесшумная работа"],
        },
        {
            icon: Camera,
            title: "Камера заднего вида",
            price: "От 2000₽",
            features: ["HD качество", "Ночной режим", "Парковочные линии"],
        },
        {
            icon: MapPin,
            title: "Иммобилайзер",
            price: "От 3500₽",
            features: ["Скрытая установка", "Блокировка двигателя", "Максимальная защита"],
        },
    ]
})
</script>
