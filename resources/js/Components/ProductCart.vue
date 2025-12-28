<script setup>
import { ShoppingCart, Star, Radio, Navigation, Zap } from 'lucide-vue-next';
import { motion } from "motion-v"
import Button from "./ui/Button.vue";
import Image from "./ui/Image.vue"; // ⬅ новый компонент

import { useOrderModal } from "@/Composables/useOrderModal.js"
const { open } = useOrderModal()

const props = defineProps({
    product: { Object, required: true },
})
</script>

<template>
    <motion.div
        :initial="{ opacity: 0, y: 20 }"
        :whileInView="{ opacity: 1, y: 0 }"
        :inViewOptions="{ once: true }"
        class="group bg-white rounded-2xl overflow-hidden shadow-md border border-gray-200 hover:shadow-lg hover:border-emerald-500 transition-all duration-300"
    >
        <div class="relative overflow-hidden bg-gray-100 aspect-[4/3]">
            <Image
                v-if="product.image"
                v-show="product.image"
                :src="product.image"
                preset="product.card"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />

            <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm rounded-full px-3 py-1.5 flex items-center gap-1 shadow-md">
                <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                <span class="text-sm font-medium text-gray-900">{{ product.rating }}</span>
            </div>
        </div>

        <div class="p-5">
            <div class="text-sm text-emerald-600 font-medium mb-1">{{ product.brand }}</div>
            <h3 class="font-bold text-gray-900 mb-4 line-clamp-2 text-lg">{{ product.name }}</h3>

            <div class="flex gap-2 mb-4">
                <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200" title="GSM-модуль" v-show="product.gsm === true">
                    <Radio class="w-4 h-4 text-emerald-600" />
                    <span class="text-xs font-medium text-emerald-700">GSM</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 rounded-lg border border-blue-200" title="GPS/ГЛОНАСС" v-show="product.gps === true">
                    <Navigation class="w-4 h-4 text-blue-600" />
                    <span class="text-xs font-medium text-blue-700">GPS</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 rounded-lg border border-purple-200" title="Автозапуск" v-show="product.auto === true">
                    <Zap class="w-4 h-4 text-purple-600" />
                    <span class="text-xs font-medium text-purple-700">Авто</span>
                </div>
            </div>

            <div class="mb-5">
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ product.price.toLocaleString() }} ₽</div>
                <div class="text-sm text-gray-500">с установкой</div>
            </div>

            <div class="flex gap-2">
                <Button
                    variant="outline"
                    class="flex-1 border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200"
                >
                    Подробнее
                </Button>
                <Button
                    @click="open(product)"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-all duration-200"
                >
                    <ShoppingCart class="w-4 h-4 mr-2" />
                    Заказать
                </Button>
            </div>
        </div>
    </motion.div>
</template>
