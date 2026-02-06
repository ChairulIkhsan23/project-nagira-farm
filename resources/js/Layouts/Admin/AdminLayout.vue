<template>
    <div class="min-h-screen flex bg-gray-50 dark:bg-gray-900">
        <SidebarProvider>
            <AppSidebar />
            <Backdrop />
            <div
                class="flex-1 transition-all duration-300"
                :class="isMobile ? 'ml-0' : 'lg:ml-[280px]'"
            >
                <AppHeader />

                <main class="p-4 mx-auto max-w-7xl md:p-6">
                    <slot />
                </main>
            </div>
        </SidebarProvider>
    </div>
</template>

<script setup>
import { useSidebar } from "@/composables/useSidebar";
import AppSidebar from "@/Layouts/Admin/AppSidebar.vue";
import AppHeader from "@/Layouts/Admin/AppHeader.vue";
import Backdrop from "@/Layouts/Admin/Backdrop.vue";
import SidebarProvider from "@/Layouts/Admin/SidebarProvider.vue";

const { isMobile } = useSidebar();
</script>
