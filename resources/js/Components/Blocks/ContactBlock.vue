<script setup>
import { computed } from "vue"
import { usePage } from "@inertiajs/vue3"
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

const width = computed(() => data.value.width || 'container')
const containerClass = computed(() =>
    width.value === 'full' ? 'w-full px-4 sm:px-6' : 'max-w-7xl mx-auto px-4 sm:px-6'
)

const title = computed(() => data.value.title || "Контакты")
const description = computed(() => data.value.description || "Свяжитесь с нами удобным способом. Мы работаем для вас каждый день!")

const phone = computed(() => contacts.value.phone || data.value.phone || "8 (499) 444-14-39")
const phoneRaw = computed(() => phone.value.replace(/[^+0-9]/g, ''))
const email = computed(() => contacts.value.email || data.value.email || "alarm@style@mail.ru")
const address = computed(() => contacts.value.address || data.value.address || "Москва, пр-кт Вернадского, 124")
const metro = computed(() => data.value.metro || "м. Юго-Западная")

const workWeek = computed(() => data.value.work_week || "9:00 - 21:00")
const workWeekend = computed(() => data.value.work_weekend || "10:00 - 20:00")

const ctaTitle = computed(() => data.value.cta_title || "Остались вопросы?")
const ctaDescription = computed(() => data.value.cta_description || "Оставьте заявку, и наш специалист перезвонит вам в течение 5 минут для бесплатной консультации.")
const ctaFeatures = computed(() => {
    const raw = data.value.cta_features || [
        { item: 'Бесплатная консультация' },
        { item: 'Подбор оптимальной системы' },
        { item: 'Расчет стоимости установки' },
    ]
    return raw.map(f => (typeof f === 'string' ? { item: f } : f))
})
const ctaFooterNote = computed(() => data.value.cta_footer_note || "Работаем ежедневно с 9:00 до 21:00")

const stats = computed(() => data.value.stats || [
    { value: '15+', label: 'лет на рынке' },
    { value: '5000+', label: 'довольных клиентов' },
    { value: '24/7', label: 'поддержка клиентов' },
])

const mapIframe = computed(() => data.value.map_iframe || null)
</script>

<template>
    <section class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <div :class="containerClass">

            <!-- Header -->
            <div class="text-center mb-12 sm:mb-16">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4 sm:mb-6">
                    {{ title }}
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 sm:gap-12">
                <!-- Contact Info -->
                <div class="space-y-6">
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
                                <p class="text-sm text-gray-500 mt-1">Звоните с {{ workWeek.split(' - ')[0] }} до {{ workWeek.split(' - ')[1] }}</p>
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
                </div>

                <!-- CTA -->
                <div class="lg:sticky lg:top-36 h-fit">
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-white shadow-2xl">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                            <MessageSquare class="w-8 h-8" />
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-bold mb-4">{{ ctaTitle }}</h2>
                        <p class="text-emerald-50 mb-8 text-lg">{{ ctaDescription }}</p>

                        <div class="space-y-4 mb-8">
                            <div
                                v-for="(feature, index) in ctaFeatures"
                                :key="index"
                                class="flex items-center gap-3"
                            >
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                <span>{{ feature.item }}</span>
                            </div>
                        </div>

                        <Button
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-colors duration-300 h-14 text-lg font-semibold"
                            @click="openOrder()"
                        >
                            <Phone class="w-5 h-5 mr-2" />
                            Заказать звонок
                        </Button>

                        <p class="text-sm text-emerald-100 mt-4 text-center">
                            {{ ctaFooterNote }}
                        </p>
                    </div>

                    <!-- Map -->
                    <div class="mt-8 rounded-2xl overflow-hidden aspect-video">
                        <div
                            v-if="mapIframe"
                            class="w-full h-full"
                            v-html="mapIframe"
                        ></div>
                        <div
                            v-else
                            class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400"
                        >
                            <MapPin class="w-12 h-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom stats -->
            <div
                v-if="stats.length"
                class="mt-16 sm:mt-20 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
            >
                <div
                    v-for="(stat, index) in stats"
                    :key="index"
                    class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-200 text-center"
                    :class="{ 'sm:col-span-2 lg:col-span-1': index === stats.length - 1 && stats.length % 2 !== 0 && stats.length % 3 !== 0 }"
                >
                    <div class="text-4xl sm:text-5xl font-bold text-emerald-600 mb-2">{{ stat.value }}</div>
                    <div class="text-gray-700 font-medium">{{ stat.label }}</div>
                </div>
            </div>

        </div>
    </section>
</template>
