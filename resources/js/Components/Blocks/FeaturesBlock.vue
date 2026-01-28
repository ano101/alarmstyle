<template>
    <section class="py-12 sm:py-16 md:py-20 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Header -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                class="text-center mb-8 sm:mb-12"
            >
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
                    {{ title }}
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </motion.div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <motion.div
                    v-for="(feature, index) in features"
                    :key="feature.title"
                    :initial="{ opacity: 0, y: 20 }"
                    :while-in-view="{ opacity: 1, y: 0 }"
                    :viewport="{ once: true }"
                    :transition="{ delay: index * 0.1 }"
                    class="bg-white rounded-2xl p-6 sm:p-8 shadow-md border border-gray-100 hover:shadow-lg hover:border-emerald-500 transition-all duration-300"
                >
                    <div class="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center mb-6 shadow-md">
                        <component :is="feature.icon" class="w-7 h-7 text-white" />
                    </div>

                    <h3 class="font-bold text-gray-900 mb-3 text-lg">
                        {{ feature.title }}
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        {{ feature.description }}
                    </p>
                </motion.div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from "vue"
import { motion } from "motion-v"
import {
    MessageSquare,
    Award,
    Clock,
    MapPin,
    Shield,
    CheckCircle,
    Star,
    Zap,
} from "lucide-vue-next"

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const data = computed(() => props.block?.data || {})

const title = computed(() => data.value.title || "Почему выбирают нас")
const description = computed(
    () =>
        data.value.description ||
        "Мы предлагаем профессиональный подход к защите вашего автомобиля"
)

// Маппинг иконок из строковых идентификаторов в Vue компоненты
const iconMap = {
    "message-square": MessageSquare,
    award: Award,
    clock: Clock,
    "map-pin": MapPin,
    shield: Shield,
    "check-circle": CheckCircle,
    star: Star,
    zap: Zap,
}

// Преобразуем данные из админки в формат с компонентами иконок
const features = computed(() => {
    if (data.value.items && Array.isArray(data.value.items)) {
        return data.value.items.map((item) => ({
            icon: iconMap[item.icon] || MessageSquare,
            title: item.title,
            description: item.description,
        }))
    }

    // Дефолтные данные, если не заполнено в админке
    return [
        {
            icon: MessageSquare,
            title: "Быстро и качественно",
            description:
                "Каждый владелец автомобиля мечтает сохранить свое имущество и защитить автомобиль от угона.",
        },
        {
            icon: Award,
            title: "Гарантия и сертификат",
            description:
                "Предоставляем официальную гарантию на все виды работ и используемое оборудование.",
        },
        {
            icon: Clock,
            title: "Выполняем работу точно в срок",
            description: "Ценим ваше время и строго соблюдаем установленные сроки выполнения работ.",
        },
        {
            icon: MapPin,
            title: "Удобное расположение",
            description: "Наш сервисный центр находится в удобном месте с бесплатной парковкой.",
        },
    ]
})
</script>
