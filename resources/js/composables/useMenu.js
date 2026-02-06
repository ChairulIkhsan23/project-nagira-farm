import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

export function useMenu() {
    const page = usePage();
    const activePath = page.url;
    const openSubmenu = ref(null);

    const toggle = (key) => {
        openSubmenu.value = openSubmenu.value === key ? null : key;
    };

    const isActive = (path, exact = false) => {
        if (exact) {
            return activePath === path || activePath === path + "/";
        }
        return activePath.startsWith(path);
    };

    const shouldAutoOpen = (children, groupIndex, itemIndex) => {
        if (!children) return false;
        return children.some((c) => isActive(c.path));
    };

    return {
        openSubmenu,
        toggle,
        isActive,
        shouldAutoOpen,
    };
}
