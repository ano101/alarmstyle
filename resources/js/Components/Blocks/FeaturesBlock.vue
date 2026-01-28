<template>
    <section class="py-20 sm:py-32 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Header -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                class="text-center mb-12 sm:mb-16"
            >
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ title }}
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </motion.div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <motion.div
                    v-for="(feature, index) in features"
                    :key="feature.title"
                    :initial="{ opacity: 0, y: 20 }"
                    :while-in-view="{ opacity: 1, y: 0 }"
                    :viewport="{ once: true }"
                    :transition="{ delay: index * 0.1 }"
                    class="bg-white rounded-2xl p-8 shadow-md border border-gray-100 hover:shadow-lg hover:border-emerald-500 transition-all duration-300"
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
import { MessageSquare, Award, Clock, MapPin } from "lucide-vue-next"

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const data = computed(() => props.block?.data || {})

// Заголовки из админки (как у тебя в других блоках)
const title = computed(() => data.value.title || "Почему выбирают нас")
const description = computed(
    () => data.value.description || "Мы предлагаем профессиональный подход к защите вашего автомобиля"
)

/**
 * Если позже захочешь тянуть список из админки:
 * data.value.items (массив) — можно будет подменить computed ниже.
 */
const features = computed(() => [
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
])
</script>
