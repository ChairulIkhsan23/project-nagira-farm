<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import TernakDetailModal from "@/Components/ui/TernakDetailModal.vue";
import ExportButton from "@/Components/ui/ExportButton.vue";
import { useToast } from "vue-toastification";
import axios from "axios";

// Icons
import {
    Search,
    Filter,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    X,
    FileX,
    Database,
    Egg,
    Beef,
    HeartPulse,
    XCircle,
    CheckCircle,
    AlertCircle,
    HelpCircle,
} from "lucide-vue-next";

/* State dan Refs */
const selectedTernak = ref(null);
const searchQuery = ref("");
const sortOption = ref("name_asc");
const showFilterPanel = ref(false);
const filterPanelRef = ref(null);
const currentPage = ref(1);
const itemsPerPage = 10;
const showMobileFilters = ref(false);
const currentStatsPage = ref(0);

// API State
const apiData = ref({
    ternaks: [],
    meta: {},
    stats: [],
});
const isLoading = ref(false);

// Filter states - mapping ke backend
const filters = ref({
    kategori: "", // breeding, fattening
    kesehatan: "", // sehat, perawatan, sakit
    status: "", // aktif, nonaktif
});

/* Helper Functions */
const getKategoriDisplay = (kategori) => {
    if (!kategori || kategori === "null") return "Reguler";
    if (kategori === "breeding") return "Breeding";
    if (kategori === "fattening") return "Fattening";
    return kategori;
};

const getKategoriClass = (kategori) => {
    if (!kategori || kategori === "null")
        return "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300";
    if (kategori === "breeding")
        return "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300";
    if (kategori === "fattening")
        return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300";
    return "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300";
};

/* Methods untuk API */
const fetchTernakFromAPI = async () => {
    isLoading.value = true;
    try {
        // Siapkan parameter untuk API sesuai dengan backend
        const params = {
            search: searchQuery.value,
            kategori:
                filters.value.kategori === "null"
                    ? "unknown"
                    : filters.value.kategori,
            kesehatan: filters.value.kesehatan,
            status: filters.value.status,
            sort_by: sortOption.value,
            per_page: itemsPerPage,
            page: currentPage.value,
        };

        // Kirim request ke API
        const response = await axios.get("/api/ternak", { params });

        if (response.data.success) {
            apiData.value.ternaks = response.data.data;
            apiData.value.meta = response.data.meta;
        } else {
            toast.error(response.data.message || "Gagal mengambil data");
        }
    } catch (error) {
        console.error("Error fetching from API:", error);
        toast.error("Gagal mengambil data dari API");

        // Fallback: kosongkan data jika error
        apiData.value.ternaks = [];
        apiData.value.meta = {};
    } finally {
        isLoading.value = false;
    }
};

const fetchStatsFromAPI = async () => {
    try {
        const response = await axios.get("/api/ternak/stats/summary");
        if (response.data.success) {
            apiData.value.stats = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching stats:", error);
        // Fallback stats kosong
        apiData.value.stats = [];
    }
};

// Load data pertama kali dan saat filter berubah
const loadData = () => {
    fetchTernakFromAPI();
    fetchStatsFromAPI();
};

/* Properti Komputasi */
// Stats data dari API
const statsData = computed(() => {
    if (apiData.value.stats.length > 0) {
        return [
            {
                title: "Total Ternak",
                value: apiData.value.stats[0]?.value || 0,
                icon: Database,
                description:
                    apiData.value.stats[0]?.description ||
                    "Semua ternak terdaftar",
            },
            {
                title: "Breeding",
                value: apiData.value.stats[1]?.value || 0,
                icon: Egg,
                description:
                    apiData.value.stats[1]?.description ||
                    "Untuk pengembangbiakan",
            },
            {
                title: "Fattening",
                value: apiData.value.stats[2]?.value || 0,
                icon: Beef,
                description:
                    apiData.value.stats[2]?.description || "Untuk penggemukan",
            },
            {
                title: "Sakit",
                value: apiData.value.stats[3]?.value || 0,
                icon: HeartPulse,
                description:
                    apiData.value.stats[3]?.description ||
                    "Membutuhkan penanganan",
            },
            {
                title: "Nonaktif",
                value: apiData.value.stats[4]?.value || 0,
                icon: XCircle,
                description:
                    apiData.value.stats[4]?.description || "Ternak tidak aktif",
            },
        ];
    }

    // Fallback jika stats belum di-load
    return [
        {
            title: "Total Ternak",
            value: 0,
            icon: Database,
            description: "Memuat data...",
        },
        {
            title: "Breeding",
            value: 0,
            icon: Egg,
            description: "Memuat data...",
        },
        {
            title: "Fattening",
            value: 0,
            icon: Beef,
            description: "Memuat data...",
        },
        {
            title: "Sakit",
            value: 0,
            icon: HeartPulse,
            description: "Memuat data...",
        },
        {
            title: "Nonaktif",
            value: 0,
            icon: XCircle,
            description: "Memuat data...",
        },
    ];
});

// Filters untuk export button (mapping ke backend)
const exportFilters = computed(() => {
    return {
        kategori:
            filters.value.kategori === "null" ? "" : filters.value.kategori,
        kesehatan: filters.value.kesehatan,
        status: filters.value.status,
        search: searchQuery.value,
    };
});

// Pagination data langsung dari API
const paginatedTernaks = computed(() => {
    return apiData.value.ternaks || [];
});

const totalPages = computed(() => {
    return apiData.value.meta?.last_page || 1;
});

const pageNumbers = computed(() => {
    const pages = [];
    const maxVisiblePages = window.innerWidth < 640 ? 2 : 3;

    let startPage = Math.max(
        1,
        currentPage.value - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    return pages;
});

// Active filter count
const activeFilterCount = computed(() => {
    return Object.values(filters.value).filter(Boolean).length;
});

// Stats untuk mobile pagination
const statsPerPage = computed(() => {
    return window.innerWidth < 768 ? 1 : statsData.value.length;
});

const paginatedStats = computed(() => {
    const start = currentStatsPage.value * statsPerPage.value;
    const end = start + statsPerPage.value;
    return statsData.value.slice(start, end);
});

const totalStatsPages = computed(() =>
    Math.ceil(statsData.value.length / statsPerPage.value)
);

const showStatsPagination = computed(() => {
    return window.innerWidth < 768 && statsData.value.length > 1;
});

/* Methods utama */
const openDetail = (ternak) => {
    selectedTernak.value = ternak;
};

const toast = useToast();

// Export handlers - menggunakan ExportButton component yang sudah diperbarui
const handleExportSuccess = (data) => {
    toast.success(`Export ${data.format.toUpperCase()} berhasil!`);
};

const handleExportError = (data) => {
    toast.error(`Export ${data.format} gagal: ${data.error}`);
};

const resetFilters = () => {
    filters.value = {
        kategori: "",
        kesehatan: "",
        status: "",
    };
    searchQuery.value = "";
    currentPage.value = 1;
    showMobileFilters.value = false;
    showFilterPanel.value = false;

    loadData();
};

const applyFilters = () => {
    currentPage.value = 1;
    showFilterPanel.value = false;
    showMobileFilters.value = false;

    loadData();
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchTernakFromAPI();
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        fetchTernakFromAPI();
    }
};

const goToPage = (page) => {
    currentPage.value = page;
    fetchTernakFromAPI();
};

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

const handleClickOutside = (event) => {
    if (filterPanelRef.value && !filterPanelRef.value.contains(event.target)) {
        showFilterPanel.value = false;
    }
};

/* Watchers untuk real-time filtering */
let searchTimeout = null;

watch(
    [searchQuery, sortOption, filters],
    () => {
        // Debounce untuk search
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage.value = 1;
            fetchTernakFromAPI();
        }, 300);
    },
    { deep: true }
);

watch(currentPage, () => {
    fetchTernakFromAPI();
});

/* Lifecycle Hooks */
onMounted(() => {
    document.addEventListener("click", handleClickOutside);

    // Handle window resize untuk reset stats page
    const handleResize = () => {
        if (window.innerWidth >= 768) {
            currentStatsPage.value = 0;
        }
    };

    window.addEventListener("resize", handleResize);
    onUnmounted(() => {
        window.removeEventListener("resize", handleResize);
    });

    // Load data pertama kali
    loadData();
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
    if (searchTimeout) clearTimeout(searchTimeout);
});
</script>

<template>
    <Head title="Data Ternak" />

    <AdminLayout>
        <div
            class="-mt-2 md:-mt-6 space-y-3 sm:space-y-4 md:space-y-6 transition-all duration-300 ease-in-out"
        >
            <!-- Mobile Header -->
            <div class="md:hidden">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Data Ternak
                    </h1>
                    <!-- Hanya filter button yang tersisa -->
                    <div class="flex items-center gap-2">
                        <!-- Export Button Mobile -->
                        <ExportButton
                            type="both"
                            :filters="exportFilters"
                            :filename="`data-ternak-${
                                new Date().toISOString().split('T')[0]
                            }`"
                            size="sm"
                            variant="primary"
                            label="Export"
                            @success="handleExportSuccess"
                            @error="handleExportError"
                            class="w-full"
                        />

                        <!-- Mobile Filter Button -->
                        <button
                            @click="showMobileFilters = !showMobileFilters"
                            class="relative p-2 rounded-lg border border-gray-300 dark:border-gray-700"
                        >
                            <Filter class="w-5 h-5" />
                            <span
                                v-if="activeFilterCount > 0"
                                class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"
                            >
                                {{ activeFilterCount }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Mobile Search -->
                <div class="relative mb-4">
                    <Search
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama atau kode ternak..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none transition-all duration-200"
                    />

                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Mobile Filter Panel -->
                <div
                    v-if="showMobileFilters"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 mb-4"
                >
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3
                                class="font-semibold text-gray-900 dark:text-white"
                            >
                                Filter Data
                            </h3>
                            <button
                                v-if="activeFilterCount > 0"
                                @click="resetFilters"
                                class="text-sm text-blue-500 hover:text-blue-600"
                            >
                                Reset
                            </button>
                        </div>

                        <!-- Kategori Filter -->
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Kategori
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    @click="
                                        filters.kategori =
                                            filters.kategori === 'breeding'
                                                ? ''
                                                : 'breeding'
                                    "
                                    class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                    :class="
                                        filters.kategori === 'breeding'
                                            ? 'bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    Breeding
                                </button>
                                <button
                                    @click="
                                        filters.kategori =
                                            filters.kategori === 'fattening'
                                                ? ''
                                                : 'fattening'
                                    "
                                    class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                    :class="
                                        filters.kategori === 'fattening'
                                            ? 'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    Fattening
                                </button>
                                <button
                                    @click="
                                        filters.kategori =
                                            filters.kategori === 'null'
                                                ? ''
                                                : 'null'
                                    "
                                    class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                    :class="
                                        filters.kategori === 'null'
                                            ? 'bg-gray-200 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    Reguler
                                </button>
                            </div>
                        </div>

                        <!-- Kesehatan Filter -->
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Status Kesehatan
                            </label>
                            <div class="space-y-2">
                                <button
                                    @click="
                                        filters.kesehatan =
                                            filters.kesehatan === 'sehat'
                                                ? ''
                                                : 'sehat'
                                    "
                                    class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                    :class="
                                        filters.kesehatan === 'sehat'
                                            ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    <CheckCircle class="w-4 h-4" />
                                    <span>Sehat</span>
                                </button>
                                <button
                                    @click="
                                        filters.kesehatan =
                                            filters.kesehatan === 'perawatan'
                                                ? ''
                                                : 'perawatan'
                                    "
                                    class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                    :class="
                                        filters.kesehatan === 'perawatan'
                                            ? 'bg-yellow-100 text-yellow-700 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    <AlertCircle class="w-4 h-4" />
                                    <span>Perawatan</span>
                                </button>
                                <button
                                    @click="
                                        filters.kesehatan =
                                            filters.kesehatan === 'sakit'
                                                ? ''
                                                : 'sakit'
                                    "
                                    class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                    :class="
                                        filters.kesehatan === 'sakit'
                                            ? 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    <XCircle class="w-4 h-4" />
                                    <span>Sakit</span>
                                </button>
                            </div>
                        </div>

                        <!-- Status Aktif Filter -->
                        <div class="mb-6">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Status Aktif
                            </label>
                            <div class="flex gap-2">
                                <button
                                    @click="
                                        filters.status =
                                            filters.status === 'aktif'
                                                ? ''
                                                : 'aktif'
                                    "
                                    class="flex-1 px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                    :class="
                                        filters.status === 'aktif'
                                            ? 'bg-green-100 text-green-700 border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    Aktif
                                </button>
                                <button
                                    @click="
                                        filters.status =
                                            filters.status === 'nonaktif'
                                                ? ''
                                                : 'nonaktif'
                                    "
                                    class="flex-1 px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                    :class="
                                        filters.status === 'nonaktif'
                                            ? 'bg-gray-200 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                                            : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    "
                                >
                                    Nonaktif
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button
                                @click="applyFilters"
                                class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium"
                            >
                                Terapkan
                            </button>
                            <button
                                @click="showMobileFilters = false"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Header -->
            <div
                class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Data Ternak
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        Kelola data ternak dengan mudah
                    </p>
                </div>

                <!-- Toolbar -->
                <div
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3"
                >
                    <!-- Search -->
                    <div class="relative flex-1 max-w-md">
                        <Search
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama atau kode ternak..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none transition-all duration-200"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <select
                            v-model="sortOption"
                            class="appearance-none pl-4 pr-10 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-all duration-200"
                        >
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
                            <option value="health_asc">
                                Kesehatan (Sehat → Sakit)
                            </option>
                            <option value="health_desc">
                                Kesehatan (Sakit → Sehat)
                            </option>
                        </select>
                        <ChevronDown
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        />
                    </div>

                    <!-- Export Button Desktop -->
                    <ExportButton
                        type="both"
                        :filters="exportFilters"
                        :filename="`data-ternak-${
                            new Date().toISOString().split('T')[0]
                        }`"
                        size="md"
                        variant="primary"
                        label="Export Data"
                        @success="handleExportSuccess"
                        @error="handleExportError"
                    />

                    <!-- Desktop Filter Button -->
                    <div class="relative hidden md:block" ref="filterPanelRef">
                        <button
                            @click="showFilterPanel = !showFilterPanel"
                            class="relative px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 flex items-center gap-2"
                        >
                            <Filter class="w-5 h-5" />
                            <span>Filter</span>
                            <span
                                v-if="activeFilterCount > 0"
                                class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                            >
                                {{ activeFilterCount }}
                            </span>
                        </button>

                        <!-- Desktop Filter Panel -->
                        <div
                            v-if="showFilterPanel"
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50"
                        >
                            <div class="p-4">
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        Filter Data
                                    </h3>
                                    <button
                                        v-if="activeFilterCount > 0"
                                        @click="resetFilters"
                                        class="text-sm text-blue-500 hover:text-blue-600"
                                    >
                                        Reset
                                    </button>
                                </div>

                                <!-- Kategori Filter -->
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Kategori
                                    </label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button
                                            @click="
                                                filters.kategori =
                                                    filters.kategori ===
                                                    'breeding'
                                                        ? ''
                                                        : 'breeding'
                                            "
                                            class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                            :class="
                                                filters.kategori === 'breeding'
                                                    ? 'bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            Breeding
                                        </button>
                                        <button
                                            @click="
                                                filters.kategori =
                                                    filters.kategori ===
                                                    'fattening'
                                                        ? ''
                                                        : 'fattening'
                                            "
                                            class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                            :class="
                                                filters.kategori === 'fattening'
                                                    ? 'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            Fattening
                                        </button>
                                        <button
                                            @click="
                                                filters.kategori =
                                                    filters.kategori === 'null'
                                                        ? ''
                                                        : 'null'
                                            "
                                            class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                            :class="
                                                filters.kategori === 'null'
                                                    ? 'bg-gray-200 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            Reguler
                                        </button>
                                    </div>
                                </div>

                                <!-- Kesehatan Filter -->
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Status Kesehatan
                                    </label>
                                    <div class="space-y-2">
                                        <button
                                            @click="
                                                filters.kesehatan =
                                                    filters.kesehatan ===
                                                    'sehat'
                                                        ? ''
                                                        : 'sehat'
                                            "
                                            class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                            :class="
                                                filters.kesehatan === 'sehat'
                                                    ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            <CheckCircle class="w-4 h-4" />
                                            <span>Sehat</span>
                                        </button>
                                        <button
                                            @click="
                                                filters.kesehatan =
                                                    filters.kesehatan ===
                                                    'perawatan'
                                                        ? ''
                                                        : 'perawatan'
                                            "
                                            class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                            :class="
                                                filters.kesehatan ===
                                                'perawatan'
                                                    ? 'bg-yellow-100 text-yellow-700 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            <AlertCircle class="w-4 h-4" />
                                            <span>Perawatan</span>
                                        </button>
                                        <button
                                            @click="
                                                filters.kesehatan =
                                                    filters.kesehatan ===
                                                    'sakit'
                                                        ? ''
                                                        : 'sakit'
                                            "
                                            class="w-full px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center gap-2"
                                            :class="
                                                filters.kesehatan === 'sakit'
                                                    ? 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            <XCircle class="w-4 h-4" />
                                            <span>Sakit</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Status Aktif Filter -->
                                <div class="mb-6">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Status Aktif
                                    </label>
                                    <div class="flex gap-2">
                                        <button
                                            @click="
                                                filters.status =
                                                    filters.status === 'aktif'
                                                        ? ''
                                                        : 'aktif'
                                            "
                                            class="flex-1 px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                            :class="
                                                filters.status === 'aktif'
                                                    ? 'bg-green-100 text-green-700 border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            Aktif
                                        </button>
                                        <button
                                            @click="
                                                filters.status =
                                                    filters.status ===
                                                    'nonaktif'
                                                        ? ''
                                                        : 'nonaktif'
                                            "
                                            class="flex-1 px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                                            :class="
                                                filters.status === 'nonaktif'
                                                    ? 'bg-gray-200 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                                            "
                                        >
                                            Nonaktif
                                        </button>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <button
                                        @click="applyFilters"
                                        class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium"
                                    >
                                        Terapkan
                                    </button>
                                    <button
                                        @click="showFilterPanel = false"
                                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Container -->
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
                                    Statistik Ternak
                                </h3>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    Ringkasan data ternak secara keseluruhan
                                </p>
                            </div>

                            <!-- Loading Indicator -->
                            <div
                                v-if="isLoading"
                                class="flex items-center gap-2"
                            >
                                <div
                                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"
                                ></div>
                                <span class="text-xs text-blue-500"
                                    >Memuat...</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Desktop: Grid 5 kolom (semua statistik tampil) -->
                    <div class="hidden md:grid md:grid-cols-5 gap-4">
                        <div
                            v-for="stat in statsData"
                            :key="stat.title"
                            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        {{ stat.title }}
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ stat.value }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                                    >
                                        {{ stat.description }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex-shrink-0"
                                >
                                    <component
                                        :is="stat.icon"
                                        class="w-5 h-5 text-gray-600 dark:text-gray-300"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Stats (1 card per halaman) -->
                    <div class="md:hidden">
                        <div
                            v-for="stat in paginatedStats"
                            :key="stat.title"
                            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        {{ stat.title }}
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ stat.value }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                                    >
                                        {{ stat.description }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex-shrink-0"
                                >
                                    <component
                                        :is="stat.icon"
                                        class="w-5 h-5 text-gray-600 dark:text-gray-300"
                                    />
                                </div>
                            </div>
                        </div>
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
                                :disabled="
                                    currentStatsPage === totalStatsPages - 1
                                "
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

            <!-- Konten Utama Tabel -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg md:rounded-2xl shadow md:shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Table Header -->
                <div
                    class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h2
                                class="text-base md:text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Daftar Ternak
                            </h2>
                            <p
                                class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                <span v-if="apiData.meta">
                                    Halaman
                                    {{ apiData.meta.current_page || 1 }} dari
                                    {{ apiData.meta.last_page || 1 }}
                                    (Total: {{ apiData.meta.total || 0 }} data)
                                </span>
                                <span
                                    v-if="searchQuery || activeFilterCount > 0"
                                    class="text-blue-500 ml-1"
                                >
                                    (difilter)
                                </span>
                            </p>
                        </div>

                        <div v-if="isLoading" class="flex items-center gap-2">
                            <div
                                class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"
                            ></div>
                            <span class="text-xs text-blue-500"
                                >Memuat data...</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-y-auto overflow-x-hidden">
                    <table class="w-full table-fixed text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th
                                    class="text-left py-3 px-3 sm:px-4 md:py-4 md:px-6 text-xs md:text-sm font-semibold text-gray-900 dark:text-white min-w-[160px] sm:min-w-[200px]"
                                >
                                    Ternak
                                </th>
                                <th
                                    class="hidden md:table-cell text-left py-3 px-4 md:py-4 md:px-6 text-xs md:text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Jenis
                                </th>
                                <th
                                    class="text-left py-3 pl-8 sm:pl-4 md:py-4 md:px-6 text-xs md:text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Kategori
                                </th>
                                <th
                                    class="hidden md:table-cell text-left py-3 px-4 md:py-4 md:px-6 text-xs md:text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Kesehatan
                                </th>
                                <th
                                    class="hidden sm:table-cell text-left py-3 px-4 md:py-4 md:px-10 text-xs md:text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Status
                                </th>
                                <th
                                    class="py-3 md:py-4 px-3 md:px-4 text-xs md:text-sm font-semibold text-gray-900 dark:text-white text-center"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Data Rows -->
                            <tr
                                v-for="ternak in paginatedTernaks"
                                :key="ternak.id"
                                class="group hover:bg-gray-50 dark:hover:bg-gray-900/50"
                            >
                                <!-- Ternak -->
                                <td
                                    class="py-3 px-3 sm:px-4 md:py-4 md:px-6 min-w-[160px] sm:min-w-[200px]"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img
                                                :src="
                                                    ternak.foto ??
                                                    '/images/default-ternak.jpg'
                                                "
                                                :alt="ternak.nama_ternak"
                                                class="w-10 h-10 md:w-12 md:h-12 rounded-lg md:rounded-xl object-cover border border-white dark:border-gray-800 shadow-sm"
                                            />
                                            <!-- Indikator status kesehatan (hanya tampil jika ternak aktif) -->
                                            <div
                                                v-if="
                                                    ternak.status_aktif ===
                                                    'aktif'
                                                "
                                            >
                                                <div
                                                    v-if="
                                                        ternak.status_kesehatan ===
                                                        'sehat'
                                                    "
                                                    class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border border-white dark:border-gray-800"
                                                ></div>
                                                <div
                                                    v-else-if="
                                                        ternak.status_kesehatan ===
                                                        'perawatan'
                                                    "
                                                    class="absolute -bottom-1 -right-1 w-3 h-3 bg-yellow-500 rounded-full border border-white dark:border-gray-800"
                                                ></div>
                                                <div
                                                    v-else-if="
                                                        ternak.status_kesehatan ===
                                                        'sakit'
                                                    "
                                                    class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full border border-white dark:border-gray-800"
                                                ></div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="font-medium text-sm md:text-base text-gray-900 dark:text-white truncate"
                                            >
                                                {{ ternak.nama_ternak }}
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                            >
                                                {{ ternak.kode_ternak }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jenis -->
                                <td
                                    class="hidden md:table-cell py-3 px-4 md:py-4 md:px-6"
                                >
                                    <span
                                        class="text-sm text-gray-900 dark:text-white truncate block max-w-[120px]"
                                    >
                                        {{ ternak.jenis_ternak }}
                                    </span>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3 pl-8 sm:pl-4 md:py-4 md:px-6">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="
                                            getKategoriClass(ternak.kategori)
                                        "
                                    >
                                        <HelpCircle
                                            v-if="
                                                !ternak.kategori ||
                                                ternak.kategori === 'null'
                                            "
                                            class="w-3 h-3 mr-1"
                                        />
                                        {{
                                            getKategoriDisplay(ternak.kategori)
                                        }}
                                    </span>
                                </td>

                                <!-- Kesehatan -->
                                <td
                                    class="hidden md:table-cell py-3 px-4 md:py-4 md:px-6"
                                >
                                    <span
                                        v-if="ternak.status_aktif === 'aktif'"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300':
                                                ternak.status_kesehatan ===
                                                'sehat',
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300':
                                                ternak.status_kesehatan ===
                                                'perawatan',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300':
                                                ternak.status_kesehatan ===
                                                'sakit',
                                        }"
                                    >
                                        <div
                                            v-if="
                                                ternak.status_aktif === 'aktif'
                                            "
                                            class="w-1.5 h-1.5 rounded-full animate-pulse"
                                            :class="{
                                                'bg-emerald-500':
                                                    ternak.status_kesehatan ===
                                                    'sehat',
                                                'bg-yellow-500':
                                                    ternak.status_kesehatan ===
                                                    'perawatan',
                                                'bg-red-500':
                                                    ternak.status_kesehatan ===
                                                    'sakit',
                                            }"
                                        ></div>
                                        {{ ternak.status_kesehatan }}
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                                    >
                                        -
                                    </span>
                                </td>

                                <!-- Status -->
                                <td
                                    class="hidden sm:table-cell py-3 px-4 md:py-4 md:px-6"
                                >
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
                                        :class="
                                            ternak.status_aktif === 'aktif'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                                : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'
                                        "
                                    >
                                        <div
                                            class="w-1.5 h-1.5 rounded-full"
                                            :class="
                                                ternak.status_aktif === 'aktif'
                                                    ? 'bg-green-500'
                                                    : 'bg-gray-400'
                                            "
                                        ></div>
                                        {{ ternak.status_aktif }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td
                                    class="py-3 px-3 md:py-4 md:px-4 text-center"
                                >
                                    <button
                                        @click="openDetail(ternak)"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs md:text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                    >
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="paginatedTernaks.length === 0">
                                <td colspan="6" class="py-8 px-4 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <FileX
                                            class="w-12 h-12 md:w-16 md:h-16 text-gray-300 dark:text-gray-600 mb-3"
                                        />
                                        <h3
                                            class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-1 md:mb-2"
                                        >
                                            {{
                                                isLoading
                                                    ? "Memuat data..."
                                                    : "Data tidak ditemukan"
                                            }}
                                        </h3>
                                        <p
                                            class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-4 md:mb-6 max-w-xs"
                                        >
                                            {{
                                                searchQuery ||
                                                activeFilterCount > 0
                                                    ? "Coba ubah kata kunci pencarian atau filter yang diterapkan"
                                                    : "Belum ada data ternak yang tersedia"
                                            }}
                                        </p>
                                        <div class="flex gap-2">
                                            <button
                                                v-if="
                                                    searchQuery ||
                                                    activeFilterCount > 0
                                                "
                                                @click="resetFilters"
                                                class="px-3 py-1.5 text-xs md:text-sm font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30"
                                            >
                                                Reset Filter
                                            </button>
                                            <button
                                                @click="loadData"
                                                class="px-3 py-1.5 text-xs md:text-sm font-medium text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30"
                                            >
                                                Muat Ulang
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="totalPages > 1"
                    class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <div
                        class="flex flex-col sm:flex-row items-center justify-between gap-3"
                    >
                        <div
                            class="text-xs md:text-sm text-gray-500 dark:text-gray-400"
                        >
                            <span v-if="apiData.meta">
                                Halaman
                                {{ apiData.meta.current_page || 1 }} dari
                                {{ apiData.meta.last_page || 1 }} (Total:
                                {{ apiData.meta.total || 0 }} data)
                            </span>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Previous Button -->
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1 || isLoading"
                                class="p-1.5 md:p-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                <ChevronLeft
                                    class="w-3.5 h-3.5 md:w-4 md:h-4"
                                />
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex items-center gap-1 mx-1 md:mx-2">
                                <button
                                    v-for="page in pageNumbers"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :disabled="isLoading"
                                    class="w-8 h-8 md:w-10 md:h-10 rounded-lg text-xs md:text-sm font-medium transition-all"
                                    :class="
                                        page === currentPage
                                            ? 'bg-blue-500 text-white'
                                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                    "
                                >
                                    {{ page }}
                                </button>

                                <!-- Ellipsis for more pages -->
                                <span
                                    v-if="
                                        totalPages > 3 &&
                                        pageNumbers[pageNumbers.length - 1] <
                                            totalPages
                                    "
                                    class="px-1 md:px-2 text-xs md:text-sm text-gray-500 dark:text-gray-400"
                                >
                                    ...
                                </span>
                            </div>

                            <!-- Next Button -->
                            <button
                                @click="nextPage"
                                :disabled="
                                    currentPage === totalPages || isLoading
                                "
                                class="p-1.5 md:p-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                <ChevronRight
                                    class="w-3.5 h-3.5 md:w-4 md:h-4"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <TernakDetailModal
            v-if="selectedTernak"
            :ternak="selectedTernak"
            @close="selectedTernak = null"
        />
    </AdminLayout>
</template>

<style scoped>
/* Mobile optimizations */
@media (max-width: 640px) {
    .table-header {
        font-size: 0.75rem;
    }

    .table-cell {
        padding: 0.5rem 0.25rem;
    }
}

/* Smooth transitions */
* {
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

/* Loading animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
