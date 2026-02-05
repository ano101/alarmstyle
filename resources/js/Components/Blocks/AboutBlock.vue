<script setup>
import { computed } from "vue"
import { motion } from "motion-v"
import {
    Award,
    Shield,
    Users,
    Clock,
    CheckCircle2,
    Star,
    TrendingUp,
    Heart,
} from "lucide-vue-next"
import Button from "@/Components/ui/Button.vue"
import Image from "@/Components/ui/Image.vue"
import { useOrderModal } from "@/Composables/useOrderModal.js"

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
})

const { open: openOrder } = useOrderModal()

// Маппинг иконок
const iconMap = {
    Clock,
    Users,
    Shield,
    Award,
    Star,
    CheckCircle2,
    TrendingUp,
    Heart,
}

// Получаем данные из props или используем значения по умолчанию
const companyName = computed(() => props.data.company_name || "AlarmStyle")
const description = computed(() => props.data.description || "Мы специализируемся на установке современных охранных систем для автомобилей.")

const stats = computed(() => props.data.stats || [
    { value: "15+", label: "лет опыта" },
    { value: "10 000+", label: "установок" },
    { value: "98%", label: "довольных клиентов" },
    { value: "24/7", label: "поддержка" },
])

const advantages = computed(() => {
    const advs = props.data.advantages || []
    return advs.map(adv => ({
        ...adv,
        icon: iconMap[adv.icon] || Award,
    }))
})

const aboutImage = computed(() => props.data.about_image || "https://images.unsplash.com/photo-1702146713882-2579afb0bfba?auto=format&fit=crop&w=1080&q=80")

const licenses = computed(() => {
    const lics = props.data.licenses || []
    return lics.map((lic, index) => ({
        ...lic,
        id: index + 1,
    }))
})

const teamList = computed(() => {
    const list = props.data.team_list || []
    // simple repeater возвращает плоский массив строк, а не массив объектов
    return Array.isArray(list) ? list : []
})

const teamImage = computed(() => props.data.team_image || "https://images.unsplash.com/photo-1683770997177-0603bd44d070?auto=format&fit=crop&w=1080&q=80")
</script>

<template>
    <section class="bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Header -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                class="mb-8 sm:mb-12"
            >
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    О компании <span class="text-emerald-600">{{ companyName }}</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl">
                    {{ description }}
                </p>
            </motion.div>

            <!-- Stats -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                :transition="{ delay: 0.1 }"
                class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-16 sm:mb-20"
            >
                <div
                    v-for="(stat, idx) in stats"
                    :key="idx"
                    class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg"
                >
                    <div class="text-3xl sm:text-4xl font-bold text-emerald-600 mb-2">
                        {{ stat.value }}
                    </div>
                    <div class="text-sm sm:text-base text-gray-600">
                        {{ stat.label }}
                    </div>
                </div>
            </motion.div>

            <!-- About Image + Advantages -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                :transition="{ delay: 0.2 }"
                class="mb-16 sm:mb-20"
            >
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                        <Image
                            v-if="aboutImage && !aboutImage.startsWith('http')"
                            :src="aboutImage"
                            preset="about.company_image"
                            alt="Наш сервисный центр"
                            class="w-full h-[400px] object-cover"
                        />
                        <img
                            v-else
                            :src="aboutImage"
                            alt="Наш сервисный центр"
                            class="w-full h-[400px] object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
                        <div class="absolute bottom-6 left-6 right-6">
                            <h3 class="text-2xl font-bold text-white mb-2">Наш сервисный центр</h3>
                            <p class="text-white/90">
                                Современное оборудование и комфортная зона ожидания
                            </p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">
                            Почему выбирают нас
                        </h2>
                        <div class="space-y-4">
                            <motion.div
                                v-for="(advantage, idx) in advantages"
                                :key="idx"
                                :initial="{ opacity: 0, x: 20 }"
                                :animate="{ opacity: 1, x: 0 }"
                                :transition="{ delay: 0.3 + idx * 0.1 }"
                                class="flex gap-4"
                            >
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                                    <component :is="advantage.icon" class="w-6 h-6 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">
                                        {{ advantage.title }}
                                    </h3>
                                    <p class="text-gray-600">
                                        {{ advantage.description }}
                                    </p>
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </div>
            </motion.div>

            <!-- Licenses -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                :transition="{ delay: 0.4 }"
                class="mb-16 sm:mb-20"
            >
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Лицензии и сертификаты
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Мы работаем официально и имеем все необходимые разрешения и сертификаты качества
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <motion.div
                        v-for="(license, idx) in licenses"
                        :key="license.id"
                        :initial="{ opacity: 0, y: 20 }"
                        :animate="{ opacity: 1, y: 0 }"
                        :transition="{ delay: 0.5 + idx * 0.1 }"
                        class="group bg-white rounded-2xl overflow-hidden border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-xl cursor-pointer"
                    >
                        <div class="relative overflow-hidden bg-gray-100 aspect-[3/4]">
                            <Image
                                v-if="license.image && !license.image.startsWith('http')"
                                :src="license.image"
                                preset="about.license"
                                :alt="license.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <img
                                v-else
                                :src="license.image"
                                :alt="license.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <Award class="w-5 h-5 text-emerald-400" />
                                    <span class="text-xs font-medium text-emerald-400">
                                        {{ license.number }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-white text-sm mb-1 line-clamp-2">
                                    {{ license.title }}
                                </h3>
                                <p class="text-xs text-white/80">
                                    {{ license.date }}
                                </p>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </motion.div>

            <!-- Team -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                :transition="{ delay: 0.6 }"
                class="mb-16 sm:mb-20"
            >
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-3xl overflow-hidden shadow-2xl">
                    <div class="grid lg:grid-cols-2">
                        <div class="p-8 sm:p-12 lg:p-16 flex flex-col justify-center">
                            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                                Наша команда профессионалов
                            </h2>
                            <p class="text-white/90 text-lg mb-8">
                                Каждый сотрудник нашей компании прошел специальное обучение и имеет многолетний опыт
                                работы с охранными системами.
                            </p>

                            <ul class="space-y-3 mb-8">
                                <li
                                    v-for="(item, idx) in teamList"
                                    :key="idx"
                                    class="flex items-center gap-3 text-white"
                                >
                                    <CheckCircle2 class="w-5 h-5 text-emerald-300 flex-shrink-0" />
                                    <span>{{ item }}</span>
                                </li>
                            </ul>

                            <Button
                                class="bg-white text-emerald-600 hover:bg-emerald-50 w-fit shadow-lg"
                                @click="openOrder()"
                            >
                                Записаться на консультацию
                            </Button>
                        </div>

                        <div class="relative h-[300px] lg:h-auto">
                            <Image
                                v-if="teamImage && !teamImage.startsWith('http')"
                                :src="teamImage"
                                preset="about.team_image"
                                alt="Наша команда"
                                class="w-full h-full object-cover"
                            />
                            <img
                                v-else
                                :src="teamImage"
                                alt="Наша команда"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </motion.div>

            <!-- CTA -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :animate="{ opacity: 1, y: 0 }"
                :transition="{ delay: 0.7 }"
                class="text-center"
            >
                <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-200 shadow-lg">
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <Star v-for="i in 5" :key="i" class="w-6 h-6 fill-yellow-400 text-yellow-400" />
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                        Доверьте безопасность вашего автомобиля профессионалам
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Запишитесь на консультацию и получите профессиональную помощь
                    </p>
                    <Button
                        class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg px-8 h-14"
                        @click="openOrder()"
                    >
                        Получить консультацию
                    </Button>
                </div>
            </motion.div>
        </div>
    </section>
</template>


