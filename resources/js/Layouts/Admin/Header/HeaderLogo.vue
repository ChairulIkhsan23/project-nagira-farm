<template>
    <Link href="/admin/dashboard" class="lg:hidden">
        <!-- Logo untuk Nagira Farm -->
        <div class="flex items-center gap-2">
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center"
            >
                <svg
                    class="w-5 h-5 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z"
                    />
                    <path d="M12 18l-3-2-2 4 5-2 5 2-2-4-3 2z" opacity="0.7" />
                </svg>
            </div>
            <span class="text-lg font-bold text-gray-900 dark:text-white"
                >Nagira</span
            >
        </div>
    </Link>
</template>

<script setup>
import { Link } from "@inertiajs/vue3"; // ✅ Ganti RouterLink dengan Link
</script>
