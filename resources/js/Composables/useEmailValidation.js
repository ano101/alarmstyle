import { ref, computed } from 'vue'

export function useEmailValidation(initial = '') {
    const email = ref(initial)

    const isValid = computed(() => {
        if (!email.value) return true // пустой email валиден (если поле не обязательное)

        // Простая regex для валидации email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return emailRegex.test(email.value)
    })

    const errorMessage = computed(() => {
        if (!email.value || isValid.value) return ''
        return 'Введите корректный email'
    })

    return {
        email,
        isValid,
        errorMessage,
    }
}

