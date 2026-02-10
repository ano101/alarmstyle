import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'

export function useRoute() {
    const { props } = usePage()

    return (name, params = {}, absolute = true) =>
        route(name, params, absolute, props.ziggy)
}
