import { ref, computed } from 'vue'

export function usePhoneMask(initial = '') {
    const raw = ref('')          // "7XXXXXXXXXX" (11 цифр) или ''
    const formatted = ref('')    // "+7 (999) 123-45-67"
    const MAX = 11

    function normalize(input) {
        let digits = String(input ?? '').replace(/\D/g, '')
        if (!digits) return ''

        // РФ:
        // 8XXXXXXXXXX -> 7XXXXXXXXXX
        if (digits[0] === '8') digits = '7' + digits.slice(1)

        // 9XXXXXXXXX... -> 79XXXXXXXXX...
        if (digits[0] === '9') digits = '7' + digits

        // если не начинается с 7 — считаем "вставили мусор", берем последние 10 как номер
        if (digits[0] !== '7') {
            digits = digits.length >= 10 ? ('7' + digits.slice(-10)) : ('7' + digits)
        }

        // жесткий лимит
        return digits.slice(0, MAX)
    }

    function format(digitsRaw) {
        const d = String(digitsRaw ?? '').replace(/\D/g, '').slice(0, MAX)
        if (!d) return ''

        // d: 7XXXXXXXXXX
        const n = d.slice(1) // 10 цифр после 7

        let out = '+7'
        if (n.length > 0) out += ' (' + n.slice(0, 3)
        if (n.length >= 3) out += ') ' + n.slice(3, 6)
        if (n.length >= 6) out += '-' + n.slice(6, 8)
        if (n.length >= 8) out += '-' + n.slice(8, 10)

        return out
    }

    function sync(value) {
        const norm = normalize(value)
        raw.value = norm
        formatted.value = format(norm)
    }

    function setCaretToEnd(el) {
        const len = el.value.length
        try { el.setSelectionRange(len, len) } catch (_) {}
    }

    // input handler: всегда переписываем то, что в поле
    function onInput(e) {
        const el = e.target
        sync(el.value)
        el.value = formatted.value
        setCaretToEnd(el)
    }

    function onPaste(e) {
        e.preventDefault()
        const el = e.target
        const text = (e.clipboardData || window.clipboardData).getData('text')
        sync(text)
        el.value = formatted.value
        setCaretToEnd(el)
    }

    // простая проверка
    const isValid = computed(() => raw.value.length === MAX && raw.value[0] === '7')

    // init
    if (initial) sync(initial)

    return { raw, formatted, isValid, onInput, onPaste, sync }
}
