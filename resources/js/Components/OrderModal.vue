<script setup>
import {  X, Phone, Mail, User, Car, Calendar, Check  } from 'lucide-vue-next'
import { motion, AnimatePresence } from 'motion-v'
import Label from "./ui/Label.vue";
import Input from "./ui/Input.vue";
import Button from "./ui/Button.vue";
import Textarea from "./ui/Textarea.vue";
import {ref} from "vue";

// например, модалка управляется пропсом
const props = defineProps({
    open: { type: Boolean, default: false },
})

const emit = defineEmits(['update:open'])

const isSubmitted = ref(false)

// единая функция закрытия
const closeModal = () => {
    emit('update:open', false)
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
                    <form  class="p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label htmlFor="name" class="text-gray-700 font-medium">
                                    Ваше имя *
                                </Label>
                                <div class="relative">
                                    <User class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="name"
                                        name="name"
                                        type="text"
                                        required
                                        value={formData.name}
                                        placeholder="Иван Иванов"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label htmlFor="phone" class="text-gray-700 font-medium">
                                    Телефон *
                                </Label>
                                <div class="relative">
                                    <Phone class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        required
                                        value={formData.phone}
                                        placeholder="+7 (999) 123-45-67"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label htmlFor="email" class="text-gray-700 font-medium">
                                Email
                            </Label>
                            <div class="relative">
                                <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value={formData.email}
                                    placeholder="example@mail.ru"
                                    class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label htmlFor="car" class="text-gray-700 font-medium">
                                    Марка и модель авто
                                </Label>
                                <div class="relative">
                                    <Car class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="car"
                                        name="car"
                                        type="text"
                                        value={formData.car}
                                        placeholder="Toyota Camry"
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label htmlFor="date" class="text-gray-700 font-medium">
                                    Желаемая дата
                                </Label>
                                <div class="relative">
                                    <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        id="date"
                                        name="date"
                                        type="date"
                                        value={formData.date}
                                        class="pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label htmlFor="message" class="text-gray-700 font-medium">
                                Комментарий
                            </Label>
                            <Textarea
                                id="message"
                                name="message"
                                placeholder="Дополнительная информация или вопросы..."
                                class="min-h-[120px] border-2 border-gray-200 focus:border-emerald-500 rounded-xl resize-none"
                            />
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
                        >
                            <Phone class="w-5 h-5 mr-2" />
                            Отправить заявку
                        </Button>
                    </form>
                </motion.div>
            </div>
        </template>
    </AnimatePresence>
</template>
