import { ref } from "vue"

const isOpen = ref(false)
const selectedProduct = ref(null)

export function useOrderModal() {
    const open = (product = null) => {
        selectedProduct.value = product
        isOpen.value = true
    }

    const close = () => {
        isOpen.value = false
    }

    return {
        isOpen,
        selectedProduct,
        open,
        close,
    }
}
