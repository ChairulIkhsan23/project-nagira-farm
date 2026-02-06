import { ref, onMounted, onUnmounted, provide, inject } from "vue";

const SidebarSymbol = Symbol();

export function useSidebarProvider() {
    const isExpanded = ref(true); // desktop selalu true
    const isMobileOpen = ref(false);
    const isMobile = ref(false);

    const handleResize = () => {
        isMobile.value = window.innerWidth < 1024;
        if (!isMobile.value) {
            isMobileOpen.value = false;
            isExpanded.value = true;
        }
    };

    onMounted(() => {
        handleResize();
        window.addEventListener("resize", handleResize);
    });

    onUnmounted(() => {
        window.removeEventListener("resize", handleResize);
    });

    const toggleMobileSidebar = () => {
        isMobileOpen.value = !isMobileOpen.value;
    };

    provide(SidebarSymbol, {
        isExpanded,
        isMobileOpen,
        isMobile,
        toggleMobileSidebar,
    });

    return {
        isExpanded,
        isMobileOpen,
        isMobile,
        toggleMobileSidebar,
    };
}

export function useSidebar() {
    const context = inject(SidebarSymbol);
    if (!context) {
        throw new Error("useSidebar must be used inside SidebarProvider");
    }
    return context;
}
