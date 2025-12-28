<script setup>
import MainLayout from "@/Layouts/MainLayout.vue"

defineOptions({ layout: MainLayout })

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <!-- Переопределяем слот title для товара -->

    <!-- Основной контент товара -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <!-- Галерея изображений -->
            <div class="bg-white rounded-lg p-4">
                <img
                    :src="product.image"
                    :alt="product.name"
                    class="w-full h-auto"
                >
            </div>
        </div>

        <div>
            <!-- Цена и кнопка -->
            <div class="bg-white rounded-lg p-6">
                <div class="text-3xl font-bold mb-4">
                    {{ product.price }} ₽
                </div>
                <button class="w-full bg-emerald-600 text-white py-3 px-6 rounded-lg hover:bg-emerald-700 transition">
                    Заказать
                </button>
            </div>

            <!-- Описание -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-3">Описание</h2>
                <div class="text-slate-600" v-html="product.description" />
            </div>
        </div>
    </div>
</template>

