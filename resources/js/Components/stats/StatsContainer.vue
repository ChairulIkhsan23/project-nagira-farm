<!-- resources/js/Components/stats/StatsContainer.vue -->
<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import StatsCard from "./StatsCard.vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";

const props = defineProps({
    stats: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Statistik Ternak",
    },
    subtitle: {
        type: String,
        default: "Ringkasan data secara keseluruhan",
    },
    showPagination: {
        type: Boolean,
        default: false,
    },
});

// Tambahkan ref untuk window width
const windowWidth = ref(
    typeof window !== "undefined" ? window.innerWidth : 1024
);
const currentStatsPage = ref(0);

const statsPerPage = computed(() => {
    return windowWidth.value < 768 ? 1 : props.stats.length;
});

const paginatedStats = computed(() => {
    const start = currentStatsPage.value * statsPerPage.value;
    const end = start + statsPerPage.value;
    return props.stats.slice(start, end);
});

const totalStatsPages = computed(() =>
    Math.ceil(props.stats.length / statsPerPage.value)
);

const showStatsPagination = computed(() => {
    return (
        props.showPagination &&
        windowWidth.value < 768 &&
        props.stats.length > 1
    );
});

const nextStatsPage = () => {
    if (currentStatsPage.value < totalStatsPages.value - 1) {
        currentStatsPage.value++;
    }
};

const prevStatsPage = () => {
    if (currentStatsPage.value > 0) {
        currentStatsPage.value--;
    }
};

const goToStatsPage = (page) => {
    currentStatsPage.value = page;
};

const handleResize = () => {
    windowWidth.value = window.innerWidth;
    if (windowWidth.value >= 768) {
        currentStatsPage.value = 0;
    }
};

onMounted(() => {
    if (typeof window !== "undefined") {
        windowWidth.value = window.innerWidth;
        window.addEventListener("resize", handleResize);
    }
});

onUnmounted(() => {
    if (typeof window !== "undefined") {
        window.removeEventListener("resize", handleResize);
    }
});
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <div class="p-4 md:p-6">
            <!-- Stats Header -->
            <div class="mb-4 md:mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3
                            class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ title }}
                        </h3>
                        <p
                            v-if="subtitle"
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            {{ subtitle }}
                        </p>
                    </div>

                    <!-- Loading Indicator -->
                    <div v-if="loading" class="flex items-center gap-2">
                        <div
                            class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"
                        ></div>
                        <span class="text-xs text-blue-500">Memuat...</span>
                    </div>
                </div>
            </div>

            <!-- Desktop: Grid 5 kolom (semua statistik tampil) -->
            <div class="hidden md:grid md:grid-cols-5 gap-4">
                <StatsCard
                    v-for="stat in stats"
                    :key="stat.title"
                    :title="stat.title"
                    :value="stat.value"
                    :description="stat.description"
                    :icon="stat.icon"
                    :icon-class="
                        stat.iconClass || 'text-gray-600 dark:text-gray-300'
                    "
                    :icon-bg-class="
                        stat.iconBgClass || 'bg-gray-100 dark:bg-gray-700'
                    "
                    :loading="loading"
                />
            </div>

            <!-- Mobile Stats (1 card per halaman) -->
            <div class="md:hidden">
                <StatsCard
                    v-for="stat in paginatedStats"
                    :key="stat.title"
                    :title="stat.title"
                    :value="stat.value"
                    :description="stat.description"
                    :icon="stat.icon"
                    :icon-class="
                        stat.iconClass || 'text-gray-600 dark:text-gray-300'
                    "
                    :icon-bg-class="
                        stat.iconBgClass || 'bg-gray-100 dark:bg-gray-700'
                    "
                    :loading="loading"
                    :compact="false"
                />
            </div>

            <!-- Mobile Stats Pagination -->
            <div
                v-if="showStatsPagination"
                class="md:hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-center gap-3">
                    <button
                        @click="prevStatsPage"
                        :disabled="currentStatsPage === 0"
                        class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <ChevronLeft
                            class="w-4 h-4 text-gray-600 dark:text-gray-400"
                        />
                    </button>

                    <div class="flex items-center gap-1">
                        <span
                            v-for="page in totalStatsPages"
                            :key="page"
                            @click="goToStatsPage(page - 1)"
                            class="w-2 h-2 rounded-full cursor-pointer transition-all duration-200"
                            :class="
                                currentStatsPage === page - 1
                                    ? 'bg-blue-500 w-4'
                                    : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500'
                            "
                        />
                    </div>

                    <button
                        @click="nextStatsPage"
                        :disabled="currentStatsPage === totalStatsPages - 1"
                        class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <ChevronRight
                            class="w-4 h-4 text-gray-600 dark:text-gray-400"
                        />
                    </button>
                </div>
                <p
                    class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2"
                >
                    Halaman {{ currentStatsPage + 1 }} dari
                    {{ totalStatsPages }}
                </p>
            </div>
        </div>
    </div>
</template>
