<script setup>
import { computed } from "vue"
import { usePage } from "@inertiajs/vue3"
import { motion } from "motion-v"
import { Phone, Mail, MapPin, Clock, MessageSquare } from "lucide-vue-next"
import Button from "@/Components/ui/Button.vue"
import { useOrderModal } from "@/Composables/useOrderModal.js"

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
})

const { open: openOrder } = useOrderModal()

const page = usePage()
const settings = computed(() => page.props.settings ?? {})
const contacts = computed(() => settings.value.contacts ?? {})

const data = computed(() => props.block?.data || {})

// Width support (хотя для контактов обычно используется container)
const width = computed(() => data.value.width || 'container')
const containerClass = computed(() =>
    width.value === 'full' ? 'w-full px-4 sm:px-6' : 'max-w-7xl mx-auto px-4 sm:px-6'
)

const title = computed(() => data.value.title || "Контакты")
const description = computed(
    () => data.value.description || "Свяжитесь с нами удобным способом. Мы работаем для вас каждый день!"
)

// Данные из глобальных настроек с fallback на значения из блока или дефолтные
const phone = computed(() => contacts.value.phone || data.value.phone || "8 (499) 444-14-39")
const phoneRaw = computed(() => phone.value.replace(/[^+0-9]/g, ''))
const email = computed(() => contacts.value.email || data.value.email || "alarm@style@mail.ru")
const address = computed(() => contacts.value.address || data.value.address || "Москва, пр-кт Вернадского, 124")
const metro = computed(() => data.value.metro || "м. Юго-Западная")

const workWeek = computed(() => data.value.work_week || "9:00 - 21:00")
const workWeekend = computed(() => data.value.work_weekend || "10:00 - 20:00")
</script>

<template>
    <section class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <div :class="containerClass">

            <!-- Header -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                class="text-center mb-12 sm:mb-16"
            >
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4 sm:mb-6">
                    {{ title }}
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </motion.div>

            <div class="grid lg:grid-cols-2 gap-8 sm:gap-12">
                <!-- Contact Info -->
                <motion.div
                    :initial="{ opacity: 0, x: -20 }"
                    :while-in-view="{ opacity: 1, x: 0 }"
                    :viewport="{ once: true }"
                    :transition="{ delay: 0.2 }"
                    class="space-y-6"
                >
                    <!-- Phone -->
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <Phone class="w-7 h-7 text-emerald-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-2">Телефон</h3>
                                <a
                                    :href="`tel:${phoneRaw}`"
                                    class="text-lg sm:text-xl text-emerald-600 hover:text-emerald-700 font-medium transition"
                                >
                                    {{ phone }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">Звоните с 9:00 до 21:00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <Mail class="w-7 h-7 text-emerald-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                                <a
                                    :href="`mailto:${email}`"
                                    class="text-lg sm:text-xl text-emerald-600 hover:text-emerald-700 font-medium transition break-all"
                                >
                                    {{ email }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">Ответим в течение часа</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <MapPin class="w-7 h-7 text-emerald-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-2">Адрес</h3>
                                <p class="text-lg text-gray-700">{{ address }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ metro }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 hover:border-emerald-500 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <Clock class="w-7 h-7 text-emerald-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-3">Режим работы</h3>
                                <div class="space-y-2 text-gray-700">
                                    <div class="flex justify-between">
                                        <span>Понедельник - Пятница:</span>
                                        <span class="font-medium">{{ workWeek }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Суббота - Воскресенье:</span>
                                        <span class="font-medium">{{ workWeekend }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </motion.div>

                <!-- CTA -->
                <motion.div
                    :initial="{ opacity: 0, x: 20 }"
                    :while-in-view="{ opacity: 1, x: 0 }"
                    :viewport="{ once: true }"
                    :transition="{ delay: 0.4 }"
                    class="lg:sticky lg:top-36 h-fit"
                >
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-white shadow-2xl">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                            <MessageSquare class="w-8 h-8" />
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-bold mb-4">Остались вопросы?</h2>
                        <p class="text-emerald-50 mb-8 text-lg">
                            Оставьте заявку, и наш специалист перезвонит вам в течение 5 минут для бесплатной консультации.
                        </p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span>Бесплатная консультация</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span>Подбор оптимальной системы</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span>Расчет стоимости установки</span>
                            </div>
                        </div>

                        <Button
                            class="w-full bg-white text-emerald-600 hover:bg-gray-100 shadow-lg hover:shadow-xl transition-all duration-200 h-14 text-lg font-semibold"
                            @click="openOrder()"
                        >
                            <Phone class="w-5 h-5 mr-2" />
                            Заказать звонок
                        </Button>

                        <p class="text-sm text-emerald-100 mt-4 text-center">
                            Работаем ежедневно с 9:00 до 21:00
                        </p>
                    </div>

                    <div class="mt-8 bg-gray-200 rounded-2xl overflow-hidden aspect-video">
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <MapPin class="w-12 h-12" />
                        </div>
                    </div>
                </motion.div>
            </div>

            <!-- Bottom stats -->
            <motion.div
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                class="mt-16 sm:mt-20 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
            >
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-emerald-600 mb-2">15+</div>
                    <div class="text-gray-700 font-medium">лет на рынке</div>
                </div>
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-emerald-600 mb-2">5000+</div>
                    <div class="text-gray-700 font-medium">довольных клиентов</div>
                </div>
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 text-center sm:col-span-2 lg:col-span-1">
                    <div class="text-4xl sm:text-5xl font-bold text-emerald-600 mb-2">24/7</div>
                    <div class="text-gray-700 font-medium">поддержка клиентов</div>
                </div>
            </motion.div>

        </div>
    </section>
</template>
