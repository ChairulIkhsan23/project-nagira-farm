<template>
    <div
        class="mx-auto mb-6 w-full max-w-60 rounded-2xl bg-white/80 dark:bg-gray-900/60 backdrop-blur-md border border-gray-200/60 dark:border-white/10 px-5 py-6 shadow-sm hover:shadow-md transition-all duration-300 group"
    >
        <!-- Farm Info Header -->
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3
                    class="text-lg font-bold text-gray-900 dark:text-white mb-0.5"
                >
                    Nagira Farm
                </h3>
                <p
                    class="text-xs text-gray-500 dark:text-gray-400 font-medium tracking-wide uppercase"
                >
                    Farm Management
                </p>
            </div>
            <div
                class="p-2.5 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-500/15 dark:to-emerald-500/15 ring-1 ring-green-100 dark:ring-green-500/20 group-hover:scale-105 transition-transform duration-200"
            >
                <svg
                    class="w-5 h-5 text-green-600 dark:text-green-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
            </div>
        </div>

        <!-- Status & Stats -->
        <div class="space-y-4">
            <!-- Status Badge -->
            <div
                class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-500/10 dark:to-emerald-500/10 border border-green-100 dark:border-green-500/20"
            >
                <div class="flex items-center gap-2.5">
                    <div class="relative">
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-green-500 animate-ping"
                        ></div>
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-green-500 absolute top-0 left-0"
                        ></div>
                    </div>
                    <span
                        class="text-sm font-semibold text-green-700 dark:text-green-400"
                    >
                        System Online
                    </span>
                </div>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full bg-white dark:bg-gray-800 text-green-700 dark:text-green-400"
                >
                    v1.0.0
                </span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    © 2025 Nagira Farm
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional custom animations */
@keyframes subtle-float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-2px);
    }
}

.group:hover {
    animation: subtle-float 3s ease-in-out infinite;
}
</style>
