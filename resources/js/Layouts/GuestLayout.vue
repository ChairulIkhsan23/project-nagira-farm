<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount } from "vue";

// State untuk menentukan apakah di desktop atau mobile
const isDesktop = ref(false);

// Fungsi untuk cek breakpoint
const checkBreakpoint = () => {
    isDesktop.value = window.innerWidth >= 1024; // lg breakpoint
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener("resize", checkBreakpoint);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", checkBreakpoint);
});
</script>

<template>
    <!-- Gunakan class conditional untuk menghandle fixed/scroll -->
    <div
        :class="[
            isDesktop ? 'fixed inset-0 overflow-hidden' : 'min-h-screen',
            'bg-gradient-to-br from-gray-50 to-gray-100',
        ]"
    >
        <div class="flex" :class="isDesktop ? 'h-screen' : 'min-h-screen'">
            <!-- Left Side - Form Section -->
            <div
                :class="[
                    isDesktop
                        ? 'overflow-y-auto h-full'
                        : 'min-h-screen flex flex-col justify-center',
                    'flex flex-1 flex-col px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24',
                ]"
            >
                <div
                    class="mx-auto w-full max-w-sm lg:w-96"
                    :class="isDesktop ? 'py-8' : ''"
                >
                    <div class="text-center lg:hidden mb-8">
                        <Link href="/">
                            <div class="flex flex-col items-center">
                                <ApplicationLogo
                                    class="h-16 w-16 fill-current text-emerald-600 mb-2"
                                />
                                <span class="text-2xl font-bold text-gray-900"
                                    >Nagira Farm</span
                                >
                                <span class="text-sm text-gray-600"
                                    >Sistem Informasi Peternakan Modern</span
                                >
                            </div>
                        </Link>
                    </div>

                    <div>
                        <div class="hidden lg:block">
                            <Link href="/">
                                <ApplicationLogo
                                    class="h-10 w-10 fill-current text-emerald-600"
                                />
                            </Link>
                        </div>
                        <h2
                            class="mt-8 text-3xl font-bold tracking-tight text-gray-900"
                        >
                            Sign in to your account
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Need access to the admin panel?
                            <a
                                href="#"
                                class="font-medium text-emerald-600 hover:text-emerald-500"
                                >Contact your system administrator</a
                            >
                        </p>
                    </div>

                    <div class="mt-8">
                        <slot />
                    </div>
                </div>
            </div>

            <!-- Right Side - Brand Section -->
            <div class="relative hidden w-0 flex-1 lg:block">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-emerald-800"
                >
                    <!-- Modern angular gradient pattern -->
                    <div class="absolute inset-0">
                        <div
                            class="absolute inset-0 opacity-25"
                            style="
                                background-image: linear-gradient(
                                        90deg,
                                        transparent 95%,
                                        rgba(255, 255, 255, 0.3) 100%
                                    ),
                                    linear-gradient(
                                        0deg,
                                        transparent 95%,
                                        rgba(255, 255, 255, 0.3) 100%
                                    ),
                                    conic-gradient(
                                        from 45deg at 25% 25%,
                                        rgba(99, 102, 241, 0.08) 0deg,
                                        rgba(168, 85, 247, 0.08) 90deg,
                                        rgba(236, 72, 153, 0.08) 180deg,
                                        rgba(14, 165, 233, 0.08) 270deg,
                                        rgba(99, 102, 241, 0.08) 360deg
                                    );
                                background-size: 40px 40px, 40px 40px, 400% 400%;
                                background-position: 0 0, 0 0, 0 0;
                            "
                        ></div>
                    </div>

                    <!-- Content -->
                    <div
                        class="relative flex h-full flex-col items-center justify-center px-12"
                    >
                        <div class="max-w-md text-center">
                            <ApplicationLogo
                                class="h-24 w-24 fill-current text-white mx-auto mb-6"
                            />
                            <h1 class="text-4xl font-bold text-white mb-2">
                                Nagira Farm
                            </h1>
                            <p
                                class="text-2xl font-semibold text-emerald-100 mb-4"
                            >
                                Sistem Informasi Peternakan Modern
                            </p>
                            <p class="text-xl text-emerald-200">
                                "Solusi modern untuk operasional peternakan yang
                                efisien dan berkelanjutan".
                            </p>
                        </div>

                        <!-- SVG Icon Management Section -->
                        <div class="relative mt-12 mb-8">
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div
                                    class="absolute inset-0"
                                    style="
                                        background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%230E9F6E\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
                                    "
                                ></div>
                            </div>

                            <!-- Icon Grid -->
                            <div class="mb-3 relative grid grid-cols-3 gap-6">
                                <!-- Icon 1: Farm Management -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Farm</span
                                    >
                                </div>

                                <!-- Icon 2: Analytics -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Analytics</span
                                    >
                                </div>

                                <!-- Icon 3: Inventory -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Inventory</span
                                    >
                                </div>

                                <!-- Icon 4: Health -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Health</span
                                    >
                                </div>

                                <!-- Icon 5: Schedule -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Schedule</span
                                    >
                                </div>

                                <!-- Icon 6: Reports -->
                                <div
                                    class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:border-emerald-200"
                                >
                                    <div
                                        class="mb-3 p-3 rounded-full bg-emerald-100 group-hover:bg-emerald-200 transition-colors duration-300"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-emerald-700"
                                        >Reports</span
                                    >
                                </div>
                            </div>

                            <!-- Watermark in Green Section -->
                            <div
                                class="mt-5 absolute left-0 right-0 text-center"
                            >
                                <div class="text-sm text-white">
                                    © {{ new Date().getFullYear() }}
                                    <span class="font-bold">Nagira Farm</span> |
                                    All Rights Reserved
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
