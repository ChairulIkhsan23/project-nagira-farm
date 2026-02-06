<script setup>
import { Link } from "@inertiajs/vue3";
import { useSidebar } from "@/composables/useSidebar";
import { useMenu } from "@/composables/useMenu";
import { menuGroups } from "@/config/menu.config";
import SidebarWidget from "@/Layouts/Admin/SidebarWidget.vue";

const { isMobileOpen } = useSidebar();
const { openSubmenu, toggle, isActive, shouldAutoOpen } = useMenu();
</script>

<template>
    <aside
        :class="[
            'fixed left-0 z-[9998] w-[280px]',
            'bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 flex flex-col',
            'top-[64px] h-[calc(100vh-64px)]',
            'lg:top-0 lg:h-screen',
            'transform-gpu transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] will-change-transform',
            'shadow-lg lg:shadow-none',
            isMobileOpen
                ? 'translate-x-0'
                : '-translate-x-[280px] lg:translate-x-0',
        ]"
    >
        <!-- Mobile Logo -->
        <div class="py-6 px-5 flex lg:hidden items-center">
            <Link href="/admin/dashboard">
                <img
                    src="/images/logo/logo.svg"
                    alt="Logo"
                    class="dark:hidden max-w-[160px]"
                />
            </Link>
        </div>

        <!-- Desktop Logo -->
        <div class="py-8 px-5 hidden lg:flex items-center">
            <Link href="/admin/dashboard">
                <img
                    src="/images/logo/logo.svg"
                    alt="Logo"
                    class="dark:hidden max-w-[180px]"
                />
            </Link>
        </div>

        <!-- Menu Utama -->
        <nav class="flex-1 overflow-y-auto no-scrollbar px-3 py-2">
            <ul class="flex flex-col gap-1">
                <template v-for="(group, g) in menuGroups" :key="g">
                    <li v-for="(item, i) in group.items" :key="i">
                        <!-- Menu With Sub -->
                        <template v-if="item.children">
                            <button
                                @click="toggle(`${g}-${i}`)"
                                class="w-full flex items-center gap-3 py-3 px-3 rounded-xl transition-all duration-200 group"
                                :class="
                                    openSubmenu === `${g}-${i}` ||
                                    isActive(item.children[0]?.path)
                                        ? 'bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-700 dark:text-emerald-300 shadow-sm ring-1 ring-emerald-100 dark:ring-emerald-800/30'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-100'
                                "
                            >
                                <span
                                    class="w-6 h-6 flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
                                    :class="{
                                        'text-emerald-600 dark:text-emerald-400':
                                            openSubmenu === `${g}-${i}` ||
                                            isActive(item.children[0]?.path),
                                    }"
                                >
                                    <component :is="item.icon" />
                                </span>

                                <span
                                    class="font-medium text-sm truncate transition-all duration-200"
                                >
                                    {{ item.name }}
                                </span>

                                <svg
                                    class="w-4 h-4 ml-auto transition-all duration-300"
                                    :class="{
                                        'rotate-180 text-emerald-600 dark:text-emerald-400':
                                            openSubmenu === `${g}-${i}`,
                                        'text-gray-400 dark:text-gray-500':
                                            !openSubmenu,
                                    }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                    />
                                </svg>
                            </button>

                            <!-- Sub Menu -->
                            <transition name="submenu-slide">
                                <ul
                                    v-show="
                                        openSubmenu === `${g}-${i}` ||
                                        shouldAutoOpen(item.children, g, i)
                                    "
                                    class="ml-10 mt-2 space-y-1"
                                >
                                    <li
                                        v-for="c in item.children"
                                        :key="c.name"
                                    >
                                        <Link
                                            :href="c.path"
                                            class="flex items-center gap-3 py-2.5 px-3 rounded-lg transition-all duration-200 group"
                                            :class="
                                                isActive(c.path)
                                                    ? 'bg-gradient-to-r from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 text-emerald-700 dark:text-emerald-300 shadow-sm ring-1 ring-emerald-100 dark:ring-emerald-800/30'
                                                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800/50'
                                            "
                                        >
                                            <div
                                                class="flex items-center justify-center w-1.5 h-1.5"
                                            >
                                                <div
                                                    v-if="isActive(c.path)"
                                                    class="w-1.5 h-1.5 bg-emerald-500 rounded-full"
                                                ></div>
                                                <div
                                                    v-else
                                                    class="w-1 h-1 bg-gray-400 dark:bg-gray-600 rounded-full opacity-50 group-hover:opacity-100 transition-opacity"
                                                ></div>
                                            </div>

                                            <span
                                                class="text-sm font-medium truncate transition-all duration-200"
                                            >
                                                {{ c.name }}
                                            </span>

                                            <!-- Checkmark untuk submenu aktif -->
                                            <svg
                                                v-if="isActive(c.path)"
                                                class="w-3.5 h-3.5 ml-auto text-emerald-500 dark:text-emerald-400 animate-bounce"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5"
                                                />
                                            </svg>
                                        </Link>
                                    </li>
                                </ul>
                            </transition>
                        </template>

                        <!-- Single Menu -->
                        <template v-else>
                            <Link
                                :href="item.path"
                                class="w-full flex items-center gap-3 py-3 px-3 rounded-xl transition-all duration-200 group"
                                :class="
                                    isActive(item.path)
                                        ? 'bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-700 dark:text-emerald-300 shadow-sm ring-1 ring-emerald-100 dark:ring-emerald-800/30 relative before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:w-1 before:h-8 before:bg-gradient-to-b before:from-emerald-500 before:to-teal-500 before:rounded-r-full'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-100'
                                "
                            >
                                <span
                                    class="w-6 h-6 flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
                                    :class="{
                                        'text-emerald-600 dark:text-emerald-400':
                                            isActive(item.path),
                                    }"
                                >
                                    <component :is="item.icon" />
                                </span>

                                <span
                                    class="font-medium text-sm truncate transition-all duration-200"
                                >
                                    {{ item.name }}
                                </span>

                                <!-- Hover Arrow -->
                                <svg
                                    class="w-3.5 h-3.5 ml-auto opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-200 text-gray-400 dark:text-gray-500"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                                    />
                                </svg>
                            </Link>
                        </template>
                    </li>
                </template>
            </ul>
        </nav>

        <SidebarWidget class="px-3 pb-4" />
    </aside>
</template>

<style scoped>
/* Custom Scrollbar */
.no-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.no-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.no-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.no-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.dark .no-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.dark .no-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Submenu Animations */
.submenu-slide-enter-active,
.submenu-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.submenu-slide-enter-from,
.submenu-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
    max-height: 0;
}

.submenu-slide-enter-to,
.submenu-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
    max-height: 500px;
}

/* Smooth hover transitions */
* {
    transition-property: color, background-color, border-color,
        text-decoration-color, fill, stroke, opacity, box-shadow, transform,
        filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Gradient border effect for active items */
.menu-item-active::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 0.75rem;
    padding: 1px;
    background: linear-gradient(135deg, #10b981 0%, #0d9488 50%, #059669 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
}
</style>
