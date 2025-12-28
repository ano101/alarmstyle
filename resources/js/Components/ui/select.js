// select.js
import { h, defineComponent, computed } from "vue";
import {
    SelectRoot,
    SelectGroup as SelectGroupPrimitive,
    SelectValue as SelectValuePrimitive,
    SelectTrigger as SelectTriggerPrimitive,
    SelectContent as SelectContentPrimitive,
    SelectLabel as SelectLabelPrimitive,
    SelectItem as SelectItemPrimitive,
    SelectSeparator as SelectSeparatorPrimitive,
    SelectScrollUpButton as SelectScrollUpButtonPrimitive,
    SelectScrollDownButton as SelectScrollDownButtonPrimitive,
    SelectPortal,
    SelectViewport,
    SelectIcon,
    SelectItemIndicator,
    SelectItemText,
} from "radix-vue";

import { Check, ChevronDown, ChevronUp } from "lucide-vue-next";
import { cn } from "@/utils";

// Root
export const Select = defineComponent({
    name: "Select",
    setup(_, { attrs, slots }) {
        return () =>
            h(
                SelectRoot,
                {
                    "data-slot": "select",
                    ...attrs,
                },
                slots,
            );
    },
});

// Group
export const SelectGroup = defineComponent({
    name: "SelectGroup",
    setup(_, { attrs, slots }) {
        return () =>
            h(
                SelectGroupPrimitive,
                {
                    "data-slot": "select-group",
                    ...attrs,
                },
                slots,
            );
    },
});

// Value
export const SelectValue = defineComponent({
    name: "SelectValue",
    setup(_, { attrs, slots }) {
        return () =>
            h(
                SelectValuePrimitive,
                {
                    "data-slot": "select-value",
                    ...attrs,
                },
                slots,
            );
    },
});

// Trigger
export const SelectTrigger = defineComponent({
    name: "SelectTrigger",
    props: {
        size: {
            type: String,
            default: "default", // "sm" | "default"
        },
    },
    setup(props, { attrs, slots }) {
        const triggerClass = computed(() =>
            cn(
                "border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-full items-center justify-between gap-2 rounded-md border bg-input-background px-3 py-2 text-sm whitespace-nowrap transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
                attrs.class,
            ),
        );

        return () =>
            h(
                SelectTriggerPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-trigger",
                    "data-size": props.size,
                    class: triggerClass.value,
                },
                {
                    default: () => [
                        slots.default?.(),
                        h(
                            SelectIcon,
                            null,
                            {
                                default: () => h(ChevronDown, { class: "size-4 opacity-50" }),
                            },
                        ),
                    ],
                },
            );
    },
});

// Content
export const SelectContent = defineComponent({
    name: "SelectContent",
    props: {
        position: {
            type: String,
            default: "popper", // "popper" | "item-aligned"
        },
    },
    setup(props, { attrs, slots }) {
        const contentClass = computed(() =>
            cn(
                "bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 relative z-50 max-h-(--radix-select-content-available-height) min-w-[8rem] origin-(--radix-select-content-transform-origin) overflow-x-hidden overflow-y-auto rounded-md border shadow-md",
                props.position === "popper" &&
                "data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1",
                attrs.class,
            ),
        );

        const viewportClass = computed(() =>
            cn(
                "p-1",
                props.position === "popper" &&
                "h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)] scroll-my-1",
            ),
        );

        return () =>
            h(
                SelectPortal,
                null,
                {
                    default: () =>
                        h(
                            SelectContentPrimitive,
                            {
                                ...attrs,
                                "data-slot": "select-content",
                                class: contentClass.value,
                                position: props.position,
                            },
                            {
                                default: () => [
                                    h(SelectScrollUpButton),
                                    h(
                                        SelectViewport,
                                        { class: viewportClass.value },
                                        {
                                            default: () => slots.default?.(),
                                        },
                                    ),
                                    h(SelectScrollDownButton),
                                ],
                            },
                        ),
                },
            );
    },
});

// Label
export const SelectLabel = defineComponent({
    name: "SelectLabel",
    setup(_, { attrs, slots }) {
        const labelClass = computed(() =>
            cn("text-muted-foreground px-2 py-1.5 text-xs", attrs.class),
        );

        return () =>
            h(
                SelectLabelPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-label",
                    class: labelClass.value,
                },
                slots,
            );
    },
});

// Item
export const SelectItem = defineComponent({
    name: "SelectItem",
    setup(_, { attrs, slots }) {
        const itemClass = computed(() =>
            cn(
                "focus:bg-accent focus:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex w-full cursor-default items-center gap-2 rounded-sm py-1.5 pr-8 pl-2 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 *:[span]:last:flex *:[span]:last:items-center *:[span]:last:gap-2",
                attrs.class,
            ),
        );

        return () =>
            h(
                SelectItemPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-item",
                    class: itemClass.value,
                },
                {
                    default: () => [
                        h(
                            "span",
                            {
                                class:
                                    "absolute right-2 flex size-3.5 items-center justify-center",
                            },
                            [
                                h(
                                    SelectItemIndicator,
                                    null,
                                    {
                                        default: () => h(Check, { class: "size-4" }),
                                    },
                                ),
                            ],
                        ),
                        h(
                            SelectItemText,
                            null,
                            {
                                default: () => slots.default?.(),
                            },
                        ),
                    ],
                },
            );
    },
});

// Separator
export const SelectSeparator = defineComponent({
    name: "SelectSeparator",
    setup(_, { attrs }) {
        const separatorClass = computed(() =>
            cn(
                "bg-border pointer-events-none -mx-1 my-1 h-px",
                attrs.class,
            ),
        );

        return () =>
            h(
                SelectSeparatorPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-separator",
                    class: separatorClass.value,
                },
            );
    },
});

// ScrollUpButton
export const SelectScrollUpButton = defineComponent({
    name: "SelectScrollUpButton",
    setup(_, { attrs }) {
        const btnClass = computed(() =>
            cn(
                "flex cursor-default items-center justify-center py-1",
                attrs.class,
            ),
        );

        return () =>
            h(
                SelectScrollUpButtonPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-scroll-up-button",
                    class: btnClass.value,
                },
                {
                    default: () => h(ChevronUp, { class: "size-4" }),
                },
            );
    },
});

// ScrollDownButton
export const SelectScrollDownButton = defineComponent({
    name: "SelectScrollDownButton",
    setup(_, { attrs }) {
        const btnClass = computed(() =>
            cn(
                "flex cursor-default items-center justify-center py-1",
                attrs.class,
            ),
        );

        return () =>
            h(
                SelectScrollDownButtonPrimitive,
                {
                    ...attrs,
                    "data-slot": "select-scroll-down-button",
                    class: btnClass.value,
                },
                {
                    default: () => h(ChevronDown, { class: "size-4" }),
                },
            );
    },
});
