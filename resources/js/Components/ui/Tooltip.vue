<script>
import { computed, defineComponent, h } from "vue";
import {
    TooltipProvider as TooltipProviderPrimitive,
    TooltipRoot as TooltipPrimitive,
    TooltipTrigger as TooltipTriggerPrimitive,
    TooltipContent as TooltipContentPrimitive,
    TooltipPortal as TooltipPortalPrimitive,
    TooltipArrow as TooltipArrowPrimitive,
} from "radix-vue";

import { cn } from "@/utils";
/**
 * TooltipProvider
 * props: delayDuration (default 0) + остальные пропсы radix-vue TooltipProvider
 */
const TooltipProvider = defineComponent({
    name: "TooltipProvider",
    props: {
        delayDuration: { type: Number, default: 0 },
    },
    setup(props, { slots, attrs }) {
        return () =>
            h(
                TooltipProviderPrimitive,
                {
                    "data-slot": "tooltip-provider",
                    delayDuration: props.delayDuration,
                    ...attrs,
                },
                slots
            );
    },
});

/**
 * Tooltip
 * В оригинале ты каждый раз оборачиваешь Root в Provider.
 * Оставляю так же: Tooltip -> Provider -> Root.
 */
// eslint-disable-next-line no-unused-vars
const Tooltip = defineComponent({
    name: "Tooltip",
    setup(_, { slots, attrs }) {
        return () =>
            h(
                TooltipProvider,
                { delayDuration: 0 }, // можешь прокинуть сверху, если нужно — см. примечание ниже
                {
                    default: () =>
                        h(
                            TooltipPrimitive,
                            {
                                "data-slot": "tooltip",
                                ...attrs,
                            },
                            slots
                        ),
                }
            );
    },
});

/**
 * TooltipTrigger
 */
// eslint-disable-next-line no-unused-vars
const TooltipTrigger = defineComponent({
    name: "TooltipTrigger",
    setup(_, { slots, attrs }) {
        return () =>
            h(
                TooltipTriggerPrimitive,
                {
                    "data-slot": "tooltip-trigger",
                    ...attrs,
                },
                slots
            );
    },
});

/**
 * TooltipContent
 * props: sideOffset (default 0), className + остальные пропсы radix-vue TooltipContent
 */
// eslint-disable-next-line no-unused-vars
const TooltipContent = defineComponent({
    name: "TooltipContent",
    props: {
        sideOffset: { type: Number, default: 0 },
        className: { type: [String, Array, Object], default: "" },
    },
    setup(props, { slots, attrs }) {
        const klass = computed(() =>
            cn(
                "bg-primary text-primary-foreground animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 w-fit origin-(--radix-tooltip-content-transform-origin) rounded-md px-3 py-1.5 text-xs text-balance",
                props.className
            )
        );

        return () =>
            h(TooltipPortalPrimitive, null, {
                default: () =>
                    h(
                        TooltipContentPrimitive,
                        {
                            "data-slot": "tooltip-content",
                            sideOffset: props.sideOffset,
                            class: klass.value,
                            ...attrs,
                        },
                        {
                            default: () => [
                                slots.default?.(),
                                h(TooltipArrowPrimitive, {
                                    class:
                                        "bg-primary fill-primary z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]",
                                }),
                            ],
                        }
                    ),
            });
    },
});

export { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider };
</script>
