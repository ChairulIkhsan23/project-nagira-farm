<!-- resources/js/Layouts/Admin/AppHeader.vue -->
<template>
    <header
        class="sticky top-0 z-[9999] w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800"
    >
        <div class="flex items-center justify-between px-4 py-3 lg:px-6">
            <!-- MOBILE HAMBURGER -->
            <button
                @click="handleToggle"
                class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 lg:hidden"
                aria-label="Toggle sidebar"
            >
                <!-- CLOSE -->
                <svg
                    v-if="isMobileOpen"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>

                <!-- HAMBURGER -->
                <svg
                    v-else
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" d="M3 6h18M3 12h14M3 18h18" />
                </svg>
            </button>

            <SearchBar />

            <div class="flex items-center gap-3">
                <NotificationMenu />
                <UserMenu />
            </div>
        </div>
    </header>
</template>

<script setup>
import { useSidebar } from "@/composables/useSidebar";

import SearchBar from "./Header/SearchBar.vue";
import NotificationMenu from "./Header/NotificationMenu.vue";
import UserMenu from "./Header/UserMenu.vue";

const { toggleMobileSidebar, isMobileOpen } = useSidebar();

const handleToggle = () => {
    toggleMobileSidebar();
};
</script>
