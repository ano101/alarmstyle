<script setup>
import { X, Phone, Mail, User, Car, Calendar } from 'lucide-vue-next'
import { motion, AnimatePresence } from 'motion-v'
import { useForm } from '@inertiajs/vue3'
import {inject, watch, ref} from 'vue'
import { usePhoneMask } from '@/Composables/usePhoneMask'
import { useEmailValidation } from '@/Composables/useEmailValidation'
import Label from "./ui/Label.vue"
import Input from "./ui/Input.vue"
import Button from "./ui/Button.vue"
import Textarea from "./ui/Textarea.vue"
const route = inject('route')

const props = defineProps({
    open: { type: Boolean, default: false },
    product: { type: Object, default: null },
})

const emit = defineEmits(['update:open'])

const isSuccess = ref(false)

const { raw, formatted, isValid, onInput, onPaste } = usePhoneMask()
const { email: emailValue, isValid: isEmailValid, errorMessage: emailErrorMessage } = useEmailValidation()

const form = useForm({
    name: '',
    phone: '',
    email: '',
    car: '',
    preferred_date: '',
    message: '',
    product_id: null,
})

// Обновляем product_id при изменении props.product
watch(
    () => props.product,
    (product) => {
        form.product_id = product?.id || null
    },
    { immediate: true }
)

watch(
    () => raw.value,
    (val) => { form.phone = val }
)

watch(
    () => emailValue.value,
    (val) => { form.email = val }
)

const closeModal = () => {
    emit('update:open', false)
    form.reset()
    form.clearErrors()
    // Сбрасываем маску телефона
    raw.value = ''
    formatted.value = ''
    // Сбрасываем email
    emailValue.value = ''
    // Сбрасываем успех через небольшую задержку после закрытия
    setTimeout(() => {
        isSuccess.value = false
    }, 300)
}

const submitForm = () => {
    form.post(route('orders.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isSuccess.value = true
            form.reset()
            // Сбрасываем маску телефона
            raw.value = ''
            formatted.value = ''
            // Сбрасываем email
            emailValue.value = ''
            // Автоматически закрываем модалку через 3 секунды
            setTimeout(() => {
                closeModal()
            }, 3000)
        },
        onError: (errors) => {
            console.error('Order validation errors:', errors)
        },
    })
}
</script>

<template>
    <AnimatePresence>
        <!-- ВАЖНО: нужен v-if внутри AnimatePresence -->
        <template v-if="open">
            <!-- затемнение фона -->
            <motion.div
                :initial="{ opacity: 0 }"
                :animate="{ opacity: 1 }"
                :exit="{ opacity: 0 }"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
            />

            <!-- контейнер модалки -->
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <motion.div
                    :initial="{ opacity: 0, scale: 0.95, y: 20 }"
                    :animate="{ opacity: 1, scale: 1, y: 0 }"
                    :exit="{ opacity: 0, scale: 0.95, y: 20 }"
                    :transition="{ type: 'spring', duration: 0.5 }"
                    @click.stop
                    class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                >
                    <div class="relative bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6">
                        <h2 class="text-3xl font-bold text-white mb-2">Оформление заявки</h2>
                        <p class="text-emerald-100">
                            Заполните форму и мы свяжемся с вами в ближайшее время
                        </p>
                        <button
                            @click="closeModal"
                            class="absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition"
                        >
                            <X className="w-5 h-5 text-white" />
                        </button>
                    </div>
                    <form @submit.prevent="submitForm" class="p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                        <!-- Сообщение об успешной отправке -->
                        <div v-if="isSuccess" class="py-12 text-center">
                            <div class="mx-auto w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Заявка отправлена!</h3>
                            <p class="text-gray-600 text-lg mb-2">
                                Мы получили вашу заявку и свяжемся с вами в ближайшее время
                            </p>
                            <p class="text-sm text-gray-500">
                                Это окно автоматически закроется через несколько секунд
                            </p>
                        </div>

                        <!-- Форма (скрываем при успехе) -->
                        <template v-else>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="name" class="text-gray-700 font-medium">
                                    Ваше имя *
                                </Label>
                                <div class="relative">
                                    <User class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        placeholder="Иван Иванов"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                                <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="phone" class="text-gray-700 font-medium">
                                    Телефон *
                                </Label>
                                <div class="relative">
                                    <Phone class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="phone"
                                        type="tel"
                                        inputmode="numeric"
                                        autocomplete="tel"
                                        required
                                        :value="formatted"
                                        @input="onInput"
                                        @paste="onPaste"
                                        placeholder="+7 (___) ___-__-__"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                                <div v-if="form.errors.phone" class="text-sm text-red-600">{{ form.errors.phone }}</div>
                                <div v-else-if="raw && !isValid" class="text-sm text-red-600">
                                    Введите номер полностью
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="email" class="text-gray-700 font-medium">
                                Email
                            </Label>
                            <div class="relative">
                                <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <Input
                                    id="email"
                                    v-model="emailValue"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="example@mail.ru"
                                    class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                />
                            </div>
                            <div v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</div>
                            <div v-else-if="emailValue && !isEmailValid" class="text-sm text-red-600">
                                {{ emailErrorMessage }}
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="car" class="text-gray-700 font-medium">
                                    Марка и модель авто
                                </Label>
                                <div class="relative">
                                    <Car class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="car"
                                        v-model="form.car"
                                        type="text"
                                        placeholder="Toyota Camry"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                                <div v-if="form.errors.car" class="text-sm text-red-600">{{ form.errors.car }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="preferred_date" class="text-gray-700 font-medium">
                                    Желаемая дата
                                </Label>
                                <div class="relative">
                                    <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="preferred_date"
                                        v-model="form.preferred_date"
                                        type="date"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                                <div v-if="form.errors.preferred_date" class="text-sm text-red-600">{{ form.errors.preferred_date }}</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="message" class="text-gray-700 font-medium">
                                Комментарий
                            </Label>
                            <Textarea
                                id="message"
                                v-model="form.message"
                                placeholder="Дополнительная информация или вопросы..."
                                class="min-h-[120px] border-2 border-gray-200 focus:border-emerald-500 rounded-xl resize-none"
                            />
                            <div v-if="form.errors.message" class="text-sm text-red-600">{{ form.errors.message }}</div>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-4">
                            <p class="text-sm text-gray-600">
                                Нажимая кнопку "Отправить заявку", вы соглашаетесь с политикой обработки
                                персональных данных
                            </p>
                        </div>

                        <Button
                            type="submit"
                            size="lg"
                            class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-xl shadow-emerald-500/30 h-14 text-lg"
                            :disabled="form.processing"
                        >
                            <Phone class="w-5 h-5 mr-2" />
                            {{ form.processing ? 'Отправляем...' : 'Отправить заявку' }}
                        </Button>
                        </template>
                    </form>
                </motion.div>
            </div>
        </template>
    </AnimatePresence>
</template>
