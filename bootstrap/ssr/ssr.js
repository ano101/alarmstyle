import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderSlot, ssrRenderVNode, ssrRenderStyle, ssrRenderTeleport } from "vue/server-renderer";
import { mergeProps, unref, withCtx, createTextVNode, toDisplayString, useSSRContext, ref, computed, watch, useAttrs, renderSlot, createVNode, resolveDynamicComponent, createBlock, createCommentVNode, openBlock, Fragment, withModifiers, onMounted, onUnmounted, isRef, nextTick, defineComponent, h, withDirectives, vShow, renderList, onBeforeUnmount, defineAsyncComponent, Transition, createSSRApp } from "vue";
import { Link, useForm, usePage, Head, router, createInertiaApp } from "@inertiajs/vue3";
import { ChevronRight, X, User, Phone, Mail, Car, Calendar, MapPin, Search, Clock, MessageCircle, Check, ChevronUp, ChevronDown, Star, Radio, Navigation, Zap, ShoppingCart, Shield, Wrench, Info } from "lucide-vue-next";
import { AnimatePresence, motion } from "motion-v";
import { Label, CheckboxRoot, CheckboxIndicator, SelectRoot, SelectGroup, SelectValue as SelectValue$1, SelectTrigger as SelectTrigger$1, SelectIcon, SelectPortal, SelectContent as SelectContent$1, SelectViewport, SelectLabel, SelectItem as SelectItem$1, SelectItemIndicator, SelectItemText, SelectSeparator, SelectScrollUpButton as SelectScrollUpButton$1, SelectScrollDownButton as SelectScrollDownButton$1, TooltipProvider as TooltipProvider$1, TooltipRoot, TooltipTrigger as TooltipTrigger$1, TooltipPortal, TooltipContent as TooltipContent$1, TooltipArrow } from "radix-vue";
import { cva } from "class-variance-authority";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
const _sfc_main$k = {
  __name: "Breadcrumbs",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.items.length) {
        _push(`<nav${ssrRenderAttrs(mergeProps({
          class: "flex items-center gap-2 text-sm mb-8",
          "aria-label": "Breadcrumb"
        }, _attrs))}><!--[-->`);
        ssrRenderList(__props.items, (item, index) => {
          _push(`<div class="flex items-center gap-2">`);
          if (item.url && !item.current) {
            _push(ssrRenderComponent(unref(Link), {
              href: item.url,
              class: "text-gray-600 hover:text-emerald-600 transition-colors",
              "preserve-scroll": "",
              "preserve-state": ""
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(item.label)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(item.label), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          } else {
            _push(`<span class="text-gray-900 font-medium"${ssrRenderAttr("aria-current", item.current ? "page" : void 0)}>${ssrInterpolate(item.label)}</span>`);
          }
          if (index < __props.items.length - 1) {
            _push(ssrRenderComponent(unref(ChevronRight), { class: "w-4 h-4 text-gray-400" }, null, _parent));
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        });
        _push(`<!--]--></nav>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$k = _sfc_main$k.setup;
_sfc_main$k.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Breadcrumbs.vue");
  return _sfc_setup$k ? _sfc_setup$k(props, ctx) : void 0;
};
function usePhoneMask(initial = "") {
  const raw = ref("");
  const formatted = ref("");
  const MAX = 11;
  function normalize(input) {
    let digits = String(input ?? "").replace(/\D/g, "");
    if (!digits) return "";
    if (digits[0] === "8") digits = "7" + digits.slice(1);
    if (digits[0] === "9") digits = "7" + digits;
    if (digits[0] !== "7") {
      digits = digits.length >= 10 ? "7" + digits.slice(-10) : "7" + digits;
    }
    return digits.slice(0, MAX);
  }
  function format(digitsRaw) {
    const d = String(digitsRaw ?? "").replace(/\D/g, "").slice(0, MAX);
    if (!d) return "";
    const n = d.slice(1);
    let out = "+7";
    if (n.length > 0) out += " (" + n.slice(0, 3);
    if (n.length >= 3) out += ") " + n.slice(3, 6);
    if (n.length >= 6) out += "-" + n.slice(6, 8);
    if (n.length >= 8) out += "-" + n.slice(8, 10);
    return out;
  }
  function sync(value) {
    const norm = normalize(value);
    raw.value = norm;
    formatted.value = format(norm);
  }
  function setCaretToEnd(el) {
    const len = el.value.length;
    try {
      el.setSelectionRange(len, len);
    } catch (_) {
    }
  }
  function onInput(e) {
    const el = e.target;
    sync(el.value);
    el.value = formatted.value;
    setCaretToEnd(el);
  }
  function onPaste(e) {
    e.preventDefault();
    const el = e.target;
    const text = (e.clipboardData || window.clipboardData).getData("text");
    sync(text);
    el.value = formatted.value;
    setCaretToEnd(el);
  }
  const isValid = computed(() => raw.value.length === MAX && raw.value[0] === "7");
  if (initial) sync(initial);
  return { raw, formatted, isValid, onInput, onPaste, sync };
}
(function() {
  const t = [];
  for (let e = 0; e < 256; ++e) t.push("%" + ((e < 16 ? "0" : "") + e.toString(16)).toUpperCase());
  return t;
})();
const _sfc_main$j = {
  __name: "CallbackModal",
  __ssrInlineRender: true,
  props: {
    open: { type: Boolean, default: false }
  },
  emits: ["close"],
  setup(__props, { emit: __emit }) {
    const isSuccess = ref(false);
    const form = useForm({
      name: "",
      phone: "",
      comment: "",
      page_url: "",
      utm: {},
      website: ""
    });
    const errors = computed(() => form.errors || {});
    const { raw, formatted, isValid } = usePhoneMask();
    watch(
      () => raw.value,
      (val) => {
        form.phone = val;
      }
    );
    const canSubmit = computed(() => isValid.value && !form.processing);
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.open) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed inset-0 z-[60]" }, _attrs))}><div class="absolute inset-0 bg-black/50"></div><div class="absolute inset-0 flex items-center justify-center p-4"><div class="w-full max-w-md rounded-2xl bg-white shadow-xl border border-slate-200"><div class="flex items-start justify-between gap-4 p-4 border-b border-slate-100"><div><div class="text-lg font-semibold">Обратный звонок</div><div class="text-xs text-slate-500 mt-1"> Оставьте телефон — перезвоним в ближайшее время. </div></div><button type="button" class="h-9 w-9 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50"> ✕ </button></div><form class="p-4 space-y-3">`);
        if (isSuccess.value) {
          _push(`<div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-center"><div class="text-emerald-600 text-4xl mb-2">✓</div><div class="text-lg font-semibold text-emerald-900 mb-1">Заявка отправлена!</div><div class="text-sm text-emerald-700"> Мы свяжемся с вами в ближайшее время </div></div>`);
        } else {
          _push(`<!--[--><div><label class="block text-xs font-medium text-slate-700 mb-1">Имя</label><input${ssrRenderAttr("value", unref(form).name)} type="text" autocomplete="name" placeholder="Как к вам обращаться" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">`);
          if (errors.value.name) {
            _push(`<div class="mt-1 text-xs text-rose-600">${ssrInterpolate(errors.value.name)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div><label class="block text-xs font-medium text-slate-700 mb-1">Телефон *</label><input type="tel" inputmode="numeric" autocomplete="tel" placeholder="+7 (___) ___-__-__"${ssrRenderAttr("value", unref(formatted))} class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">`);
          if (errors.value.phone) {
            _push(`<div class="mt-1 text-xs text-rose-600">${ssrInterpolate(errors.value.phone)}</div>`);
          } else if (unref(raw) && !unref(isValid)) {
            _push(`<div class="mt-1 text-xs text-rose-600"> Введите номер полностью </div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div><label class="block text-xs font-medium text-slate-700 mb-1">Комментарий</label><textarea rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500" placeholder="Например: удобное время для звонка">${ssrInterpolate(unref(form).comment)}</textarea>`);
          if (errors.value.comment) {
            _push(`<div class="mt-1 text-xs text-rose-600">${ssrInterpolate(errors.value.comment)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><button type="submit" class="w-full rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-60"${ssrIncludeBooleanAttr(!canSubmit.value) ? " disabled" : ""}>${ssrInterpolate(unref(form).processing ? "Отправляем…" : "Жду звонка")}</button><div class="text-[11px] text-slate-400"> Нажимая кнопку, вы соглашаетесь на обработку персональных данных. </div><input${ssrRenderAttr("value", unref(form).website)} type="text" tabindex="-1" autocomplete="off" class="hidden"><!--]-->`);
        }
        _push(`</form></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$j = _sfc_main$j.setup;
_sfc_main$j.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CallbackModal.vue");
  return _sfc_setup$j ? _sfc_setup$j(props, ctx) : void 0;
};
function cn(...classes) {
  return classes.filter(Boolean).join(" ");
}
const _sfc_main$i = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "Label",
  __ssrInlineRender: true,
  props: {
    // аналог htmlFor из React
    forHtml: {
      type: String,
      default: void 0
    }
  },
  setup(__props) {
    const attrs = useAttrs();
    const props = __props;
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(Label), mergeProps(unref(attrs), {
        "data-slot": "label",
        for: props.forHtml || unref(attrs).for,
        class: unref(cn)(
          "flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50",
          unref(attrs).class
        )
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
          } else {
            return [
              renderSlot(_ctx.$slots, "default")
            ];
          }
        }),
        _: 3
      }, _parent));
    };
  }
});
const _sfc_setup$i = _sfc_main$i.setup;
_sfc_main$i.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Label.vue");
  return _sfc_setup$i ? _sfc_setup$i(props, ctx) : void 0;
};
const _sfc_main$h = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "Input",
  __ssrInlineRender: true,
  props: {
    type: {
      type: String,
      default: "text"
    },
    className: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const attrs = useAttrs();
    const props = __props;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<input${ssrRenderAttrs(mergeProps(unref(attrs), {
        type: props.type,
        "data-slot": "input",
        class: unref(cn)(
          "file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground border-input flex h-9 w-full min-w-0 rounded-md border px-3 py-1 text-base bg-input-background transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
          "focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]",
          "aria-invalid:ring-destructive/20 aria-invalid:border-destructive",
          props.className
        )
      }, _attrs))}>`);
    };
  }
});
const _sfc_setup$h = _sfc_main$h.setup;
_sfc_main$h.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Input.vue");
  return _sfc_setup$h ? _sfc_setup$h(props, ctx) : void 0;
};
const _sfc_main$g = {
  __name: "Button",
  __ssrInlineRender: true,
  props: {
    // насильно задать тег/компонент, если нужно что-то особенное
    as: {
      type: [String, Object],
      default: null
    },
    // если есть href и as не задан, Button станет Inertia Link
    href: {
      type: String,
      default: null
    },
    variant: {
      type: String,
      default: "default"
    },
    size: {
      type: String,
      default: "default"
    }
  },
  emits: ["click"],
  setup(__props, { emit: __emit }) {
    const buttonVariants = cva(
      "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive",
      {
        variants: {
          variant: {
            default: "bg-primary text-primary-foreground hover:bg-primary/90",
            destructive: "bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20",
            outline: "border bg-background text-foreground hover:bg-accent hover:text-accent-foreground",
            secondary: "bg-secondary text-secondary-foreground hover:bg-secondary/80",
            ghost: "hover:bg-accent hover:text-accent-foreground",
            link: "text-primary underline-offset-4 hover:underline"
          },
          size: {
            default: "h-9 px-4 py-2 has-[>svg]:px-3",
            sm: "h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5",
            lg: "h-10 rounded-md px-6 has-[>svg]:px-4",
            icon: "size-9 rounded-md"
          }
        },
        defaultVariants: {
          variant: "default",
          size: "default"
        }
      }
    );
    const props = __props;
    const emit = __emit;
    const componentTag = computed(() => {
      if (props.as) return props.as;
      if (props.href) return Link;
      return "button";
    });
    const classes = computed(
      () => cn(
        buttonVariants({
          variant: props.variant,
          size: props.size
        })
      )
    );
    return (_ctx, _push, _parent, _attrs) => {
      ssrRenderVNode(_push, createVNode(resolveDynamicComponent(componentTag.value), mergeProps({
        href: __props.href,
        class: classes.value
      }, _ctx.$attrs, {
        onClick: (event) => emit("click", event)
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
          } else {
            return [
              renderSlot(_ctx.$slots, "default")
            ];
          }
        }),
        _: 3
      }), _parent);
    };
  }
};
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Button.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const _sfc_main$f = {
  __name: "Textarea",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [String, Number],
      default: ""
    }
  },
  emits: ["update:modelValue", "input"],
  setup(__props, { emit: __emit }) {
    const attrs = useAttrs();
    const textareaClass = computed(
      () => cn(
        "resize-none border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 aria-invalid:border-destructive flex field-sizing-content min-h-16 w-full rounded-md border bg-input-background px-3 py-2 text-base transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
        attrs.class
      )
    );
    return (_ctx, _push, _parent, _attrs) => {
      let _temp0;
      _push(`<textarea${ssrRenderAttrs(_temp0 = mergeProps({
        "data-slot": "textarea",
        class: textareaClass.value
      }, unref(attrs), { value: __props.modelValue }, _attrs), "textarea")}>${ssrInterpolate("value" in _temp0 ? _temp0.value : "")}</textarea>`);
    };
  }
};
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Textarea.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const _sfc_main$e = {
  __name: "OrderModal",
  __ssrInlineRender: true,
  props: {
    open: { type: Boolean, default: false },
    product: { type: Object, default: null }
  },
  emits: ["update:open"],
  setup(__props, { emit: __emit }) {
    const emit = __emit;
    const closeModal = () => {
      emit("update:open", false);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(AnimatePresence), _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (__props.open) {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(unref(motion).div, {
                initial: { opacity: 0 },
                animate: { opacity: 1 },
                exit: { opacity: 0 },
                class: "fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
              }, null, _parent2, _scopeId));
              _push2(`<div class="fixed inset-0 z-50 flex items-center justify-center p-4"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(motion).div, {
                initial: { opacity: 0, scale: 0.95, y: 20 },
                animate: { opacity: 1, scale: 1, y: 0 },
                exit: { opacity: 0, scale: 0.95, y: 20 },
                transition: { type: "spring", duration: 0.5 },
                onClick: () => {
                },
                class: "bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="relative bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6"${_scopeId2}><h2 class="text-3xl font-bold text-white mb-2"${_scopeId2}>Оформление заявки</h2><p class="text-emerald-100"${_scopeId2}> Заполните форму и мы свяжемся с вами в ближайшее время </p><button class="absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(X), { className: "w-5 h-5 text-white" }, null, _parent3, _scopeId2));
                    _push3(`</button></div><form class="p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]"${_scopeId2}><div class="grid md:grid-cols-2 gap-6"${_scopeId2}><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "name",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Ваше имя * `);
                        } else {
                          return [
                            createTextVNode(" Ваше имя * ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`<div class="relative"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(User), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }, null, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$h, {
                      id: "name",
                      name: "name",
                      type: "text",
                      required: "",
                      value: "{formData.name}",
                      placeholder: "Иван Иванов",
                      class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div></div><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "phone",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Телефон * `);
                        } else {
                          return [
                            createTextVNode(" Телефон * ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`<div class="relative"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(Phone), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }, null, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$h, {
                      id: "phone",
                      name: "phone",
                      type: "tel",
                      required: "",
                      value: "{formData.phone}",
                      placeholder: "+7 (999) 123-45-67",
                      class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div></div></div><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "email",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Email `);
                        } else {
                          return [
                            createTextVNode(" Email ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`<div class="relative"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(Mail), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }, null, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$h, {
                      id: "email",
                      name: "email",
                      type: "email",
                      value: "{formData.email}",
                      placeholder: "example@mail.ru",
                      class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div></div><div class="grid md:grid-cols-2 gap-6"${_scopeId2}><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "car",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Марка и модель авто `);
                        } else {
                          return [
                            createTextVNode(" Марка и модель авто ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`<div class="relative"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(Car), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }, null, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$h, {
                      id: "car",
                      name: "car",
                      type: "text",
                      value: "{formData.car}",
                      placeholder: "Toyota Camry",
                      class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div></div><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "date",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Желаемая дата `);
                        } else {
                          return [
                            createTextVNode(" Желаемая дата ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`<div class="relative"${_scopeId2}>`);
                    _push3(ssrRenderComponent(unref(Calendar), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }, null, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$h, {
                      id: "date",
                      name: "date",
                      type: "date",
                      value: "{formData.date}",
                      class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div></div></div><div class="space-y-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_sfc_main$i, {
                      htmlFor: "message",
                      class: "text-gray-700 font-medium"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Комментарий `);
                        } else {
                          return [
                            createTextVNode(" Комментарий ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(ssrRenderComponent(_sfc_main$f, {
                      id: "message",
                      name: "message",
                      placeholder: "Дополнительная информация или вопросы...",
                      class: "min-h-[120px] border-2 border-gray-200 focus:border-emerald-500 rounded-xl resize-none"
                    }, null, _parent3, _scopeId2));
                    _push3(`</div><div class="bg-gray-50 rounded-2xl p-4"${_scopeId2}><p class="text-sm text-gray-600"${_scopeId2}> Нажимая кнопку &quot;Отправить заявку&quot;, вы соглашаетесь с политикой обработки персональных данных </p></div>`);
                    _push3(ssrRenderComponent(_sfc_main$g, {
                      type: "submit",
                      size: "lg",
                      class: "w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-xl shadow-emerald-500/30 h-14 text-lg"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(ssrRenderComponent(unref(Phone), { class: "w-5 h-5 mr-2" }, null, _parent4, _scopeId3));
                          _push4(` Отправить заявку `);
                        } else {
                          return [
                            createVNode(unref(Phone), { class: "w-5 h-5 mr-2" }),
                            createTextVNode(" Отправить заявку ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`</form>`);
                  } else {
                    return [
                      createVNode("div", { class: "relative bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6" }, [
                        createVNode("h2", { class: "text-3xl font-bold text-white mb-2" }, "Оформление заявки"),
                        createVNode("p", { class: "text-emerald-100" }, " Заполните форму и мы свяжемся с вами в ближайшее время "),
                        createVNode("button", {
                          onClick: closeModal,
                          class: "absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition"
                        }, [
                          createVNode(unref(X), { className: "w-5 h-5 text-white" })
                        ])
                      ]),
                      createVNode("form", { class: "p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]" }, [
                        createVNode("div", { class: "grid md:grid-cols-2 gap-6" }, [
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "name",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Ваше имя * ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(User), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "name",
                                name: "name",
                                type: "text",
                                required: "",
                                value: "{formData.name}",
                                placeholder: "Иван Иванов",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ]),
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "phone",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Телефон * ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Phone), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "phone",
                                name: "phone",
                                type: "tel",
                                required: "",
                                value: "{formData.phone}",
                                placeholder: "+7 (999) 123-45-67",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode(_sfc_main$i, {
                            htmlFor: "email",
                            class: "text-gray-700 font-medium"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Email ")
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "relative" }, [
                            createVNode(unref(Mail), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                            createVNode(_sfc_main$h, {
                              id: "email",
                              name: "email",
                              type: "email",
                              value: "{formData.email}",
                              placeholder: "example@mail.ru",
                              class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                            })
                          ])
                        ]),
                        createVNode("div", { class: "grid md:grid-cols-2 gap-6" }, [
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "car",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Марка и модель авто ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Car), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "car",
                                name: "car",
                                type: "text",
                                value: "{formData.car}",
                                placeholder: "Toyota Camry",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ]),
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "date",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Желаемая дата ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Calendar), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "date",
                                name: "date",
                                type: "date",
                                value: "{formData.date}",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode(_sfc_main$i, {
                            htmlFor: "message",
                            class: "text-gray-700 font-medium"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Комментарий ")
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$f, {
                            id: "message",
                            name: "message",
                            placeholder: "Дополнительная информация или вопросы...",
                            class: "min-h-[120px] border-2 border-gray-200 focus:border-emerald-500 rounded-xl resize-none"
                          })
                        ]),
                        createVNode("div", { class: "bg-gray-50 rounded-2xl p-4" }, [
                          createVNode("p", { class: "text-sm text-gray-600" }, ' Нажимая кнопку "Отправить заявку", вы соглашаетесь с политикой обработки персональных данных ')
                        ]),
                        createVNode(_sfc_main$g, {
                          type: "submit",
                          size: "lg",
                          class: "w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-xl shadow-emerald-500/30 h-14 text-lg"
                        }, {
                          default: withCtx(() => [
                            createVNode(unref(Phone), { class: "w-5 h-5 mr-2" }),
                            createTextVNode(" Отправить заявку ")
                          ]),
                          _: 1
                        })
                      ])
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div><!--]-->`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              __props.open ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                createVNode(unref(motion).div, {
                  initial: { opacity: 0 },
                  animate: { opacity: 1 },
                  exit: { opacity: 0 },
                  class: "fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
                }),
                createVNode("div", {
                  class: "fixed inset-0 z-50 flex items-center justify-center p-4",
                  onClick: withModifiers(closeModal, ["self"])
                }, [
                  createVNode(unref(motion).div, {
                    initial: { opacity: 0, scale: 0.95, y: 20 },
                    animate: { opacity: 1, scale: 1, y: 0 },
                    exit: { opacity: 0, scale: 0.95, y: 20 },
                    transition: { type: "spring", duration: 0.5 },
                    onClick: withModifiers(() => {
                    }, ["stop"]),
                    class: "bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                  }, {
                    default: withCtx(() => [
                      createVNode("div", { class: "relative bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6" }, [
                        createVNode("h2", { class: "text-3xl font-bold text-white mb-2" }, "Оформление заявки"),
                        createVNode("p", { class: "text-emerald-100" }, " Заполните форму и мы свяжемся с вами в ближайшее время "),
                        createVNode("button", {
                          onClick: closeModal,
                          class: "absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition"
                        }, [
                          createVNode(unref(X), { className: "w-5 h-5 text-white" })
                        ])
                      ]),
                      createVNode("form", { class: "p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-120px)]" }, [
                        createVNode("div", { class: "grid md:grid-cols-2 gap-6" }, [
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "name",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Ваше имя * ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(User), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "name",
                                name: "name",
                                type: "text",
                                required: "",
                                value: "{formData.name}",
                                placeholder: "Иван Иванов",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ]),
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "phone",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Телефон * ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Phone), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "phone",
                                name: "phone",
                                type: "tel",
                                required: "",
                                value: "{formData.phone}",
                                placeholder: "+7 (999) 123-45-67",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode(_sfc_main$i, {
                            htmlFor: "email",
                            class: "text-gray-700 font-medium"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Email ")
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "relative" }, [
                            createVNode(unref(Mail), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                            createVNode(_sfc_main$h, {
                              id: "email",
                              name: "email",
                              type: "email",
                              value: "{formData.email}",
                              placeholder: "example@mail.ru",
                              class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                            })
                          ])
                        ]),
                        createVNode("div", { class: "grid md:grid-cols-2 gap-6" }, [
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "car",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Марка и модель авто ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Car), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "car",
                                name: "car",
                                type: "text",
                                value: "{formData.car}",
                                placeholder: "Toyota Camry",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ]),
                          createVNode("div", { class: "space-y-2" }, [
                            createVNode(_sfc_main$i, {
                              htmlFor: "date",
                              class: "text-gray-700 font-medium"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Желаемая дата ")
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "relative" }, [
                              createVNode(unref(Calendar), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" }),
                              createVNode(_sfc_main$h, {
                                id: "date",
                                name: "date",
                                type: "date",
                                value: "{formData.date}",
                                class: "pl-12 h-12 border-2 border-gray-200 focus:border-emerald-500 rounded-xl"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode(_sfc_main$i, {
                            htmlFor: "message",
                            class: "text-gray-700 font-medium"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Комментарий ")
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$f, {
                            id: "message",
                            name: "message",
                            placeholder: "Дополнительная информация или вопросы...",
                            class: "min-h-[120px] border-2 border-gray-200 focus:border-emerald-500 rounded-xl resize-none"
                          })
                        ]),
                        createVNode("div", { class: "bg-gray-50 rounded-2xl p-4" }, [
                          createVNode("p", { class: "text-sm text-gray-600" }, ' Нажимая кнопку "Отправить заявку", вы соглашаетесь с политикой обработки персональных данных ')
                        ]),
                        createVNode(_sfc_main$g, {
                          type: "submit",
                          size: "lg",
                          class: "w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-xl shadow-emerald-500/30 h-14 text-lg"
                        }, {
                          default: withCtx(() => [
                            createVNode(unref(Phone), { class: "w-5 h-5 mr-2" }),
                            createTextVNode(" Отправить заявку ")
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    _: 1
                  }, 8, ["onClick"])
                ])
              ], 64)) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/OrderModal.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const isOpen = ref(false);
const selectedProduct = ref(null);
function useOrderModal() {
  const open = (product = null) => {
    selectedProduct.value = product;
    isOpen.value = true;
  };
  const close = () => {
    isOpen.value = false;
  };
  return {
    isOpen,
    selectedProduct,
    open,
    close
  };
}
const _sfc_main$d = {
  __name: "MainLayout",
  __ssrInlineRender: true,
  setup(__props) {
    const navigate = (url) => {
      if (!url) return;
      router.visit(url, {
        preserveScroll: true,
        preserveState: true
      });
    };
    const { isOpen: isOpen2, selectedProduct: selectedProduct2 } = useOrderModal();
    const page = usePage();
    const seo = computed(() => page.props.seo ?? {});
    const title = computed(() => seo.value.title || "Alarmstyle — автостудия");
    const settings = computed(() => page.props.settings ?? {});
    const contacts = computed(() => settings.value.contacts ?? {});
    computed(() => settings.value.company ?? {});
    computed(() => page.props.pageTitle ?? null);
    const breadcrumbs = computed(() => page.props.breadcrumbs ?? []);
    const jsonLd = computed(() => page.props.jsonLd ?? null);
    const mobileMenuOpen = ref(false);
    const callbackOpen = ref(false);
    const searchQuery = ref("");
    const searchFocused = ref(false);
    ref(false);
    const menus = computed(() => page.props.menus ?? {});
    const headerMenu = computed(() => menus.value.header ?? []);
    const popularSearches = [
      "Автосигнализация",
      "GPS-трекер",
      "Автозапуск",
      "Видеорегистратор"
    ];
    const handleClickOutside = (e) => {
      if (!e.target.closest(".search-container")) {
        searchFocused.value = false;
      }
    };
    onMounted(() => {
      document.addEventListener("click", handleClickOutside);
    });
    onUnmounted(() => {
      document.removeEventListener("click", handleClickOutside);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="min-h-screen flex flex-col bg-slate-50 text-slate-900">`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(title.value)}</title>`);
            if (seo.value.description) {
              _push2(`<meta name="description"${ssrRenderAttr("content", seo.value.description)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.keywords) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seo.value.keywords)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots"${ssrRenderAttr("content", seo.value.noindex ? "noindex, nofollow" : "index, follow")}${_scopeId}>`);
            if (seo.value.canonical) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", seo.value.canonical)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.og_title) {
              _push2(`<meta property="og:title"${ssrRenderAttr("content", seo.value.og_title)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.og_description) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seo.value.og_description)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.og_image) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seo.value.og_image)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.og_type) {
              _push2(`<meta property="og:type"${ssrRenderAttr("content", seo.value.og_type)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:url"${ssrRenderAttr("content", seo.value.url || (typeof _ctx.window !== "undefined" ? _ctx.window.location.href : ""))}${_scopeId}>`);
            if (seo.value.twitter_card) {
              _push2(`<meta name="twitter:card"${ssrRenderAttr("content", seo.value.twitter_card)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.twitter_title) {
              _push2(`<meta name="twitter:title"${ssrRenderAttr("content", seo.value.twitter_title)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.twitter_description) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seo.value.twitter_description)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seo.value.twitter_image) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", seo.value.twitter_image)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("title", null, toDisplayString(title.value), 1),
              seo.value.description ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "description",
                content: seo.value.description
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.keywords ? (openBlock(), createBlock("meta", {
                key: 1,
                name: "keywords",
                content: seo.value.keywords
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: seo.value.noindex ? "noindex, nofollow" : "index, follow"
              }, null, 8, ["content"]),
              seo.value.canonical ? (openBlock(), createBlock("link", {
                key: 2,
                rel: "canonical",
                href: seo.value.canonical
              }, null, 8, ["href"])) : createCommentVNode("", true),
              seo.value.og_title ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:title",
                content: seo.value.og_title
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.og_description ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:description",
                content: seo.value.og_description
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.og_image ? (openBlock(), createBlock("meta", {
                key: 5,
                property: "og:image",
                content: seo.value.og_image
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.og_type ? (openBlock(), createBlock("meta", {
                key: 6,
                property: "og:type",
                content: seo.value.og_type
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: seo.value.url || (typeof _ctx.window !== "undefined" ? _ctx.window.location.href : "")
              }, null, 8, ["content"]),
              seo.value.twitter_card ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:card",
                content: seo.value.twitter_card
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.twitter_title ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "twitter:title",
                content: seo.value.twitter_title
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.twitter_description ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "twitter:description",
                content: seo.value.twitter_description
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seo.value.twitter_image ? (openBlock(), createBlock("meta", {
                key: 10,
                name: "twitter:image",
                content: seo.value.twitter_image
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      if (searchFocused.value) {
        _push(`<div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"></div>`);
      } else {
        _push(`<!---->`);
      }
      if (mobileMenuOpen.value) {
        _push(`<div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] lg:hidden"></div>`);
      } else {
        _push(`<!---->`);
      }
      if (mobileMenuOpen.value) {
        _push(`<div class="fixed top-0 right-0 bottom-0 w-[85%] max-w-sm bg-gradient-to-br from-gray-50 to-white shadow-2xl z-[70] lg:hidden overflow-y-auto"><div class="sticky top-0 bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-lg"><div class="flex items-center justify-between mb-6"><div class="text-white"><div class="text-2xl font-bold"> ALARM<span class="text-white/90">STYLE</span></div><div class="text-xs text-white/80 uppercase tracking-wide mt-1"> Охранные системы </div></div><button class="p-2.5 rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-3 bg-white/10 backdrop-blur-sm rounded-2xl p-4"><a${ssrRenderAttr("href", `tel:${contacts.value.phone?.replace(/[^+0-9]/g, "")}`)} class="flex items-center gap-3 text-white hover:text-white/80 transition-colors"><div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></div><span class="text-sm font-medium">${ssrInterpolate(contacts.value.phone)}</span></a><a${ssrRenderAttr("href", `mailto:${contacts.value.email}`)} class="flex items-center gap-3 text-white hover:text-white/80 transition-colors"><div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div><span class="text-sm font-medium">${ssrInterpolate(contacts.value.email)}</span></a><a href="https://yandex.ru/maps/-/CDdkMLVl" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-white hover:text-white/80 transition-colors"><div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div><span class="text-sm font-medium">${ssrInterpolate(contacts.value.address)}</span></a><div class="flex gap-2 pt-2"><a${ssrRenderAttr("href", contacts.value.whatsapp)} target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110" title="WhatsApp"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg></a><a${ssrRenderAttr("href", contacts.value.telegram)} target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110" title="Telegram"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"></path></svg></a></div></div></div><nav class="p-6 space-y-2"><!--[-->`);
        ssrRenderList(_ctx.navLinks, (link, idx) => {
          _push(ssrRenderComponent(unref(Link), {
            key: link.href,
            href: link.href,
            onClick: ($event) => mobileMenuOpen.value = false,
            class: "flex items-center w-full text-left px-6 py-4 rounded-2xl font-medium transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(link.label)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(link.label), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--><button class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 h-14 rounded-2xl flex items-center justify-center font-semibold"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> Обратный звонок </button><div class="pt-6 mt-6 border-t border-gray-200"><div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-4 text-center"><svg class="w-5 h-5 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-sm text-emerald-900 font-medium">Работаем ежедневно</p><p class="text-lg font-bold text-emerald-700">с 9:00 до 21:00</p></div></div></nav></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/50"><div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3"><div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between gap-4 text-sm"><div class="flex items-center gap-4 sm:gap-6"><a${ssrRenderAttr("href", `tel:${contacts.value.phone?.replace(/[^+0-9]/g, "")}`)} class="flex items-center gap-2 hover:opacity-80 transition">`);
      _push(ssrRenderComponent(unref(Phone), { class: "w-4 h-4" }, null, _parent));
      _push(`<span class="hidden sm:inline">${ssrInterpolate(contacts.value.phone)}</span></a><a${ssrRenderAttr("href", `mailto:${contacts.value.email}`)} class="hidden lg:flex items-center gap-2 hover:opacity-80 transition">`);
      _push(ssrRenderComponent(unref(Mail), { class: "w-4 h-4" }, null, _parent));
      _push(`<span>${ssrInterpolate(contacts.value.email)}</span></a></div><div class="flex items-center gap-3 sm:gap-4"><div class="flex items-center gap-2 sm:gap-3"><a${ssrRenderAttr("href", contacts.value.whatsapp)} target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 transition-colors" title="WhatsApp"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg></a><a${ssrRenderAttr("href", contacts.value.telegram)} target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 transition-colors" title="Telegram"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"></path></svg></a></div><div class="hidden md:flex items-center gap-2">`);
      _push(ssrRenderComponent(unref(MapPin), { class: "w-4 h-4 flex-shrink-0" }, null, _parent));
      _push(`<span class="text-xs sm:text-sm">${ssrInterpolate(contacts.value.address)}</span></div></div></div></div><div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-5"><div class="flex items-center justify-between gap-4 sm:gap-8">`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/",
        class: "flex items-center gap-3 group flex-shrink-0"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="text-left"${_scopeId}><div class="text-lg sm:text-xl font-bold text-gray-900"${_scopeId}> ALARM<span class="text-emerald-600"${_scopeId}>STYLE</span></div><div class="text-xs text-gray-500 uppercase tracking-wide hidden sm:block"${_scopeId}> Охранные системы </div></div>`);
          } else {
            return [
              createVNode("div", { class: "text-left" }, [
                createVNode("div", { class: "text-lg sm:text-xl font-bold text-gray-900" }, [
                  createTextVNode(" ALARM"),
                  createVNode("span", { class: "text-emerald-600" }, "STYLE")
                ]),
                createVNode("div", { class: "text-xs text-gray-500 uppercase tracking-wide hidden sm:block" }, " Охранные системы ")
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="hidden lg:flex flex-1 max-w-xl relative search-container"><div class="relative w-full">`);
      _push(ssrRenderComponent(unref(Search), { class: "absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600 pointer-events-none" }, null, _parent));
      _push(`<input${ssrRenderAttr("value", searchQuery.value)} type="text" placeholder="Поиск услуг..." class="pl-12 h-11 bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl w-full outline-none focus:ring-1 focus:ring-emerald-500 transition-all"></div>`);
      if (searchFocused.value) {
        _push(`<div class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 z-50"><div class="flex items-center gap-2 mb-3"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg><span class="text-sm font-medium text-gray-700">Популярные запросы</span></div><div class="flex flex-wrap gap-2"><!--[-->`);
        ssrRenderList(popularSearches, (search, idx) => {
          _push(`<button class="px-4 py-2 bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200">${ssrInterpolate(search)}</button>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><nav class="hidden lg:flex items-center gap-4 xl:gap-6 flex-shrink-0"><!--[-->`);
      ssrRenderList(headerMenu.value, (item) => {
        _push(`<!--[-->`);
        if (!item.children?.length) {
          _push(ssrRenderComponent(unref(Link), {
            href: item.href || "#",
            class: "text-sm font-medium text-gray-700 hover:text-emerald-600 transition-colors",
            target: item.newTab ? "_blank" : null
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(item.label)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(item.label), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<div class="relative group"><button type="button" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 transition-colors">${ssrInterpolate(item.label)} <svg class="w-4 h-4 ml-1 transform transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button><div class="pointer-events-none absolute left-0 top-full w-56 pt-3 opacity-0 translate-y-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto z-50"><div class="bg-white rounded-xl shadow-2xl border border-gray-200 p-3"><!--[-->`);
          ssrRenderList(item.children, (child) => {
            _push(ssrRenderComponent(unref(Link), {
              key: child.label,
              href: child.href || "#",
              class: "block w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors",
              target: child.newTab ? "_blank" : null
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(child.label)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(child.label), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          });
          _push(`<!--]--></div></div></div>`);
        }
        _push(`<!--]-->`);
      });
      _push(`<!--]--><button class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md px-6 h-10 rounded-lg transition-colors duration-300 flex items-center font-medium"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> Обратный звонок </button></nav><button class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"><svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button></div><div class="lg:hidden mt-4 search-container"><div class="relative"><svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><input${ssrRenderAttr("value", searchQuery.value)} type="text" placeholder="Поиск..." class="pl-12 h-11 bg-gray-50 border border-gray-200 focus:border-emerald-500 rounded-xl w-full outline-none focus:ring-1 focus:ring-emerald-500">`);
      if (searchFocused.value) {
        _push(`<div class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 z-50"><div class="flex items-center gap-2 mb-3"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg><span class="text-sm font-medium text-gray-700">Популярные</span></div><div class="flex flex-wrap gap-2"><!--[-->`);
        ssrRenderList(popularSearches, (search, idx) => {
          _push(`<button class="px-3 py-2 bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200">${ssrInterpolate(search)}</button>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></header><div class="min-h-screen pt-40 pb-24 bg-gradient-to-br from-gray-50 to-white"><div class="max-w-7xl mx-auto px-4 sm:px-6">`);
      if (breadcrumbs.value.length) {
        _push(ssrRenderComponent(_sfc_main$k, {
          items: breadcrumbs.value,
          onNavigate: navigate
        }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</div></div><footer class="bg-gray-900 text-white"><div class="max-w-7xl mx-auto px-6 py-16"><div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12"><div><div class="mb-6"><div class="text-xl font-bold text-white mb-2"> ALARM<span class="text-emerald-500">STYLE</span></div><div class="text-sm text-gray-400 uppercase tracking-wide">Охранные системы</div></div><p class="text-gray-400 mb-6 leading-relaxed"> Профессиональная установка автомобильных охранных систем с гарантией качества </p></div><div><h3 class="font-bold text-white mb-6">Услуги</h3><ul class="space-y-3"><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Автосигнализации </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> GPS-трекеры </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Парктроники </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Камеры заднего вида </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Иммобилайзеры </a></li></ul></div><div><h3 class="font-bold text-white mb-6">Компания</h3><ul class="space-y-3"><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> О нас </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Наши работы </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Отзывы </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Гарантии </a></li><li><a href="#" class="text-gray-400 hover:text-emerald-400 transition"> Контакты </a></li></ul></div><div><h3 class="font-bold text-white mb-6">Контакты</h3><ul class="space-y-4"><li><a href="tel:+74994441439" class="flex items-start gap-3 text-gray-400 hover:text-emerald-400 transition">`);
      _push(ssrRenderComponent(unref(Phone), { class: "w-5 h-5 flex-shrink-0 mt-0.5" }, null, _parent));
      _push(`<span>8 (499) 444-14-39</span></a></li><li><a href="mailto:alarm@style@mail.ru" class="flex items-start gap-3 text-gray-400 hover:text-emerald-400 transition">`);
      _push(ssrRenderComponent(unref(Mail), { class: "w-5 h-5 flex-shrink-0 mt-0.5" }, null, _parent));
      _push(`<span>alarm@style@mail.ru</span></a></li><li><div class="flex items-start gap-3 text-gray-400">`);
      _push(ssrRenderComponent(unref(MapPin), { class: "w-5 h-5 flex-shrink-0 mt-0.5" }, null, _parent));
      _push(`<span>Москва, пр-кт Вернадского, 124</span></div></li><li><div class="flex items-start gap-3 text-gray-400">`);
      _push(ssrRenderComponent(unref(Clock), { class: "w-5 h-5 flex-shrink-0 mt-0.5" }, null, _parent));
      _push(`<span>Ежедневно 10:00-21:00</span></div></li></ul><div class="mt-6"><h4 class="font-bold text-white mb-4">Мессенджеры</h4><div class="flex gap-3"><a href="https://wa.me/74994441439" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-500 transition-colors" title="WhatsApp">`);
      _push(ssrRenderComponent(unref(MessageCircle), { class: "w-5 h-5" }, null, _parent));
      _push(`</a><a href="https://t.me/alarmstyle" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-500 transition-colors" title="Telegram">`);
      _push(ssrRenderComponent(unref(MessageCircle), { class: "w-5 h-5" }, null, _parent));
      _push(`</a></div></div></div></div><div class="border-t border-gray-800 pt-8"><div class="flex flex-col md:flex-row justify-between items-center gap-4"><p class="text-gray-400 text-sm"> © 2024 AlarmStyle. Все права защищены. </p><div class="flex gap-6"><a href="#" class="text-gray-400 hover:text-emerald-400 text-sm transition"> Политика конфиденциальности </a><a href="#" class="text-gray-400 hover:text-emerald-400 text-sm transition"> Публичная оферта </a></div></div></div></div></footer>`);
      _push(ssrRenderComponent(_sfc_main$e, {
        open: unref(isOpen2),
        "onUpdate:open": ($event) => isRef(isOpen2) ? isOpen2.value = $event : null,
        product: unref(selectedProduct2)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$j, {
        open: callbackOpen.value,
        onClose: ($event) => callbackOpen.value = false
      }, null, _parent));
      _push(`</div>`);
      if (jsonLd.value) {
        ssrRenderVNode(_push, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, null), _parent);
      } else {
        _push(`<!---->`);
      }
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/MainLayout.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const _sfc_main$c = /* @__PURE__ */ Object.assign({
  inheritAttrs: false
}, {
  __name: "Checkbox",
  __ssrInlineRender: true,
  setup(__props) {
    const attrs = useAttrs();
    const rootClass = computed(
      () => cn(
        "peer  bg-input-background data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50",
        attrs.class
      )
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(CheckboxRoot), mergeProps(unref(attrs), {
        "data-slot": "checkbox",
        class: rootClass.value
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(CheckboxIndicator), {
              "data-slot": "checkbox-indicator",
              class: "flex items-center justify-center text-current transition-none"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(Check), { class: "size-3.5" }, null, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(unref(Check), { class: "size-3.5" })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(unref(CheckboxIndicator), {
                "data-slot": "checkbox-indicator",
                class: "flex items-center justify-center text-current transition-none"
              }, {
                default: withCtx(() => [
                  createVNode(unref(Check), { class: "size-3.5" })
                ]),
                _: 1
              })
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
});
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Checkbox.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$b = {
  __name: "AttributeFilter",
  __ssrInlineRender: true,
  props: {
    attribute: {
      type: Object,
      required: true
    },
    facets: {
      type: Object,
      required: true
    },
    selectedSlugs: {
      type: Array,
      required: true
    },
    categorySlug: {
      type: String,
      required: true
    },
    filters: {
      type: Object,
      required: true
    }
  },
  emits: ["navigate"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const typeFront = computed(() => Number(props.attribute.type_front ?? 1));
    function getValueSlug(value) {
      return value.slug?.slug ?? value.slug ?? value.slug_string ?? value.slug_value ?? null;
    }
    const attributeValueSlugs = computed(
      () => (props.attribute.values || []).map((v) => getValueSlug(v)).filter(Boolean)
    );
    const selectedSlugsSet = computed(() => new Set(props.selectedSlugs || []));
    computed(() => {
      if (typeFront.value !== 3) return null;
      for (const slug of attributeValueSlugs.value) {
        if (selectedSlugsSet.value.has(slug)) {
          return slug;
        }
      }
      return null;
    });
    function buildBasePath(slugs = []) {
      const arr = Array.isArray(slugs) ? [...slugs] : [];
      if (arr.length === 0) {
        return props.categorySlug;
      }
      return props.categorySlug + "/" + arr.join("/");
    }
    function buildCatalogUrl(path, query = {}) {
      let url = "/category";
      if (path) {
        url += "/" + String(path).replace(/^\/+/, "");
      }
      const q = new URLSearchParams();
      const fullQuery = { ...props.filters, ...query };
      Object.entries(fullQuery).forEach(([key, value]) => {
        if (value !== void 0 && value !== null && value !== "") {
          q.append(key, String(value));
        }
      });
      const qs = q.toString();
      if (qs) url += "?" + qs;
      return url;
    }
    computed(() => {
      if (typeFront.value !== 3) return null;
      let resetSlugs = props.selectedSlugs.filter(
        (s) => !attributeValueSlugs.value.includes(s)
      );
      resetSlugs.sort();
      const path = buildBasePath(resetSlugs);
      return buildCatalogUrl(path);
    });
    function isValueActive(value) {
      const valueSlug = getValueSlug(value);
      return valueSlug && selectedSlugsSet.value.has(valueSlug);
    }
    function isValueDisabled(value) {
      const canUse = props.facets?.[value.id] ?? true;
      const active = isValueActive(value);
      return !canUse && !active;
    }
    function valueUrl(value) {
      const valueSlug = getValueSlug(value);
      if (!valueSlug) return null;
      const active = isValueActive(value);
      const canUse = props.facets?.[value.id] ?? true;
      if (!canUse && !active) return null;
      let newSlugs = [...props.selectedSlugs];
      if (typeFront.value !== 1 && !active) {
        newSlugs = newSlugs.filter(
          (s) => !attributeValueSlugs.value.includes(s)
        );
      }
      if (active) {
        newSlugs = newSlugs.filter((s) => s !== valueSlug);
      } else {
        newSlugs.push(valueSlug);
      }
      newSlugs.sort();
      const path = buildBasePath(newSlugs);
      return buildCatalogUrl(path);
    }
    function onClickValue(e, value) {
      const url = valueUrl(value);
      if (url) emit("navigate", url);
    }
    const isOpen2 = ref(true);
    ref(null);
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.attribute.values && __props.attribute.values.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white rounded-xl p-5 border border-gray-200" }, _attrs))} data-v-f307d5a6><button class="flex items-center justify-between w-full mb-4" type="button" data-v-f307d5a6><h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide" data-v-f307d5a6>${ssrInterpolate(__props.attribute.name)}</h3>`);
        if (isOpen2.value) {
          _push(ssrRenderComponent(unref(ChevronUp), { class: "w-5 h-5 text-gray-500" }, null, _parent));
        } else {
          _push(ssrRenderComponent(unref(ChevronDown), { class: "w-5 h-5 text-gray-500" }, null, _parent));
        }
        _push(`</button><div style="${ssrRenderStyle(isOpen2.value ? null : { display: "none" })}" data-v-f307d5a6><div class="space-y-3" data-v-f307d5a6><!--[-->`);
        ssrRenderList(__props.attribute.values, (value) => {
          _push(`<!--[-->`);
          if (isValueDisabled(value)) {
            _push(`<div class="flex items-center gap-2" data-v-f307d5a6>`);
            _push(ssrRenderComponent(_sfc_main$c, {
              id: `attr-${__props.attribute.id}-value-${value.id}`,
              checked: isValueActive(value),
              disabled: "disabled"
            }, null, _parent));
            _push(ssrRenderComponent(_sfc_main$i, {
              forHtml: `attr-${__props.attribute.id}-value-${value.id}`,
              class: "text-sm text-gray-600 cursor-pointer flex-1 font-normal opacity-60"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(value.value)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(value.value), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
            _push(`</div>`);
          } else {
            _push(`<div class="flex items-center gap-2" data-v-f307d5a6>`);
            _push(ssrRenderComponent(_sfc_main$c, {
              id: `attr-${__props.attribute.id}-value-${value.id}`,
              checked: isValueActive(value),
              onClick: ($event) => onClickValue($event, value)
            }, null, _parent));
            _push(ssrRenderComponent(_sfc_main$i, {
              forHtml: `attr-${__props.attribute.id}-value-${value.id}`,
              class: "text-sm text-gray-600 cursor-pointer flex-1 font-normal",
              onClick: ($event) => onClickValue($event, value)
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(ssrRenderComponent(unref(Link), {
                    href: valueUrl(value)
                  }, {
                    default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                      if (_push3) {
                        _push3(`${ssrInterpolate(value.value)}`);
                      } else {
                        return [
                          createTextVNode(toDisplayString(value.value), 1)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent2, _scopeId));
                } else {
                  return [
                    createVNode(unref(Link), {
                      href: valueUrl(value)
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(value.value), 1)
                      ]),
                      _: 2
                    }, 1032, ["href"])
                  ];
                }
              }),
              _: 2
            }, _parent));
            _push(`</div>`);
          }
          _push(`<!--]-->`);
        });
        _push(`<!--]--></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/AttributeFilter.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const AttributeFilter = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["__scopeId", "data-v-f307d5a6"]]);
const _sfc_main$a = {
  __name: "CatalogSidebar",
  __ssrInlineRender: true,
  props: {
    category: {
      type: Object,
      required: true
    },
    categories: {
      type: Array,
      required: true
    },
    attributes: {
      type: Array,
      required: true
    },
    facets: {
      type: Object,
      required: true
    },
    selectedSlugs: {
      type: Array,
      required: true
    },
    priceBounds: {
      type: Object,
      default: () => ({})
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    categorySlug: {
      type: String,
      required: true
    },
    isMobileOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: ["navigate", "close-mobile"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const minPrice = computed(() => props.priceBounds?.min ?? null);
    const maxPrice = computed(() => props.priceBounds?.max ?? null);
    const priceFrom = ref(props.filters.price_from ?? "");
    const priceTo = ref(props.filters.price_to ?? "");
    const isSyncingFilters = ref(false);
    watch(
      () => props.filters,
      async (f) => {
        isSyncingFilters.value = true;
        priceFrom.value = f.price_from ?? "";
        priceTo.value = f.price_to ?? "";
        await nextTick();
        isSyncingFilters.value = false;
      },
      { deep: true }
    );
    function buildCatalogUrl(path = "", query = {}) {
      let url = "/category";
      if (path) {
        url += "/" + path.replace(/^\/+/, "");
      }
      const qs = new URLSearchParams();
      Object.entries(query).forEach(([key, value]) => {
        if (value !== null && value !== void 0 && value !== "") {
          qs.append(key, value);
        }
      });
      const q = qs.toString();
      if (q.length > 0) {
        url += "?" + q;
      }
      return url;
    }
    function applyPriceFilter() {
      const query = { ...props.filters };
      query.price_from = priceFrom.value || void 0;
      query.price_to = priceTo.value || void 0;
      let basePath = props.categorySlug;
      if (props.selectedSlugs.length) {
        basePath += "/" + props.selectedSlugs.join("/");
      }
      const url = buildCatalogUrl(basePath, query);
      emit("navigate", url);
    }
    function handlePriceChange() {
      if (isSyncingFilters.value) return;
      applyPriceFilter();
    }
    function categoryUrl(slug) {
      return buildCatalogUrl(slug, props.filters);
    }
    function getValueSlug(value) {
      return value.slug?.slug ?? value.slug ?? value.slug_string ?? value.slug_value ?? null;
    }
    const selectedSlugsSet = computed(() => new Set(props.selectedSlugs || []));
    const sortedAttributes = computed(() => {
      return [...props.attributes].sort((a, b) => {
        const aHasSelected = (a.values || []).some(
          (v) => selectedSlugsSet.value.has(getValueSlug(v))
        );
        const bHasSelected = (b.values || []).some(
          (v) => selectedSlugsSet.value.has(getValueSlug(v))
        );
        if (aHasSelected === bHasSelected) return 0;
        return aHasSelected ? -1 : 1;
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      ssrRenderTeleport(_push, (_push2) => {
        if (__props.isMobileOpen) {
          _push2(`<div class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (__props.isMobileOpen) {
          _push2(`<div class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-white z-50 lg:hidden overflow-y-auto"><div class="sticky top-0 bg-white border-b border-gray-200 px-5 py-4 flex items-center justify-between"><h2 class="text-lg font-semibold text-gray-900">Фильтры</h2><button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="p-5 space-y-6"><div class="bg-gray-50 rounded-xl p-4 border border-gray-200"><h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Категории</h3><div class="space-y-2"><button class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 bg-emerald-100 text-emerald-700 font-medium">${ssrInterpolate(__props.category.name)}</button><!--[-->`);
          ssrRenderList(__props.categories, (cat) => {
            _push2(`<button class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">${ssrInterpolate(cat.name)}</button>`);
          });
          _push2(`<!--]--></div></div><div class="bg-gray-50 rounded-xl p-4 border border-gray-200"><h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Цена</h3><div class="space-y-4"><div class="flex gap-3">`);
          _push2(ssrRenderComponent(_sfc_main$h, {
            modelValue: priceFrom.value,
            "onUpdate:modelValue": ($event) => priceFrom.value = $event,
            name: "price_from",
            min: minPrice.value !== null ? parseInt(minPrice.value) : void 0,
            type: "number",
            placeholder: minPrice.value !== null ? parseInt(minPrice.value) : void 0,
            class: "h-10 border-2 border-gray-200 rounded-lg",
            onChange: handlePriceChange,
            onKeyup: handlePriceChange
          }, null, _parent));
          _push2(ssrRenderComponent(_sfc_main$h, {
            modelValue: priceTo.value,
            "onUpdate:modelValue": ($event) => priceTo.value = $event,
            name: "price_to",
            max: maxPrice.value !== null ? parseInt(maxPrice.value) : void 0,
            type: "number",
            placeholder: maxPrice.value !== null ? parseInt(maxPrice.value) : void 0,
            class: "h-10 border-2 border-gray-200 rounded-lg",
            onChange: handlePriceChange,
            onKeyup: handlePriceChange
          }, null, _parent));
          _push2(`</div></div></div><!--[-->`);
          ssrRenderList(sortedAttributes.value, (attribute) => {
            _push2(ssrRenderComponent(AttributeFilter, {
              key: attribute.id,
              attribute,
              facets: __props.facets,
              "selected-slugs": __props.selectedSlugs,
              "category-slug": __props.categorySlug,
              filters: __props.filters,
              onNavigate: ($event) => emit("navigate", $event)
            }, null, _parent));
          });
          _push2(`<!--]--></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      }, "body", false, _parent);
      _push(`<aside class="hidden lg:block w-72 flex-shrink-0"><div class="sticky top-36 space-y-6"><div class="bg-white rounded-xl p-5 border border-gray-200"><h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Категории</h3><div class="space-y-2"><a${ssrRenderAttr("href", categoryUrl(__props.categorySlug))} class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 bg-emerald-100 text-emerald-700 font-medium">${ssrInterpolate(__props.category.name)}</a><!--[-->`);
      ssrRenderList(__props.categories, (cat) => {
        _push(`<a${ssrRenderAttr("href", categoryUrl(cat.slug?.slug ?? cat.slug ?? ""))} class="w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">${ssrInterpolate(cat.name)}</a>`);
      });
      _push(`<!--]--></div></div><div class="bg-white rounded-xl p-5 border border-gray-200"><h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Цена</h3><div class="space-y-4"><div class="flex gap-3">`);
      _push(ssrRenderComponent(_sfc_main$h, {
        modelValue: priceFrom.value,
        "onUpdate:modelValue": ($event) => priceFrom.value = $event,
        name: "price_from",
        min: minPrice.value !== null ? parseInt(minPrice.value) : void 0,
        type: "number",
        placeholder: minPrice.value !== null ? parseInt(minPrice.value) : void 0,
        class: "h-10 border-2 border-gray-200 rounded-lg",
        onChange: handlePriceChange,
        onKeyup: handlePriceChange
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$h, {
        modelValue: priceTo.value,
        "onUpdate:modelValue": ($event) => priceTo.value = $event,
        name: "price_to",
        max: maxPrice.value !== null ? parseInt(maxPrice.value) : void 0,
        type: "number",
        placeholder: maxPrice.value !== null ? parseInt(maxPrice.value) : void 0,
        class: "h-10 border-2 border-gray-200 rounded-lg",
        onChange: handlePriceChange,
        onKeyup: handlePriceChange
      }, null, _parent));
      _push(`</div></div></div><!--[-->`);
      ssrRenderList(sortedAttributes.value, (attribute) => {
        _push(ssrRenderComponent(AttributeFilter, {
          key: attribute.id,
          attribute,
          facets: __props.facets,
          "selected-slugs": __props.selectedSlugs,
          "category-slug": __props.categorySlug,
          filters: __props.filters,
          onNavigate: ($event) => emit("navigate", $event)
        }, null, _parent));
      });
      _push(`<!--]--></div></aside><!--]-->`);
    };
  }
};
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CatalogSidebar.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const Select = defineComponent({
  name: "Select",
  setup(_, { attrs, slots }) {
    return () => h(
      SelectRoot,
      {
        "data-slot": "select",
        ...attrs
      },
      slots
    );
  }
});
defineComponent({
  name: "SelectGroup",
  setup(_, { attrs, slots }) {
    return () => h(
      SelectGroup,
      {
        "data-slot": "select-group",
        ...attrs
      },
      slots
    );
  }
});
const SelectValue = defineComponent({
  name: "SelectValue",
  setup(_, { attrs, slots }) {
    return () => h(
      SelectValue$1,
      {
        "data-slot": "select-value",
        ...attrs
      },
      slots
    );
  }
});
const SelectTrigger = defineComponent({
  name: "SelectTrigger",
  props: {
    size: {
      type: String,
      default: "default"
      // "sm" | "default"
    }
  },
  setup(props, { attrs, slots }) {
    const triggerClass = computed(
      () => cn(
        "border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-full items-center justify-between gap-2 rounded-md border bg-input-background px-3 py-2 text-sm whitespace-nowrap transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
        attrs.class
      )
    );
    return () => h(
      SelectTrigger$1,
      {
        ...attrs,
        "data-slot": "select-trigger",
        "data-size": props.size,
        class: triggerClass.value
      },
      {
        default: () => [
          slots.default?.(),
          h(
            SelectIcon,
            null,
            {
              default: () => h(ChevronDown, { class: "size-4 opacity-50" })
            }
          )
        ]
      }
    );
  }
});
const SelectContent = defineComponent({
  name: "SelectContent",
  props: {
    position: {
      type: String,
      default: "popper"
      // "popper" | "item-aligned"
    }
  },
  setup(props, { attrs, slots }) {
    const contentClass = computed(
      () => cn(
        "bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 relative z-50 max-h-(--radix-select-content-available-height) min-w-[8rem] origin-(--radix-select-content-transform-origin) overflow-x-hidden overflow-y-auto rounded-md border shadow-md",
        props.position === "popper" && "data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1",
        attrs.class
      )
    );
    const viewportClass = computed(
      () => cn(
        "p-1",
        props.position === "popper" && "h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)] scroll-my-1"
      )
    );
    return () => h(
      SelectPortal,
      null,
      {
        default: () => h(
          SelectContent$1,
          {
            ...attrs,
            "data-slot": "select-content",
            class: contentClass.value,
            position: props.position
          },
          {
            default: () => [
              h(SelectScrollUpButton),
              h(
                SelectViewport,
                { class: viewportClass.value },
                {
                  default: () => slots.default?.()
                }
              ),
              h(SelectScrollDownButton)
            ]
          }
        )
      }
    );
  }
});
defineComponent({
  name: "SelectLabel",
  setup(_, { attrs, slots }) {
    const labelClass = computed(
      () => cn("text-muted-foreground px-2 py-1.5 text-xs", attrs.class)
    );
    return () => h(
      SelectLabel,
      {
        ...attrs,
        "data-slot": "select-label",
        class: labelClass.value
      },
      slots
    );
  }
});
const SelectItem = defineComponent({
  name: "SelectItem",
  setup(_, { attrs, slots }) {
    const itemClass = computed(
      () => cn(
        "focus:bg-accent focus:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex w-full cursor-default items-center gap-2 rounded-sm py-1.5 pr-8 pl-2 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 *:[span]:last:flex *:[span]:last:items-center *:[span]:last:gap-2",
        attrs.class
      )
    );
    return () => h(
      SelectItem$1,
      {
        ...attrs,
        "data-slot": "select-item",
        class: itemClass.value
      },
      {
        default: () => [
          h(
            "span",
            {
              class: "absolute right-2 flex size-3.5 items-center justify-center"
            },
            [
              h(
                SelectItemIndicator,
                null,
                {
                  default: () => h(Check, { class: "size-4" })
                }
              )
            ]
          ),
          h(
            SelectItemText,
            null,
            {
              default: () => slots.default?.()
            }
          )
        ]
      }
    );
  }
});
defineComponent({
  name: "SelectSeparator",
  setup(_, { attrs }) {
    const separatorClass = computed(
      () => cn(
        "bg-border pointer-events-none -mx-1 my-1 h-px",
        attrs.class
      )
    );
    return () => h(
      SelectSeparator,
      {
        ...attrs,
        "data-slot": "select-separator",
        class: separatorClass.value
      }
    );
  }
});
const SelectScrollUpButton = defineComponent({
  name: "SelectScrollUpButton",
  setup(_, { attrs }) {
    const btnClass = computed(
      () => cn(
        "flex cursor-default items-center justify-center py-1",
        attrs.class
      )
    );
    return () => h(
      SelectScrollUpButton$1,
      {
        ...attrs,
        "data-slot": "select-scroll-up-button",
        class: btnClass.value
      },
      {
        default: () => h(ChevronUp, { class: "size-4" })
      }
    );
  }
});
const SelectScrollDownButton = defineComponent({
  name: "SelectScrollDownButton",
  setup(_, { attrs }) {
    const btnClass = computed(
      () => cn(
        "flex cursor-default items-center justify-center py-1",
        attrs.class
      )
    );
    return () => h(
      SelectScrollDownButton$1,
      {
        ...attrs,
        "data-slot": "select-scroll-down-button",
        class: btnClass.value
      },
      {
        default: () => h(ChevronDown, { class: "size-4" })
      }
    );
  }
});
const _sfc_main$9 = {
  __name: "Image",
  __ssrInlineRender: true,
  props: {
    src: {
      type: String,
      required: true
    },
    // пресет 1x, например "product.card"
    preset: {
      type: String,
      required: true
    },
    alt: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<img${ssrRenderAttrs(mergeProps({
        src: _ctx.route("images.show", { path: __props.src, preset: __props.preset }),
        srcset: [
          _ctx.route("images.show", { path: __props.src, preset: __props.preset }) + " 1x",
          _ctx.route("images.show", { path: __props.src, preset: __props.preset + "_2x" }) + " 2x"
        ].join(", "),
        alt: __props.alt,
        loading: "lazy"
      }, _ctx.$attrs, _attrs))}>`);
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ui/Image.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {
  __name: "ProductCart",
  __ssrInlineRender: true,
  props: {
    product: { Object, required: true }
  },
  setup(__props) {
    const { open } = useOrderModal();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(motion).div, mergeProps({
        initial: { opacity: 0, y: 20 },
        whileInView: { opacity: 1, y: 0 },
        inViewOptions: { once: true },
        class: "group bg-white rounded-2xl overflow-hidden shadow-md border border-gray-200 hover:shadow-lg hover:border-emerald-500 transition-all duration-300 flex flex-col h-full"
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="relative overflow-hidden bg-gray-100 aspect-[4/3]"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: `/product/${__props.product.slug}`,
              class: "block w-full h-full"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  if (__props.product.image) {
                    _push3(ssrRenderComponent(_sfc_main$9, {
                      style: __props.product.image ? null : { display: "none" },
                      src: __props.product.image,
                      preset: "product.card",
                      alt: __props.product.name,
                      class: "w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    }, null, _parent3, _scopeId2));
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    __props.product.image ? withDirectives((openBlock(), createBlock(_sfc_main$9, {
                      key: 0,
                      src: __props.product.image,
                      preset: "product.card",
                      alt: __props.product.name,
                      class: "w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    }, null, 8, ["src", "alt"])), [
                      [vShow, __props.product.image]
                    ]) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm rounded-full px-3 py-1.5 flex items-center gap-1 shadow-md"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Star), { class: "w-4 h-4 fill-yellow-400 text-yellow-400" }, null, _parent2, _scopeId));
            _push2(`<span class="text-sm font-medium text-gray-900"${_scopeId}>${ssrInterpolate(__props.product.rating)}</span></div></div><div class="p-5 flex flex-col flex-1"${_scopeId}><div class="text-sm text-emerald-600 font-medium mb-1"${_scopeId}>${ssrInterpolate(__props.product.brand)}</div>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: `/product/${__props.product.slug}`
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<h3 class="font-bold text-gray-900 mb-4 line-clamp-2 text-lg hover:text-emerald-600 transition-colors duration-200"${_scopeId2}>${ssrInterpolate(__props.product.name)}</h3>`);
                } else {
                  return [
                    createVNode("h3", { class: "font-bold text-gray-900 mb-4 line-clamp-2 text-lg hover:text-emerald-600 transition-colors duration-200" }, toDisplayString(__props.product.name), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="flex gap-2 mb-4"${_scopeId}><div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200" title="GSM-модуль" style="${ssrRenderStyle(__props.product.gsm === true ? null : { display: "none" })}"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Radio), { class: "w-4 h-4 text-emerald-600" }, null, _parent2, _scopeId));
            _push2(`<span class="text-xs font-medium text-emerald-700"${_scopeId}>GSM</span></div><div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 rounded-lg border border-blue-200" title="GPS/ГЛОНАСС" style="${ssrRenderStyle(__props.product.gps === true ? null : { display: "none" })}"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Navigation), { class: "w-4 h-4 text-blue-600" }, null, _parent2, _scopeId));
            _push2(`<span class="text-xs font-medium text-blue-700"${_scopeId}>GPS</span></div><div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 rounded-lg border border-purple-200" title="Автозапуск" style="${ssrRenderStyle(__props.product.auto === true ? null : { display: "none" })}"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Zap), { class: "w-4 h-4 text-purple-600" }, null, _parent2, _scopeId));
            _push2(`<span class="text-xs font-medium text-purple-700"${_scopeId}>Авто</span></div></div><div class="mb-5"${_scopeId}><div class="text-3xl font-bold text-gray-900 mb-1"${_scopeId}>${ssrInterpolate(__props.product.price.toLocaleString())} ₽</div><div class="text-sm text-gray-500"${_scopeId}>с установкой</div></div><div class="flex gap-2 mt-auto"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: `/product/${__props.product.slug}`,
              class: "flex-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(_sfc_main$g, {
                    variant: "outline",
                    class: "w-full border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200"
                  }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Подробнее `);
                      } else {
                        return [
                          createTextVNode(" Подробнее ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(_sfc_main$g, {
                      variant: "outline",
                      class: "w-full border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200"
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Подробнее ")
                      ]),
                      _: 1
                    })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$g, {
              onClick: ($event) => unref(open)(__props.product),
              class: "flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-all duration-200"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(ShoppingCart), { class: "w-4 h-4 mr-2" }, null, _parent3, _scopeId2));
                  _push3(` Заказать `);
                } else {
                  return [
                    createVNode(unref(ShoppingCart), { class: "w-4 h-4 mr-2" }),
                    createTextVNode(" Заказать ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "relative overflow-hidden bg-gray-100 aspect-[4/3]" }, [
                createVNode(unref(Link), {
                  href: `/product/${__props.product.slug}`,
                  class: "block w-full h-full"
                }, {
                  default: withCtx(() => [
                    __props.product.image ? withDirectives((openBlock(), createBlock(_sfc_main$9, {
                      key: 0,
                      src: __props.product.image,
                      preset: "product.card",
                      alt: __props.product.name,
                      class: "w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    }, null, 8, ["src", "alt"])), [
                      [vShow, __props.product.image]
                    ]) : createCommentVNode("", true)
                  ]),
                  _: 1
                }, 8, ["href"]),
                createVNode("div", { class: "absolute top-3 right-3 bg-white/95 backdrop-blur-sm rounded-full px-3 py-1.5 flex items-center gap-1 shadow-md" }, [
                  createVNode(unref(Star), { class: "w-4 h-4 fill-yellow-400 text-yellow-400" }),
                  createVNode("span", { class: "text-sm font-medium text-gray-900" }, toDisplayString(__props.product.rating), 1)
                ])
              ]),
              createVNode("div", { class: "p-5 flex flex-col flex-1" }, [
                createVNode("div", { class: "text-sm text-emerald-600 font-medium mb-1" }, toDisplayString(__props.product.brand), 1),
                createVNode(unref(Link), {
                  href: `/product/${__props.product.slug}`
                }, {
                  default: withCtx(() => [
                    createVNode("h3", { class: "font-bold text-gray-900 mb-4 line-clamp-2 text-lg hover:text-emerald-600 transition-colors duration-200" }, toDisplayString(__props.product.name), 1)
                  ]),
                  _: 1
                }, 8, ["href"]),
                createVNode("div", { class: "flex gap-2 mb-4" }, [
                  withDirectives(createVNode("div", {
                    class: "flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200",
                    title: "GSM-модуль"
                  }, [
                    createVNode(unref(Radio), { class: "w-4 h-4 text-emerald-600" }),
                    createVNode("span", { class: "text-xs font-medium text-emerald-700" }, "GSM")
                  ], 512), [
                    [vShow, __props.product.gsm === true]
                  ]),
                  withDirectives(createVNode("div", {
                    class: "flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 rounded-lg border border-blue-200",
                    title: "GPS/ГЛОНАСС"
                  }, [
                    createVNode(unref(Navigation), { class: "w-4 h-4 text-blue-600" }),
                    createVNode("span", { class: "text-xs font-medium text-blue-700" }, "GPS")
                  ], 512), [
                    [vShow, __props.product.gps === true]
                  ]),
                  withDirectives(createVNode("div", {
                    class: "flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 rounded-lg border border-purple-200",
                    title: "Автозапуск"
                  }, [
                    createVNode(unref(Zap), { class: "w-4 h-4 text-purple-600" }),
                    createVNode("span", { class: "text-xs font-medium text-purple-700" }, "Авто")
                  ], 512), [
                    [vShow, __props.product.auto === true]
                  ])
                ]),
                createVNode("div", { class: "mb-5" }, [
                  createVNode("div", { class: "text-3xl font-bold text-gray-900 mb-1" }, toDisplayString(__props.product.price.toLocaleString()) + " ₽", 1),
                  createVNode("div", { class: "text-sm text-gray-500" }, "с установкой")
                ]),
                createVNode("div", { class: "flex gap-2 mt-auto" }, [
                  createVNode(unref(Link), {
                    href: `/product/${__props.product.slug}`,
                    class: "flex-1"
                  }, {
                    default: withCtx(() => [
                      createVNode(_sfc_main$g, {
                        variant: "outline",
                        class: "w-full border-2 border-gray-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Подробнее ")
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  createVNode(_sfc_main$g, {
                    onClick: ($event) => unref(open)(__props.product),
                    class: "flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-all duration-200"
                  }, {
                    default: withCtx(() => [
                      createVNode(unref(ShoppingCart), { class: "w-4 h-4 mr-2" }),
                      createTextVNode(" Заказать ")
                    ]),
                    _: 1
                  }, 8, ["onClick"])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ProductCart.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const defaultSort = "popular_desc";
const _sfc_main$7 = {
  __name: "CatalogProducts",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Object,
      required: true
      // paginator
    },
    categorySlug: {
      type: String,
      required: true
    },
    selectedSlugs: {
      type: Array,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    attributes: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ["navigate", "open-filters"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const sortOptions = [
      { value: "popular_desc", label: "По популярности" },
      { value: "price_asc", label: "Сначала дешевле" },
      { value: "price_desc", label: "Сначала дороже" }
    ];
    const currentSort = computed(() => {
      const sort = props.filters?.sort;
      const allowed = sortOptions.map((o) => o.value);
      if (typeof sort === "string" && allowed.includes(sort)) {
        return sort;
      }
      return defaultSort;
    });
    function buildBasePath(customSlugs = null) {
      const slugs = customSlugs ?? props.selectedSlugs;
      if (!slugs.length) {
        return props.categorySlug;
      }
      return props.categorySlug + "/" + slugs.join("/");
    }
    function buildCatalogUrl(path = "", query = {}) {
      let url = "/category";
      if (path) {
        url += "/" + path.replace(/^\/+/, "");
      }
      const qs = new URLSearchParams();
      Object.entries(query).forEach(([key, value]) => {
        if (value !== null && value !== void 0 && value !== "") {
          qs.append(key, value);
        }
      });
      const q = qs.toString();
      if (q.length > 0) {
        url += "?" + q;
      }
      return url;
    }
    function sortUrl(value) {
      const basePath = buildBasePath();
      const query = { ...props.filters, sort: value };
      return buildCatalogUrl(basePath, query);
    }
    function navigateToSort(value) {
      const url = sortUrl(value);
      emit("navigate", url);
    }
    function goToPage(link) {
      if (!link.url || link.active) return;
      emit("navigate", link.url);
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "flex-1" }, _attrs))}><div class="flex items-center justify-between gap-3 mb-6"><button type="button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg:not([class*=&#39;size-&#39;])]:size-4 shrink-0 [&amp;_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 aria-invalid:border-destructive bg-background text-foreground hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 has-[&gt;svg]:px-3 lg:hidden border-2 border-gray-200 hover:border-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal w-5 h-5 mr-2" data-fg-b4ig99="1.34:81.464:/src/app/components/Catalog.tsx:498:21:19341:46:e:SlidersHorizontal::::::BJAT"><line x1="21" x2="14" y1="4" y2="4"></line><line x1="10" x2="3" y1="4" y2="4"></line><line x1="21" x2="12" y1="12" y2="12"></line><line x1="8" x2="3" y1="12" y2="12"></line><line x1="21" x2="16" y1="20" y2="20"></line><line x1="12" x2="3" y1="20" y2="20"></line><line x1="14" x2="14" y1="2" y2="6"></line><line x1="8" x2="8" y1="10" y2="14"></line><line x1="16" x2="16" y1="18" y2="22"></line></svg> Фильтры </button>`);
      _push(ssrRenderComponent(unref(Select), {
        modelValue: currentSort.value,
        "onUpdate:modelValue": navigateToSort
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(SelectTrigger), { class: "w-full sm:w-56 h-11 border-2 border-gray-200 rounded-xl" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(SelectValue), null, null, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(unref(SelectValue))
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(SelectContent), null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<!--[-->`);
                  ssrRenderList(sortOptions, (option) => {
                    _push3(ssrRenderComponent(unref(SelectItem), {
                      key: option.value,
                      value: option.value
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`${ssrInterpolate(option.label)}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString(option.label), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                  });
                  _push3(`<!--]-->`);
                } else {
                  return [
                    (openBlock(), createBlock(Fragment, null, renderList(sortOptions, (option) => {
                      return createVNode(unref(SelectItem), {
                        key: option.value,
                        value: option.value
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(option.label), 1)
                        ]),
                        _: 2
                      }, 1032, ["value"]);
                    }), 64))
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(unref(SelectTrigger), { class: "w-full sm:w-56 h-11 border-2 border-gray-200 rounded-xl" }, {
                default: withCtx(() => [
                  createVNode(unref(SelectValue))
                ]),
                _: 1
              }),
              createVNode(unref(SelectContent), null, {
                default: withCtx(() => [
                  (openBlock(), createBlock(Fragment, null, renderList(sortOptions, (option) => {
                    return createVNode(unref(SelectItem), {
                      key: option.value,
                      value: option.value
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(option.label), 1)
                      ]),
                      _: 2
                    }, 1032, ["value"]);
                  }), 64))
                ]),
                _: 1
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
      if (__props.loading) {
        _push(`<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6"><!--[-->`);
        ssrRenderList(8, (n) => {
          _push(`<div class="rounded-2xl border border-slate-100 bg-white overflow-hidden animate-pulse"><div class="p-4 space-y-3"><div class="h-4 bg-slate-200 rounded w-4/5"></div><div class="h-3 bg-slate-200 rounded w-1/2"></div><div class="h-9 bg-slate-200/80 rounded-xl w-full mt-4"></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-8">`);
        if (__props.items.data && __props.items.data.length) {
          _push(`<!--[-->`);
          ssrRenderList(__props.items.data, (item) => {
            _push(ssrRenderComponent(_sfc_main$8, {
              key: item.id,
              product: item
            }, null, _parent));
          });
          _push(`<!--]-->`);
        } else {
          _push(`<p class="text-sm text-slate-500"> Товары не найдены. </p>`);
        }
        _push(`</div>`);
      }
      if (__props.items.links && __props.items.links.length > 1) {
        _push(`<div class="mt-6 flex flex-wrap items-center justify-center gap-2"><!--[-->`);
        ssrRenderList(__props.items.links, (link) => {
          _push(ssrRenderComponent(_sfc_main$g, {
            key: link.label + (link.url || ""),
            variant: "outline",
            type: "button",
            disabled: !link.url,
            onClick: ($event) => goToPage(link),
            class: ["border-2 rounded-xl text-xs sm:text-sm transition-colors flex items-center justify-center", [
              // Числовые страницы — квадратные кнопки
              Number.isInteger(Number(link.label)) ? "h-10 w-10 p-0" : "px-3 py-2",
              // Активная страница
              link.active ? "bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600" : "bg-white border-gray-200 text-gray-900 hover:border-emerald-600 hover:bg-emerald-50",
              // Отключенные (нет url) — как disabled
              !link.url ? "opacity-50 cursor-default pointer-events-none" : ""
            ]]
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span${_scopeId}>${link.label ?? ""}</span>`);
              } else {
                return [
                  createVNode("span", {
                    innerHTML: link.label
                  }, null, 8, ["innerHTML"])
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</section>`);
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CatalogProducts.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
  __name: "CatalogLanding",
  __ssrInlineRender: true,
  props: {
    landing: {
      type: Object,
      default: null
    },
    category: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "mt-8" }, _attrs))}>`);
      if (__props.landing && __props.landing.content) {
        _push(`<div class="mb-6 prose max-w-none">${__props.landing.content ?? ""}</div>`);
      } else if (__props.category && __props.category.description) {
        _push(`<div class="mb-6 prose max-w-none text-sm text-gray-700">${__props.category.description ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</section>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CatalogLanding.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "CatalogQuickLinks",
  __ssrInlineRender: true,
  props: {
    links: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.links.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-wrap gap-3 mb-6" }, _attrs))}><!--[-->`);
        ssrRenderList(__props.links, (btn, i) => {
          _push(ssrRenderComponent(_sfc_main$g, {
            key: i,
            href: btn.href,
            variant: "ghost",
            class: "bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 transition-all duration-200"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(btn.label)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(btn.label), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CatalogQuickLinks.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "CatalogActiveFilters",
  __ssrInlineRender: true,
  props: {
    /** Массив атрибутов с их значениями */
    attributes: {
      type: Array,
      default: () => []
    },
    /** Массив выбранных slug'ов атрибутов */
    selectedSlugs: {
      type: Array,
      default: () => []
    },
    /** Фильтры (price_from, price_to, sort, etc.) */
    filters: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ["remove-attribute", "clear-price", "reset-all"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    function getValueSlug(value) {
      return value.slug?.slug ?? value.slug ?? value.slug_string ?? value.slug_value ?? null;
    }
    const selectedChips = computed(() => {
      const chips = [];
      const selectedSet = new Set(props.selectedSlugs || []);
      for (const attr of props.attributes || []) {
        for (const val of attr.values || []) {
          const slug = getValueSlug(val);
          if (!slug) continue;
          if (!selectedSet.has(slug)) continue;
          chips.push({
            type: "attribute",
            key: slug,
            label: `${attr.name}: ${val.value}`
          });
        }
      }
      const from = props.filters.price_from;
      const to = props.filters.price_to;
      if (from || to) {
        let label = "Цена: ";
        if (from && to) {
          label += `${Number(from).toLocaleString("ru-RU")}–${Number(to).toLocaleString("ru-RU")} ₽`;
        } else if (from) {
          label += `от ${Number(from).toLocaleString("ru-RU")} ₽`;
        } else if (to) {
          label += `до ${Number(to).toLocaleString("ru-RU")} ₽`;
        }
        chips.push({
          type: "price",
          key: "price",
          label
        });
      }
      return chips;
    });
    function handleResetAll() {
      emit("reset-all");
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white rounded-xl p-4 mb-6 border border-gray-200" }, _attrs, {
        style: selectedChips.value.length ? null : { display: "none" }
      }))}><div class="flex items-center justify-between mb-3"><span class="text-sm font-medium text-gray-700">Активные фильтры:</span>`);
      _push(ssrRenderComponent(_sfc_main$g, {
        onClick: handleResetAll,
        size: "sm",
        variant: "ghost"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Очистить все`);
          } else {
            return [
              createTextVNode("Очистить все")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="flex flex-wrap gap-2"><!--[-->`);
      ssrRenderList(selectedChips.value, (chip) => {
        _push(`<div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium">${ssrInterpolate(chip.label)} <button class="hover:bg-emerald-200 rounded-full p-0.5 transition">`);
        _push(ssrRenderComponent(unref(X), { class: "w-3.5 h-3.5" }, null, _parent));
        _push(`</button></div>`);
      });
      _push(`<!--]--></div></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/CatalogActiveFilters.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = /* @__PURE__ */ Object.assign({ layout: _sfc_main$d }, {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const category = computed(() => page.props.category);
    const categories = computed(() => page.props.categories);
    const items = computed(() => page.props.items);
    const attributes = computed(() => page.props.attributes);
    const selectedValueSlugs = computed(() => page.props.selectedValueSlugs);
    const facets = computed(() => page.props.facets);
    const priceBounds = computed(() => page.props.priceBounds);
    const categorySlug = computed(() => page.props.categorySlug);
    const landing = computed(() => page.props.landing);
    const filters = computed(() => page.props.filters || {});
    const quickLinks = computed(() => page.props.quickLinks || []);
    const isLoading = ref(false);
    const catalogTop = ref(null);
    const showFilters = ref(false);
    function navigate(url) {
      showFilters.value = false;
      router.visit(url, {
        preserveScroll: true,
        preserveState: true
      });
    }
    function buildBasePath(customSlugs = null) {
      const slugs = customSlugs ?? selectedValueSlugs.value;
      if (!slugs.length) {
        return categorySlug.value;
      }
      return categorySlug.value + "/" + slugs.join("/");
    }
    function buildCatalogUrl(path = "", query = {}) {
      let url = "/category";
      if (path) {
        url += "/" + path.replace(/^\/+/, "");
      }
      const qs = new URLSearchParams();
      Object.entries(query).forEach(([key, value]) => {
        if (value !== null && value !== void 0 && value !== "") {
          qs.append(key, value);
        }
      });
      const q = qs.toString();
      if (q.length > 0) {
        url += "?" + q;
      }
      return url;
    }
    function removeAttributeFilter(slug) {
      const newSlugs = selectedValueSlugs.value.filter((s) => s !== slug);
      const basePath = buildBasePath(newSlugs);
      const query = { ...filters.value };
      const url = buildCatalogUrl(basePath, query);
      navigate(url);
    }
    function clearPriceFilter() {
      const basePath = buildBasePath();
      const query = {
        ...filters.value,
        price_from: void 0,
        price_to: void 0
      };
      const url = buildCatalogUrl(basePath, query);
      navigate(url);
    }
    function resetAll() {
      const url = buildCatalogUrl(categorySlug.value, {});
      navigate(url);
    }
    function scrollToTop() {
      if (!catalogTop.value) return;
      const rect = catalogTop.value.getBoundingClientRect();
      const top = rect.top + window.scrollY - 80;
      window.scrollTo({
        top,
        behavior: "smooth"
      });
    }
    function onStart() {
      isLoading.value = true;
    }
    function onFinish() {
      isLoading.value = false;
      scrollToTop();
    }
    const offStart = ref(null);
    const offFinish = ref(null);
    onMounted(() => {
      offStart.value = router.on("start", onStart);
      offFinish.value = router.on("finish", onFinish);
    });
    onBeforeUnmount(() => {
      if (offStart.value) offStart.value();
      if (offFinish.value) offFinish.value();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="flex items-center justify-between mb-8"><div><h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-2">Каталог</h1></div></div>`);
      _push(ssrRenderComponent(_sfc_main$5, { links: quickLinks.value }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$4, {
        attributes: attributes.value,
        "selected-slugs": selectedValueSlugs.value,
        filters: filters.value,
        onRemoveAttribute: removeAttributeFilter,
        onClearPrice: clearPriceFilter,
        onResetAll: resetAll
      }, null, _parent));
      _push(`<div class="flex gap-8">`);
      _push(ssrRenderComponent(_sfc_main$a, {
        category: category.value,
        categories: categories.value,
        attributes: attributes.value,
        facets: facets.value,
        "selected-slugs": selectedValueSlugs.value,
        "price-bounds": priceBounds.value,
        filters: filters.value,
        "category-slug": categorySlug.value,
        "is-mobile-open": showFilters.value,
        onNavigate: navigate,
        onCloseMobile: ($event) => showFilters.value = false
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$7, {
        items: items.value,
        "category-slug": categorySlug.value,
        "selected-slugs": selectedValueSlugs.value,
        filters: filters.value,
        attributes: attributes.value,
        loading: isLoading.value,
        onNavigate: navigate,
        onOpenFilters: ($event) => showFilters.value = true
      }, null, _parent));
      if (items.value.current_page === 1) {
        _push(ssrRenderComponent(_sfc_main$6, {
          landing: landing.value,
          category: category.value
        }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div><!--]-->`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Catalog/Index.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$3
}, Symbol.toStringTag, { value: "Module" }));
const __variableDynamicImportRuntimeHelper = (glob$1, path$13, segs) => {
  const v = glob$1[path$13];
  if (v) return typeof v === "function" ? v() : Promise.resolve(v);
  return new Promise((_, reject) => {
    (typeof queueMicrotask === "function" ? queueMicrotask : setTimeout)(reject.bind(null, /* @__PURE__ */ new Error("Unknown variable dynamic import: " + path$13 + (path$13.split("/").length !== segs ? ". Note that variables only represent file names one level deep." : ""))));
  });
};
const _sfc_main$2 = {
  __name: "BlocksRenderer",
  __ssrInlineRender: true,
  props: {
    blocks: Array
  },
  setup(__props) {
    const resolveComponent = (type) => {
      if (!type) return null;
      const name = type.split("_").map((part) => part.charAt(0).toUpperCase() + part.slice(1)).join("") + "Block";
      return defineAsyncComponent(
        () => __variableDynamicImportRuntimeHelper(/* @__PURE__ */ Object.assign({ "./Blocks/TextBlock.vue": () => import("./assets/TextBlock-CGKeZuxb.js") }), `./Blocks/${name}.vue`, 3)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-6" }, _attrs))}><!--[-->`);
      ssrRenderList(__props.blocks, (block, index) => {
        _push(`<!--[-->`);
        if (resolveComponent(block.type)) {
          ssrRenderVNode(_push, createVNode(resolveDynamicComponent(resolveComponent(block.type)), { block }, null), _parent);
        } else {
          _push(`<pre class="text-xs bg-gray-50 p-2 rounded border border-dashed">Unknown block type: ${ssrInterpolate(block.type)}
${ssrInterpolate(block)}
      </pre>`);
        }
        _push(`<!--]-->`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/BlocksRenderer.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ Object.assign({ layout: _sfc_main$d }, {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    page: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$2, mergeProps({
        blocks: __props.page.blocks || []
      }, _attrs), null, _parent));
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Page/Show.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$1
}, Symbol.toStringTag, { value: "Module" }));
const TooltipProvider = defineComponent({
  name: "TooltipProvider",
  props: {
    delayDuration: { type: Number, default: 0 }
  },
  setup(props, { slots, attrs }) {
    return () => h(
      TooltipProvider$1,
      {
        "data-slot": "tooltip-provider",
        delayDuration: props.delayDuration,
        ...attrs
      },
      slots
    );
  }
});
const Tooltip = defineComponent({
  name: "Tooltip",
  setup(_, { slots, attrs }) {
    return () => h(
      TooltipProvider,
      { delayDuration: 0 },
      // можешь прокинуть сверху, если нужно — см. примечание ниже
      {
        default: () => h(
          TooltipRoot,
          {
            "data-slot": "tooltip",
            ...attrs
          },
          slots
        )
      }
    );
  }
});
const TooltipTrigger = defineComponent({
  name: "TooltipTrigger",
  setup(_, { slots, attrs }) {
    return () => h(
      TooltipTrigger$1,
      {
        "data-slot": "tooltip-trigger",
        ...attrs
      },
      slots
    );
  }
});
const TooltipContent = defineComponent({
  name: "TooltipContent",
  props: {
    sideOffset: { type: Number, default: 0 },
    className: { type: [String, Array, Object], default: "" }
  },
  setup(props, { slots, attrs }) {
    const klass = computed(
      () => cn(
        "bg-primary text-primary-foreground animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 w-fit origin-(--radix-tooltip-content-transform-origin) rounded-md px-3 py-1.5 text-xs text-balance",
        props.className
      )
    );
    return () => h(TooltipPortal, null, {
      default: () => h(
        TooltipContent$1,
        {
          "data-slot": "tooltip-content",
          sideOffset: props.sideOffset,
          class: klass.value,
          ...attrs
        },
        {
          default: () => [
            slots.default?.(),
            h(TooltipArrow, {
              class: "bg-primary fill-primary z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]"
            })
          ]
        }
      )
    });
  }
});
const _sfc_main = /* @__PURE__ */ Object.assign({ layout: _sfc_main$d }, {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    product: {
      type: Object,
      required: true
    },
    brand: {
      type: String,
      required: false
    },
    attributeFeature: {
      type: Array,
      required: false
    }
  },
  setup(__props) {
    const props = __props;
    const mainSplide = ref(null);
    const thumbsSplide = ref(null);
    const galleryImages = computed(() => {
      return props.product.gallery || [props.product.image];
    });
    const { isOpen: isOrderOpen, selectedProduct: selectedProduct2, open: openOrder } = useOrderModal();
    const callbackOpen = ref(false);
    const openOrderModal = () => {
      openOrder(props.product);
    };
    const openCallbackModal = () => {
      callbackOpen.value = true;
    };
    const openGroups = ref(/* @__PURE__ */ new Set());
    const toggleGroup = (index) => {
      if (openGroups.value.has(index)) {
        openGroups.value.delete(index);
      } else {
        openGroups.value.add(index);
      }
      openGroups.value = new Set(openGroups.value);
    };
    const isGroupOpen = (index) => {
      return openGroups.value.has(index);
    };
    const mainOptions = {
      type: "fade",
      rewind: true,
      pagination: false,
      arrows: true,
      heightRatio: 0.75,
      cover: true
    };
    const thumbsOptions = {
      fixedWidth: 100,
      fixedHeight: 100,
      gap: 12,
      rewind: true,
      pagination: false,
      arrows: false,
      isNavigation: true,
      cover: true,
      // было: focus: 'center',
      focus: 0,
      // было: trimSpace: false,
      trimSpace: true,
      autoHeight: true,
      breakpoints: {
        640: {
          fixedWidth: 70,
          fixedHeight: 70
        }
      }
    };
    let lightbox = null;
    onMounted(() => {
      if (mainSplide.value && thumbsSplide.value) {
        mainSplide.value.sync(thumbsSplide.value.splide);
      }
      lightbox = new PhotoSwipeLightbox({
        gallery: "#product-gallery",
        children: "a",
        pswpModule: () => import("photoswipe"),
        padding: { top: 30, bottom: 30, left: 20, right: 20 },
        bgOpacity: 0.9
      });
      lightbox.init();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="grid lg:grid-cols-2 gap-12 lg:gap-16" data-v-6db90438>`);
      _push(ssrRenderComponent(unref(motion).div, {
        initial: { opacity: 0, scale: 0.95 },
        animate: { opacity: 1, scale: 1 },
        transition: { duration: 0.5 },
        class: "space-y-6 h-fit"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="flex justify-end" data-v-6db90438${_scopeId}><div class="bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 flex items-center gap-2 shadow-lg border border-gray-200" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Star), { class: "w-5 h-5 fill-yellow-400 text-yellow-400" }, null, _parent2, _scopeId));
            _push2(`<span class="font-medium text-gray-900" data-v-6db90438${_scopeId}>${ssrInterpolate(__props.product.rating)}</span></div></div><div id="product-gallery" class="rounded-2xl overflow-hidden bg-white shadow-xl border border-gray-200" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Splide), {
              ref_key: "mainSplide",
              ref: mainSplide,
              options: mainOptions,
              class: "product-gallery-main"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<!--[-->`);
                  ssrRenderList(galleryImages.value, (image, index) => {
                    _push3(ssrRenderComponent(unref(SplideSlide), { key: index }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`<a${ssrRenderAttr("href", _ctx.route("images.show", { path: image, preset: "product.lightbox" }))}${ssrRenderAttr("data-pswp-width", 1200)}${ssrRenderAttr("data-pswp-height", 900)} target="_blank" rel="noreferrer" class="block cursor-zoom-in" data-v-6db90438${_scopeId3}>`);
                          _push4(ssrRenderComponent(_sfc_main$9, {
                            src: image,
                            preset: "product.card",
                            alt: `${__props.product.name} ${index + 1}`,
                            class: "w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                          }, null, _parent4, _scopeId3));
                          _push4(`</a>`);
                        } else {
                          return [
                            createVNode("a", {
                              href: _ctx.route("images.show", { path: image, preset: "product.lightbox" }),
                              "data-pswp-width": 1200,
                              "data-pswp-height": 900,
                              target: "_blank",
                              rel: "noreferrer",
                              class: "block cursor-zoom-in"
                            }, [
                              createVNode(_sfc_main$9, {
                                src: image,
                                preset: "product.card",
                                alt: `${__props.product.name} ${index + 1}`,
                                class: "w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                              }, null, 8, ["src", "alt"])
                            ], 8, ["href"])
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                  });
                  _push3(`<!--]-->`);
                } else {
                  return [
                    (openBlock(true), createBlock(Fragment, null, renderList(galleryImages.value, (image, index) => {
                      return openBlock(), createBlock(unref(SplideSlide), { key: index }, {
                        default: withCtx(() => [
                          createVNode("a", {
                            href: _ctx.route("images.show", { path: image, preset: "product.lightbox" }),
                            "data-pswp-width": 1200,
                            "data-pswp-height": 900,
                            target: "_blank",
                            rel: "noreferrer",
                            class: "block cursor-zoom-in"
                          }, [
                            createVNode(_sfc_main$9, {
                              src: image,
                              preset: "product.card",
                              alt: `${__props.product.name} ${index + 1}`,
                              class: "w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            }, null, 8, ["src", "alt"])
                          ], 8, ["href"])
                        ]),
                        _: 2
                      }, 1024);
                    }), 128))
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            if (galleryImages.value.length > 1) {
              _push2(`<div class="pb-4" data-v-6db90438${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Splide), {
                ref_key: "thumbsSplide",
                ref: thumbsSplide,
                options: thumbsOptions,
                class: "product-gallery-thumbs"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<!--[-->`);
                    ssrRenderList(galleryImages.value, (image, index) => {
                      _push3(ssrRenderComponent(unref(SplideSlide), { key: index }, {
                        default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                          if (_push4) {
                            _push4(`<div class="cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all" data-v-6db90438${_scopeId3}>`);
                            _push4(ssrRenderComponent(_sfc_main$9, {
                              src: image,
                              preset: "product.thumbnail",
                              alt: `${__props.product.name} ${index + 1}`,
                              class: "w-full h-full object-cover"
                            }, null, _parent4, _scopeId3));
                            _push4(`</div>`);
                          } else {
                            return [
                              createVNode("div", { class: "cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all" }, [
                                createVNode(_sfc_main$9, {
                                  src: image,
                                  preset: "product.thumbnail",
                                  alt: `${__props.product.name} ${index + 1}`,
                                  class: "w-full h-full object-cover"
                                }, null, 8, ["src", "alt"])
                              ])
                            ];
                          }
                        }),
                        _: 2
                      }, _parent3, _scopeId2));
                    });
                    _push3(`<!--]-->`);
                  } else {
                    return [
                      (openBlock(true), createBlock(Fragment, null, renderList(galleryImages.value, (image, index) => {
                        return openBlock(), createBlock(unref(SplideSlide), { key: index }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all" }, [
                              createVNode(_sfc_main$9, {
                                src: image,
                                preset: "product.thumbnail",
                                alt: `${__props.product.name} ${index + 1}`,
                                class: "w-full h-full object-cover"
                              }, null, 8, ["src", "alt"])
                            ])
                          ]),
                          _: 2
                        }, 1024);
                      }), 128))
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("div", { class: "flex justify-end" }, [
                createVNode("div", { class: "bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 flex items-center gap-2 shadow-lg border border-gray-200" }, [
                  createVNode(unref(Star), { class: "w-5 h-5 fill-yellow-400 text-yellow-400" }),
                  createVNode("span", { class: "font-medium text-gray-900" }, toDisplayString(__props.product.rating), 1)
                ])
              ]),
              createVNode("div", {
                id: "product-gallery",
                class: "rounded-2xl overflow-hidden bg-white shadow-xl border border-gray-200"
              }, [
                createVNode(unref(Splide), {
                  ref_key: "mainSplide",
                  ref: mainSplide,
                  options: mainOptions,
                  class: "product-gallery-main"
                }, {
                  default: withCtx(() => [
                    (openBlock(true), createBlock(Fragment, null, renderList(galleryImages.value, (image, index) => {
                      return openBlock(), createBlock(unref(SplideSlide), { key: index }, {
                        default: withCtx(() => [
                          createVNode("a", {
                            href: _ctx.route("images.show", { path: image, preset: "product.lightbox" }),
                            "data-pswp-width": 1200,
                            "data-pswp-height": 900,
                            target: "_blank",
                            rel: "noreferrer",
                            class: "block cursor-zoom-in"
                          }, [
                            createVNode(_sfc_main$9, {
                              src: image,
                              preset: "product.card",
                              alt: `${__props.product.name} ${index + 1}`,
                              class: "w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            }, null, 8, ["src", "alt"])
                          ], 8, ["href"])
                        ]),
                        _: 2
                      }, 1024);
                    }), 128))
                  ]),
                  _: 1
                }, 512)
              ]),
              galleryImages.value.length > 1 ? (openBlock(), createBlock("div", {
                key: 0,
                class: "pb-4"
              }, [
                createVNode(unref(Splide), {
                  ref_key: "thumbsSplide",
                  ref: thumbsSplide,
                  options: thumbsOptions,
                  class: "product-gallery-thumbs"
                }, {
                  default: withCtx(() => [
                    (openBlock(true), createBlock(Fragment, null, renderList(galleryImages.value, (image, index) => {
                      return openBlock(), createBlock(unref(SplideSlide), { key: index }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-emerald-400 transition-all" }, [
                            createVNode(_sfc_main$9, {
                              src: image,
                              preset: "product.thumbnail",
                              alt: `${__props.product.name} ${index + 1}`,
                              class: "w-full h-full object-cover"
                            }, null, 8, ["src", "alt"])
                          ])
                        ]),
                        _: 2
                      }, 1024);
                    }), 128))
                  ]),
                  _: 1
                }, 512)
              ])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(unref(motion).div, {
        initial: { opacity: 0, y: 20 },
        animate: { opacity: 1, y: 0 },
        transition: { duration: 0.5, delay: 0.2 }
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (__props.brand) {
              _push2(`<div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-4" data-v-6db90438${_scopeId}><span class="text-sm font-medium text-emerald-700" data-v-6db90438${_scopeId}>${ssrInterpolate(__props.brand)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<h1 class="text-4xl font-bold text-gray-900 mb-6" data-v-6db90438${_scopeId}>${ssrInterpolate(__props.product.name)}</h1><div class="flex items-baseline gap-3 mb-10" data-v-6db90438${_scopeId}><div class="text-5xl font-bold text-gray-900" data-v-6db90438${_scopeId}>${ssrInterpolate(__props.product.price.toLocaleString())} ₽ </div><div class="text-lg text-gray-500" data-v-6db90438${_scopeId}> c установкой </div></div><div class="grid grid-cols-3 gap-4 mb-10" data-v-6db90438${_scopeId}><div class="bg-white rounded-xl p-4 border border-gray-200 text-center" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Shield), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }, null, _parent2, _scopeId));
            _push2(`<div class="text-sm text-gray-700 font-medium" data-v-6db90438${_scopeId}>Гарантия 1 год</div></div><div class="bg-white rounded-xl p-4 border border-gray-200 text-center" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Clock), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }, null, _parent2, _scopeId));
            _push2(`<div class="text-sm text-gray-700 font-medium" data-v-6db90438${_scopeId}>Установка 2-3 часа</div></div><div class="bg-white rounded-xl p-4 border border-gray-200 text-center" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Wrench), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }, null, _parent2, _scopeId));
            _push2(`<div class="text-sm text-gray-700 font-medium" data-v-6db90438${_scopeId}>Бесплатная настройка</div></div></div><div class="flex gap-4 mb-12" data-v-6db90438${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              onClick: openOrderModal,
              size: "lg",
              class: "flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 h-14"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(ShoppingCart), { class: "w-5 h-5 mr-2" }, null, _parent3, _scopeId2));
                  _push3(` Заказать установку `);
                } else {
                  return [
                    createVNode(unref(ShoppingCart), { class: "w-5 h-5 mr-2" }),
                    createTextVNode(" Заказать установку ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$g, {
              onClick: openCallbackModal,
              size: "lg",
              variant: "outline",
              class: "border-2 border-gray-300 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 h-14 px-6 transition-all duration-200"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(Phone), { class: "w-5 h-5" }, null, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(unref(Phone), { class: "w-5 h-5" })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.attributeFeature.length > 0) {
              _push2(`<div class="bg-white rounded-xl border border-gray-200 p-6" data-v-6db90438${_scopeId}><h3 class="text-lg font-semibold text-gray-900 mb-4" data-v-6db90438${_scopeId}>Основные особенности</h3><div class="space-y-3" data-v-6db90438${_scopeId}><!--[-->`);
              ssrRenderList(__props.attributeFeature, (feature, index) => {
                _push2(`<div class="flex items-start gap-3" data-v-6db90438${_scopeId}><div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" data-v-6db90438${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Check), { class: "w-4 h-4 text-emerald-600" }, null, _parent2, _scopeId));
                _push2(`</div><p class="text-sm text-gray-700 leading-relaxed" data-v-6db90438${_scopeId}>${ssrInterpolate(feature)}</p></div>`);
              });
              _push2(`<!--]--></div></div>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              __props.brand ? (openBlock(), createBlock("div", {
                key: 0,
                class: "inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-4"
              }, [
                createVNode("span", { class: "text-sm font-medium text-emerald-700" }, toDisplayString(__props.brand), 1)
              ])) : createCommentVNode("", true),
              createVNode("h1", { class: "text-4xl font-bold text-gray-900 mb-6" }, toDisplayString(__props.product.name), 1),
              createVNode("div", { class: "flex items-baseline gap-3 mb-10" }, [
                createVNode("div", { class: "text-5xl font-bold text-gray-900" }, toDisplayString(__props.product.price.toLocaleString()) + " ₽ ", 1),
                createVNode("div", { class: "text-lg text-gray-500" }, " c установкой ")
              ]),
              createVNode("div", { class: "grid grid-cols-3 gap-4 mb-10" }, [
                createVNode("div", { class: "bg-white rounded-xl p-4 border border-gray-200 text-center" }, [
                  createVNode(unref(Shield), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }),
                  createVNode("div", { class: "text-sm text-gray-700 font-medium" }, "Гарантия 1 год")
                ]),
                createVNode("div", { class: "bg-white rounded-xl p-4 border border-gray-200 text-center" }, [
                  createVNode(unref(Clock), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }),
                  createVNode("div", { class: "text-sm text-gray-700 font-medium" }, "Установка 2-3 часа")
                ]),
                createVNode("div", { class: "bg-white rounded-xl p-4 border border-gray-200 text-center" }, [
                  createVNode(unref(Wrench), { class: "w-6 h-6 text-emerald-600 mx-auto mb-2" }),
                  createVNode("div", { class: "text-sm text-gray-700 font-medium" }, "Бесплатная настройка")
                ])
              ]),
              createVNode("div", { class: "flex gap-4 mb-12" }, [
                createVNode(_sfc_main$g, {
                  onClick: openOrderModal,
                  size: "lg",
                  class: "flex-1 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 h-14"
                }, {
                  default: withCtx(() => [
                    createVNode(unref(ShoppingCart), { class: "w-5 h-5 mr-2" }),
                    createTextVNode(" Заказать установку ")
                  ]),
                  _: 1
                }),
                createVNode(_sfc_main$g, {
                  onClick: openCallbackModal,
                  size: "lg",
                  variant: "outline",
                  class: "border-2 border-gray-300 hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 h-14 px-6 transition-all duration-200"
                }, {
                  default: withCtx(() => [
                    createVNode(unref(Phone), { class: "w-5 h-5" })
                  ]),
                  _: 1
                })
              ]),
              __props.attributeFeature.length > 0 ? (openBlock(), createBlock("div", {
                key: 1,
                class: "bg-white rounded-xl border border-gray-200 p-6"
              }, [
                createVNode("h3", { class: "text-lg font-semibold text-gray-900 mb-4" }, "Основные особенности"),
                createVNode("div", { class: "space-y-3" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList(__props.attributeFeature, (feature, index) => {
                    return openBlock(), createBlock("div", {
                      key: index,
                      class: "flex items-start gap-3"
                    }, [
                      createVNode("div", { class: "w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" }, [
                        createVNode(unref(Check), { class: "w-4 h-4 text-emerald-600" })
                      ]),
                      createVNode("p", { class: "text-sm text-gray-700 leading-relaxed" }, toDisplayString(feature), 1)
                    ]);
                  }), 128))
                ])
              ])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
      _push(ssrRenderComponent(unref(motion).div, {
        initial: { opacity: 0, y: 20 },
        animate: { opacity: 1, y: 0 },
        transition: { duration: 0.5, delay: 0.4 },
        class: "mt-16"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<h2 class="text-2xl font-semibold text-gray-800 mb-6" data-v-6db90438${_scopeId}>Технические характеристики</h2><div class="space-y-3" data-v-6db90438${_scopeId}><!--[-->`);
            ssrRenderList(__props.product.attributeGroups, (group, index) => {
              _push2(`<div class="bg-white rounded-xl border border-gray-200 overflow-hidden" data-v-6db90438${_scopeId}><button class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors" data-v-6db90438${_scopeId}><h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide" data-v-6db90438${_scopeId}>${ssrInterpolate(group.name)}</h3>`);
              _push2(ssrRenderComponent(unref(ChevronDown), {
                class: ["w-5 h-5 text-gray-400 transition-transform duration-300 ease-in-out", { "rotate-180": isGroupOpen(index) }]
              }, null, _parent2, _scopeId));
              _push2(`</button><div class="grid overflow-hidden" style="${ssrRenderStyle(isGroupOpen(index) ? null : { display: "none" })}" data-v-6db90438${_scopeId}><div class="min-h-0 border-t border-gray-200" data-v-6db90438${_scopeId}><div class="grid sm:grid-cols-2 xl:grid-cols-4" data-v-6db90438${_scopeId}><!--[-->`);
              ssrRenderList(group.attributes, (attribute, key) => {
                _push2(`<div class="flex flex-col gap-1 px-4 py-3 border-b border-r border-gray-100 last:border-r-0" data-v-6db90438${_scopeId}><div class="flex items-center gap-1.5" data-v-6db90438${_scopeId}><span class="text-xs text-gray-500" data-v-6db90438${_scopeId}>${ssrInterpolate(attribute.name)}</span>`);
                if (attribute.helper_text) {
                  _push2(ssrRenderComponent(unref(Tooltip), null, {
                    default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                      if (_push3) {
                        _push3(ssrRenderComponent(unref(TooltipTrigger), { asChild: "" }, {
                          default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                            if (_push4) {
                              _push4(`<button class="text-gray-400 hover:text-gray-600 transition-colors" data-v-6db90438${_scopeId3}>`);
                              _push4(ssrRenderComponent(unref(Info), { class: "w-3.5 h-3.5" }, null, _parent4, _scopeId3));
                              _push4(`</button>`);
                            } else {
                              return [
                                createVNode("button", { class: "text-gray-400 hover:text-gray-600 transition-colors" }, [
                                  createVNode(unref(Info), { class: "w-3.5 h-3.5" })
                                ])
                              ];
                            }
                          }),
                          _: 2
                        }, _parent3, _scopeId2));
                        _push3(ssrRenderComponent(unref(TooltipContent), {
                          side: "top",
                          class: "max-w-xs bg-gray-900 text-white p-3 rounded-lg"
                        }, {
                          default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                            if (_push4) {
                              _push4(`<p class="text-xs leading-relaxed" data-v-6db90438${_scopeId3}>${ssrInterpolate(attribute.helper_text)}</p>`);
                            } else {
                              return [
                                createVNode("p", { class: "text-xs leading-relaxed" }, toDisplayString(attribute.helper_text), 1)
                              ];
                            }
                          }),
                          _: 2
                        }, _parent3, _scopeId2));
                      } else {
                        return [
                          createVNode(unref(TooltipTrigger), { asChild: "" }, {
                            default: withCtx(() => [
                              createVNode("button", { class: "text-gray-400 hover:text-gray-600 transition-colors" }, [
                                createVNode(unref(Info), { class: "w-3.5 h-3.5" })
                              ])
                            ]),
                            _: 1
                          }),
                          createVNode(unref(TooltipContent), {
                            side: "top",
                            class: "max-w-xs bg-gray-900 text-white p-3 rounded-lg"
                          }, {
                            default: withCtx(() => [
                              createVNode("p", { class: "text-xs leading-relaxed" }, toDisplayString(attribute.helper_text), 1)
                            ]),
                            _: 2
                          }, 1024)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent2, _scopeId));
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div><span class="text-sm font-medium text-gray-900" data-v-6db90438${_scopeId}>${ssrInterpolate(attribute.value)}</span></div>`);
              });
              _push2(`<!--]--></div></div></div></div>`);
            });
            _push2(`<!--]--></div>`);
          } else {
            return [
              createVNode("h2", { class: "text-2xl font-semibold text-gray-800 mb-6" }, "Технические характеристики"),
              createVNode("div", { class: "space-y-3" }, [
                (openBlock(true), createBlock(Fragment, null, renderList(__props.product.attributeGroups, (group, index) => {
                  return openBlock(), createBlock("div", {
                    key: index,
                    class: "bg-white rounded-xl border border-gray-200 overflow-hidden"
                  }, [
                    createVNode("button", {
                      onClick: ($event) => toggleGroup(index),
                      class: "w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors"
                    }, [
                      createVNode("h3", { class: "text-sm font-semibold text-gray-700 uppercase tracking-wide" }, toDisplayString(group.name), 1),
                      createVNode(unref(ChevronDown), {
                        class: ["w-5 h-5 text-gray-400 transition-transform duration-300 ease-in-out", { "rotate-180": isGroupOpen(index) }]
                      }, null, 8, ["class"])
                    ], 8, ["onClick"]),
                    createVNode(Transition, {
                      "enter-active-class": "transition-all duration-300 ease-in-out",
                      "leave-active-class": "transition-all duration-300 ease-in-out",
                      "enter-from-class": "grid-rows-[0fr] opacity-0",
                      "enter-to-class": "grid-rows-[1fr] opacity-100",
                      "leave-from-class": "grid-rows-[1fr] opacity-100",
                      "leave-to-class": "grid-rows-[0fr] opacity-0"
                    }, {
                      default: withCtx(() => [
                        withDirectives(createVNode("div", { class: "grid overflow-hidden" }, [
                          createVNode("div", { class: "min-h-0 border-t border-gray-200" }, [
                            createVNode("div", { class: "grid sm:grid-cols-2 xl:grid-cols-4" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(group.attributes, (attribute, key) => {
                                return openBlock(), createBlock("div", {
                                  key,
                                  class: "flex flex-col gap-1 px-4 py-3 border-b border-r border-gray-100 last:border-r-0"
                                }, [
                                  createVNode("div", { class: "flex items-center gap-1.5" }, [
                                    createVNode("span", { class: "text-xs text-gray-500" }, toDisplayString(attribute.name), 1),
                                    attribute.helper_text ? (openBlock(), createBlock(unref(Tooltip), { key: 0 }, {
                                      default: withCtx(() => [
                                        createVNode(unref(TooltipTrigger), { asChild: "" }, {
                                          default: withCtx(() => [
                                            createVNode("button", { class: "text-gray-400 hover:text-gray-600 transition-colors" }, [
                                              createVNode(unref(Info), { class: "w-3.5 h-3.5" })
                                            ])
                                          ]),
                                          _: 1
                                        }),
                                        createVNode(unref(TooltipContent), {
                                          side: "top",
                                          class: "max-w-xs bg-gray-900 text-white p-3 rounded-lg"
                                        }, {
                                          default: withCtx(() => [
                                            createVNode("p", { class: "text-xs leading-relaxed" }, toDisplayString(attribute.helper_text), 1)
                                          ]),
                                          _: 2
                                        }, 1024)
                                      ]),
                                      _: 2
                                    }, 1024)) : createCommentVNode("", true)
                                  ]),
                                  createVNode("span", { class: "text-sm font-medium text-gray-900" }, toDisplayString(attribute.value), 1)
                                ]);
                              }), 128))
                            ])
                          ])
                        ], 512), [
                          [vShow, isGroupOpen(index)]
                        ])
                      ]),
                      _: 2
                    }, 1024)
                  ]);
                }), 128))
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(unref(motion).div, {
        initial: { opacity: 0, y: 20 },
        animate: { opacity: 1, y: 0 },
        transition: { duration: 0.5, delay: 0.6 },
        class: "mt-12 bg-white rounded-2xl p-8 sm:p-12 border border-gray-200"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<h2 class="text-2xl font-semibold text-gray-800 mb-6" data-v-6db90438${_scopeId}>О проукте</h2><div class="prose prose-gray max-w-none text-gray-700 space-y-4 leading-relaxed" data-v-6db90438${_scopeId}><p data-v-6db90438${_scopeId}> Современная автомобильная сигнализация с диалоговым кодом обеспечивает надежную защиту вашего автомобиля от угона. Передовые технологии шифрования делают невозможным перехват и клонирование сигнала брелока. </p><p data-v-6db90438${_scopeId}> Система интегрируется с CAN-шиной автомобиля, что позволяет контролировать все важные параметры и блокировать двигатель при попытке угона. Встроенный GSM-модуль обеспечивает связь с владельцем через мобильное приложение. </p><p data-v-6db90438${_scopeId}> Профессиональная установка нашими специалистами гарантирует корректную работу всех функций сигнализации. Мы предоставляем гарантию на оборудование и выполненные работы. </p></div>`);
          } else {
            return [
              createVNode("h2", { class: "text-2xl font-semibold text-gray-800 mb-6" }, "О проукте"),
              createVNode("div", { class: "prose prose-gray max-w-none text-gray-700 space-y-4 leading-relaxed" }, [
                createVNode("p", null, " Современная автомобильная сигнализация с диалоговым кодом обеспечивает надежную защиту вашего автомобиля от угона. Передовые технологии шифрования делают невозможным перехват и клонирование сигнала брелока. "),
                createVNode("p", null, " Система интегрируется с CAN-шиной автомобиля, что позволяет контролировать все важные параметры и блокировать двигатель при попытке угона. Встроенный GSM-модуль обеспечивает связь с владельцем через мобильное приложение. "),
                createVNode("p", null, " Профессиональная установка нашими специалистами гарантирует корректную работу всех функций сигнализации. Мы предоставляем гарантию на оборудование и выполненные работы. ")
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$e, {
        open: unref(isOrderOpen),
        "onUpdate:open": ($event) => isRef(isOrderOpen) ? isOrderOpen.value = $event : null,
        product: unref(selectedProduct2)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$j, {
        open: callbackOpen.value,
        onClose: ($event) => callbackOpen.value = false
      }, null, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Product/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Show = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-6db90438"]]);
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Show
}, Symbol.toStringTag, { value: "Module" }));
createServer(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    resolve: (name) => {
      const pages = /* @__PURE__ */ Object.assign({ "./Pages/Catalog/Index.vue": __vite_glob_0_0, "./Pages/Page/Show.vue": __vite_glob_0_1, "./Pages/Product/Show.vue": __vite_glob_0_2 });
      return pages[`./Pages/${name}.vue`];
    },
    setup({ App, props, plugin }) {
      return createSSRApp({
        render: () => h(App, props)
      }).use(plugin);
    }
  })
);
