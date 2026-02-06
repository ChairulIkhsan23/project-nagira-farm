<template>
    <div class="relative" ref="dropdownRef">
        <button
            class="flex items-center text-gray-700 dark:text-gray-400"
            @click.prevent="toggleDropdown"
        >
            <div class="mr-3 overflow-hidden rounded-full h-11 w-11">
                <img
                    src="/images/user/profile.jpg"
                    alt="User"
                    class="w-full h-full object-cover"
                />
            </div>

            <span class="block mr-1 font-medium text-theme-sm">
                {{ auth.user.nama ?? "User" }}
            </span>

            <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" />
        </button>

        <!-- DROPDOWN -->
        <div
            v-if="dropdownOpen"
            class="absolute right-0 z-50 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
        >
            <!-- User Info -->
            <div>
                <span
                    class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400"
                >
                    {{ auth.user.name }}
                </span>
                <span
                    class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400"
                >
                    {{ auth.user.email }}
                </span>
            </div>

            <!-- MENU LIST -->
            <ul
                class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800"
            >
                <li v-for="item in menuItems" :key="item.text">
                    <Link
                        :href="item.href"
                        class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5"
                        @click="closeDropdown"
                    >
                        <component
                            :is="item.icon"
                            class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
                        />
                        {{ item.text }}
                    </Link>
                </li>
            </ul>

            <!-- LOGOUT -->
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="flex items-center gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5"
                @click="closeDropdown"
            >
                <LogoutIcon
                    class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
                />
                Sign out
            </Link>
        </div>
    </div>
</template>

<script setup>
import {
    ChevronDownIcon,
    LogoutIcon,
    UserCircleIcon,
    SettingsIcon,
    InfoCircleIcon,
} from "@/Components/icons";

import { Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";

// Ambil user dari Inertia
const { props } = usePage();
const auth = props.auth;

// Dropdown state
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

// Menu Items - GUNAKAN PATH LANGSUNG untuk route yang belum didefinisikan
const menuItems = [
    {
        href: route("admin.dashboard", {}, false) || "/admin/dashboard",
        icon: UserCircleIcon,
        text: "Dashboard",
    },
    {
        // Ganti dengan path langsung karena route admin.profil.index belum didefinisikan
        href: "/admin/profile",
        icon: SettingsIcon,
        text: "Account settings",
    },
    {
        // Ganti dengan path langsung atau route yang valid
        href: "/admin/support",
        icon: InfoCircleIcon,
        text: "Support",
    },
];

// Dropdown handler
const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value);
const closeDropdown = () => (dropdownOpen.value = false);

// Klik di luar → close dropdown
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        closeDropdown();
    }
};

onMounted(() => document.addEventListener("click", handleClickOutside));
onUnmounted(() => document.removeEventListener("click", handleClickOutside));
</script>
