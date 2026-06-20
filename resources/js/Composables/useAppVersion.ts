import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type SharedPageProps = {
    app?: {
        version?: string;
    };
};

export function useAppVersion() {
    const page = usePage<SharedPageProps>();

    return computed(() => page.props.app?.version ?? '0.1.0');
}
