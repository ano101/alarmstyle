<script setup>
import { computed, ref } from 'vue'
import Checkbox from "./ui/Checkbox.vue";
import Label from "./ui/Label.vue";
import { Link } from "@inertiajs/vue3";
import {ChevronDown, ChevronUp} from "lucide-vue-next";

const props = defineProps({
    attribute: {
        type: Object,
        required: true,
    },
    facets: {
        type: Object,
        required: true,
    },
    selectedSlugs: {
        type: Array,
        required: true,
    },
    categorySlug: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['navigate'])

const typeFront = computed(() => Number(props.attribute.type_front ?? 1))

function getValueSlug(value) {
    return (
        value.slug?.slug ??
        value.slug ??
        value.slug_string ??
        value.slug_value ??
        null
    )
}

const attributeValueSlugs = computed(() =>
    (props.attribute.values || [])
        .map((v) => getValueSlug(v))
        .filter(Boolean),
)

const selectedSlugsSet = computed(() => new Set(props.selectedSlugs || []))

const currentSlugForAttr = computed(() => {
    if (typeFront.value !== 3) return null
    for (const slug of attributeValueSlugs.value) {
        if (selectedSlugsSet.value.has(slug)) {
            return slug
        }
    }
    return null
})

function buildBasePath(slugs = []) {
    const arr = Array.isArray(slugs) ? [...slugs] : []
    if (arr.length === 0) {
        return props.categorySlug
    }
    return props.categorySlug + '/' + arr.join('/')
}

function buildCatalogUrl(path, query = {}) {
    let url = '/category'

    if (path) {
        url += '/' + String(path).replace(/^\/+/, '')
    }

    const q = new URLSearchParams()
    const fullQuery = { ...props.filters, ...query }

    Object.entries(fullQuery).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            q.append(key, String(value))
        }
    })

    const qs = q.toString()
    if (qs) url += '?' + qs

    return url
}

const resetUrl = computed(() => {
    if (typeFront.value !== 3) return null

    let resetSlugs = props.selectedSlugs.filter(
        (s) => !attributeValueSlugs.value.includes(s),
    )
    resetSlugs.sort()

    const path = buildBasePath(resetSlugs)
    return buildCatalogUrl(path)
})

function selectOptionUrl(value) {
    const valueSlug = getValueSlug(value)
    if (!valueSlug) return null

    let newSlugs = props.selectedSlugs.filter(
        (s) => !attributeValueSlugs.value.includes(s),
    )
    newSlugs.push(valueSlug)
    newSlugs.sort()

    const path = buildBasePath(newSlugs)
    return buildCatalogUrl(path)
}

function isSelectActive(value) {
    const valueSlug = getValueSlug(value)
    return valueSlug && currentSlugForAttr.value === valueSlug
}

function isSelectDisabled(value) {
    const canUse = props.facets?.[value.id] ?? true
    const isActive = isSelectActive(value)
    return !canUse && !isActive
}

function onSelectChange(event) {
    const url = event.target.value
    if (url) emit('navigate', url)
}

function isValueActive(value) {
    const valueSlug = getValueSlug(value)
    return valueSlug && selectedSlugsSet.value.has(valueSlug)
}

function isValueDisabled(value) {
    const canUse = props.facets?.[value.id] ?? true
    const active = isValueActive(value)
    return !canUse && !active
}

function valueUrl(value) {
    const valueSlug = getValueSlug(value)
    if (!valueSlug) return null

    const active = isValueActive(value)
    const canUse = props.facets?.[value.id] ?? true

    if (!canUse && !active) return null

    let newSlugs = [...props.selectedSlugs]

    if (typeFront.value !== 1 && !active) {
        newSlugs = newSlugs.filter(
            (s) => !attributeValueSlugs.value.includes(s),
        )
    }

    if (active) {
        newSlugs = newSlugs.filter((s) => s !== valueSlug)
    } else {
        newSlugs.push(valueSlug)
    }

    newSlugs.sort()

    const path = buildBasePath(newSlugs)
    return buildCatalogUrl(path)
}

function onClickValue(e, value) {
    const url = valueUrl(value)
    if (url) emit('navigate', url)
}

// состояние открытия/закрытия атрибута
const isOpen = ref(true)

function toggleOpen() {
    isOpen.value = !isOpen.value
}
const accordionContent = ref(null)

</script>

<template>
    <div class="bg-white rounded-xl p-5 border border-gray-200" v-if="attribute.values && attribute.values.length">
        <button class="flex items-center justify-between w-full mb-4"  @click="toggleOpen" type="button">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">{{ attribute.name }}</h3>
            <ChevronUp
                v-if="isOpen"
                class="w-5 h-5 text-gray-500"
            />
            <ChevronDown
                v-else
                class="w-5 h-5 text-gray-500"
            />
        </button>
        <transition name="accordion">
            <div v-show="isOpen" ref="accordionContent">
                <div class="space-y-3">
                    <template v-for="value in attribute.values" :key="value.id">
                        <div v-if="isValueDisabled(value)" class="flex items-center gap-2">
                            <Checkbox
                                :id="`attr-${attribute.id}-value-${value.id}`"
                                :checked="isValueActive(value)"
                                disabled="disabled"
                            />
                            <Label
                                :forHtml="`attr-${attribute.id}-value-${value.id}`"
                                class="text-sm text-gray-600 cursor-pointer flex-1 font-normal opacity-60"
                            >
                                {{ value.value }}
                            </Label>
                        </div>
                        <div v-else class="flex items-center gap-2">
                            <Checkbox
                                :id="`attr-${attribute.id}-value-${value.id}`"
                                :checked="isValueActive(value)"
                                @click.prevent="onClickValue($event, value)"
                            />
                            <Label
                                :forHtml="`attr-${attribute.id}-value-${value.id}`"
                                class="text-sm text-gray-600 cursor-pointer flex-1 font-normal"
                                @click.prevent="onClickValue($event, value)"
                            >
                                <Link :href="valueUrl(value)">{{ value.value }}</Link>
                            </Label>
                        </div>
                    </template>
                </div>
            </div>
        </transition>

    </div>
</template>
<style scoped>
.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
}

.accordion-enter-to,
.accordion-leave-from {
    max-height: 500px; /* подбери под свои блоки */
    opacity: 1;
}

.accordion-enter-active,
.accordion-leave-active {
    transition: max-height 0.3s ease, opacity 0.3s ease;
    overflow: hidden;
}
</style>
