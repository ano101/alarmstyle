<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { usePhoneMask } from '@/Composables/usePhoneMask'

const props = defineProps({
    open: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const form = useForm({
    name: '',
    phone: '',
    comment: '',
    page_url: '',
    utm: {},
    website: '',
})

const errors = computed(() => form.errors || {})

const { raw, formatted, isValid, onInput, onPaste } = usePhoneMask()

watch(
    () => raw.value,
    (val) => { form.phone = val }
)

const canSubmit = computed(() => isValid.value && !form.processing)

function close() {
    emit('close')
    form.clearErrors()
}

function submit() {
    if (!isValid.value) return

    form.post(route('callback.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'phone', 'comment')
            close()
        },
    })
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-[60]">
        <div class="absolute inset-0 bg-black/50" @click="close"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl border border-slate-200">
                <div class="flex items-start justify-between gap-4 p-4 border-b border-slate-100">
                    <div>
                        <div class="text-lg font-semibold">Обратный звонок</div>
                        <div class="text-xs text-slate-500 mt-1">
                            Оставьте телефон — перезвоним в ближайшее время.
                        </div>
                    </div>

                    <button
                        type="button"
                        class="h-9 w-9 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50"
                        @click="close"
                    >
                        ✕
                    </button>
                </div>

                <form class="p-4 space-y-3" @submit.prevent="submit">
                    <!-- Имя -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Имя</label>
                        <input
                            v-model.trim="form.name"
                            type="text"
                            autocomplete="name"
                            placeholder="Как к вам обращаться"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none
                     focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500"
                        />
                        <div v-if="errors.name" class="mt-1 text-xs text-rose-600">{{ errors.name }}</div>
                    </div>

                    <!-- Телефон -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Телефон *</label>

                        <input
                            type="tel"
                            inputmode="numeric"
                            autocomplete="tel"
                            placeholder="+7 (___) ___-__-__"
                            :value="formatted"
                            @input="onInput"
                            @paste="onPaste"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none
                     focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500"
                        />

                        <div v-if="errors.phone" class="mt-1 text-xs text-rose-600">{{ errors.phone }}</div>
                        <div v-else-if="raw && !isValid" class="mt-1 text-xs text-rose-600">
                            Введите номер полностью
                        </div>
                    </div>

                    <!-- Комментарий -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Комментарий</label>
                        <textarea
                            v-model="form.comment"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none
                     focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500"
                            placeholder="Например: удобное время для звонка"
                        />
                        <div v-if="errors.comment" class="mt-1 text-xs text-rose-600">{{ errors.comment }}</div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                   hover:bg-emerald-700 disabled:opacity-60"
                        :disabled="!canSubmit"
                    >
                        {{ form.processing ? 'Отправляем…' : 'Жду звонка' }}
                    </button>

                    <div class="text-[11px] text-slate-400">
                        Нажимая кнопку, вы соглашаетесь на обработку персональных данных.
                    </div>

                    <input v-model="form.website" type="text" tabindex="-1" autocomplete="off" class="hidden" />
                </form>
            </div>
        </div>
    </div>
</template>
