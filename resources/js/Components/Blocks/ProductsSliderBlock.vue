<script setup>
import { computed } from 'vue'
import ProductCart from '@/Components/ProductCart.vue'

const props = defineProps({
  block: {
    type: Object,
    required: true
  }
})

const data = computed(() => props.block.data || {})
const title = computed(() => data.value.title || 'Популярные товары')
const products = computed(() => data.value.products || [])
const width = computed(() => data.value.width || 'full')

const wrapperClass = computed(() => {
  return width.value === 'full'
    ? 'w-full px-4 sm:px-6'
    : 'max-w-7xl mx-auto px-4 sm:px-6'
})
</script>

<template>
  <div :class="wrapperClass" class="py-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-8">
      {{ title }}
    </h2>

    <div v-if="products.length > 0" class="relative">
      <!-- Slider wrapper -->
      <div class="overflow-x-auto scrollbar-hide">
        <div class="flex gap-6 pb-4">
          <div
            v-for="product in products"
            :key="product.id"
            class="flex-shrink-0 w-72"
          >
            <ProductCart :product="product" />
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 text-gray-500">
      Товары не найдены
    </div>
  </div>
</template>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>

