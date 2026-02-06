<!-- components/ui/ExportButton.vue -->
<script setup>
import { ref, computed, onUnmounted } from "vue";
import { Download, FileText, Table } from "lucide-vue-next";

const props = defineProps({
    // Tipe export: 'pdf', 'excel', atau 'both'
    type: {
        type: String,
        default: "both",
        validator: (value) => ["pdf", "excel", "both"].includes(value),
    },
    // Filters untuk data
    filters: {
        type: Object,
        default: () => ({
            kategori: "", // breeding, fattening
            kesehatan: "", // sehat, perawatan, sakit
            status: "", // aktif, nonaktif
            search: "",
        }),
    },
    // API endpoint
    endpoint: {
        type: String,
        default: "/api/exports/ternak",
    },
    // Method request
    method: {
        type: String,
        default: "GET",
        validator: (value) => ["GET", "POST"].includes(value),
    },
    // Nama file
    filename: {
        type: String,
        default: "",
    },
    // Ukuran button
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
    // Variant button
    variant: {
        type: String,
        default: "primary",
        validator: (value) =>
            ["primary", "secondary", "outline"].includes(value),
    },
    // Label button
    label: {
        type: String,
        default: null,
    },
    // Loading state
    loading: {
        type: Boolean,
        default: false,
    },
    // Disabled state
    disabled: {
        type: Boolean,
        default: false,
    },
    // Custom class
    customClass: {
        type: String,
        default: "",
    },
    // Show loading indicator
    showLoading: {
        type: Boolean,
        default: true,
    },
    // Debug mode (untuk development)
    debug: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["pdf", "excel", "click", "success", "error"]);

// State
const isLoading = ref(false);
const showDropdown = ref(false);

// Constants
const ENVIRONMENT = import.meta.env.MODE || "production";
const IS_DEVELOPMENT = ENVIRONMENT === "development";

/**
 * Map filter keys untuk sesuai dengan API backend
 * Frontend keys -> Backend keys
 */
const mapFiltersToApi = (filters) => {
    const mapped = {};

    if (filters?.kategori) {
        mapped.kategori = filters.kategori;
    }

    if (filters?.kesehatan) {
        mapped.status_kesehatan = filters.kesehatan;
    }

    if (filters?.status) {
        mapped.status_aktif = filters.status;
    }

    if (filters?.search) {
        mapped.search = filters.search;
    }

    return mapped;
};

/**
 * Build URL untuk export dengan filter
 */
const buildExportUrl = (format) => {
    const baseUrl = `${props.endpoint}/${format}`;
    const mappedFilters = mapFiltersToApi(props.filters);

    // Jika tidak ada filter, return base URL saja
    if (Object.keys(mappedFilters).length === 0) {
        return baseUrl;
    }

    const params = new URLSearchParams();

    // Tambahkan semua filter yang ada
    Object.entries(mappedFilters).forEach(([key, value]) => {
        if (value && value !== "") {
            params.append(key, value);
        }
    });

    // Tambahkan timestamp untuk menghindari cache
    params.append("_t", Date.now());

    return `${baseUrl}?${params.toString()}`;
};

/**
 * Handle export dengan method GET
 */
const handleExportGet = async (format) => {
    try {
        const exportUrl = buildExportUrl(format);

        // Debug log untuk development
        if (IS_DEVELOPMENT || props.debug) {
            console.log(`Export ${format.toUpperCase()} URL:`, exportUrl);
        }

        // Buka URL di tab baru untuk download
        window.location.href = exportUrl;

        // Emit success event
        emit("success", {
            format,
            filters: props.filters,
            url: exportUrl,
        });
    } catch (error) {
        console.error(`Export ${format.toUpperCase()} Error:`, error);

        // Emit error event
        emit("error", {
            format,
            error: error.message,
            filters: props.filters,
        });

        // Fallback: show error message
        alert(
            `Terjadi kesalahan saat export ${format.toUpperCase()}. Silakan coba lagi.`
        );
    }
};

/**
 * Handle export dengan method POST
 */
const handleExportPost = async (format) => {
    try {
        const url = `${props.endpoint}/${format}`;
        const mappedFilters = mapFiltersToApi(props.filters);

        const form = document.createElement("form");
        form.method = "POST";
        form.action = url;
        form.target = "_blank";

        // Tambahkan CSRF token jika tersedia
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;
        if (csrfToken) {
            const csrfInput = document.createElement("input");
            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Tambahkan filter parameters
        Object.entries(mappedFilters).forEach(([key, value]) => {
            if (value && value !== "") {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
        });

        // Submit form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        // Emit success event
        emit("success", {
            format,
            filters: props.filters,
            url,
        });
    } catch (error) {
        console.error(`Export ${format.toUpperCase()} Error:`, error);

        // Emit error event
        emit("error", {
            format,
            error: error.message,
            filters: props.filters,
        });

        alert(
            `Terjadi kesalahan saat export ${format.toUpperCase()}. Silakan coba lagi.`
        );
    }
};

/**
 * Handle PDF export
 */
const handlePDF = async () => {
    if (isLoading.value || props.disabled) return;

    isLoading.value = true;
    showDropdown.value = false;
    emit("click", "pdf");

    try {
        if (props.method === "POST") {
            await handleExportPost("pdf");
        } else {
            await handleExportGet("pdf");
        }

        emit("pdf", {
            success: true,
            format: "pdf",
            filters: props.filters,
        });
    } finally {
        if (props.showLoading) {
            isLoading.value = false;
        }
    }
};

/**
 * Handle Excel export
 */
const handleExcel = async () => {
    if (isLoading.value || props.disabled) return;

    isLoading.value = true;
    showDropdown.value = false;
    emit("click", "excel");

    try {
        if (props.method === "POST") {
            await handleExportPost("excel");
        } else {
            await handleExportGet("excel");
        }

        emit("excel", {
            success: true,
            format: "excel",
            filters: props.filters,
        });
    } finally {
        if (props.showLoading) {
            isLoading.value = false;
        }
    }
};

/**
 * Toggle dropdown menu
 */
const toggleDropdown = () => {
    if (props.type === "both") {
        showDropdown.value = !showDropdown.value;
    }
};

/**
 * Close dropdown ketika klik di luar component
 */
const closeDropdown = (event) => {
    if (!event.target.closest(".export-button-container")) {
        showDropdown.value = false;
    }
};

// Add click outside listener
if (typeof window !== "undefined") {
    document.addEventListener("click", closeDropdown);
}

// Class helpers
const getSizeClass = () => {
    switch (props.size) {
        case "sm":
            return "px-3 py-1.5 text-xs";
        case "lg":
            return "px-6 py-3 text-base";
        default: // md
            return "px-4 py-2.5 text-sm";
    }
};

const getVariantClass = () => {
    const base =
        "inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed";

    switch (props.variant) {
        case "secondary":
            return `${base} bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600`;
        case "outline":
            return `${base} border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700`;
        default: // primary
            return `${base} bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700`;
    }
};

const getButtonLabel = () => {
    if (props.label) return props.label;

    switch (props.type) {
        case "pdf":
            return "PDF";
        case "excel":
            return "Excel";
        case "both":
            return "Export";
        default:
            return "Export";
    }
};

const getButtonIcon = () => {
    switch (props.type) {
        case "pdf":
            return FileText;
        case "excel":
            return Table;
        case "both":
            return Download;
        default:
            return Download;
    }
};

// Computed properties
const shouldShowDropdown = computed(() => {
    return props.type === "both" && showDropdown.value;
});

const isProcessing = computed(() => {
    return props.loading || isLoading.value;
});

const downloadFilename = computed(() => {
    if (props.filename) return props.filename;

    const date = new Date().toISOString().split("T")[0];
    const filters = mapFiltersToApi(props.filters);
    const filterStr = Object.keys(filters).length > 0 ? "-filtered" : "";

    return `data-ternak-${date}${filterStr}`;
});

const hasActiveFilters = computed(() => {
    const mapped = mapFiltersToApi(props.filters);
    return Object.values(mapped).some((value) => value && value !== "");
});

// Cleanup event listener on component unmount
onUnmounted(() => {
    if (typeof window !== "undefined") {
        document.removeEventListener("click", closeDropdown);
    }
});
</script>

<template>
    <div class="relative export-button-container">
        <!-- Single Button (untuk PDF atau Excel saja) -->
        <button
            v-if="type !== 'both'"
            :class="[getSizeClass(), getVariantClass(), customClass]"
            @click="type === 'pdf' ? handlePDF() : handleExcel()"
            :disabled="disabled || isProcessing"
            :title="type === 'pdf' ? 'Export ke PDF' : 'Export ke Excel'"
        >
            <component
                :is="getButtonIcon()"
                class="w-4 h-4"
                :class="{ 'animate-spin': isProcessing }"
            />
            <span>{{ getButtonLabel() }}</span>
            <span v-if="isProcessing && showLoading" class="ml-1 text-xs">
                ...
            </span>
        </button>

        <!-- Dropdown Button (untuk type 'both') -->
        <div v-else class="relative">
            <!-- Main Button -->
            <button
                :class="[
                    getSizeClass(),
                    getVariantClass(),
                    customClass,
                    'min-w-[100px]',
                ]"
                @click="toggleDropdown"
                :disabled="disabled || isProcessing"
                :title="
                    hasActiveFilters
                        ? 'Export dengan filter yang diterapkan'
                        : 'Export semua data'
                "
            >
                <Download
                    class="w-4 h-4"
                    :class="{ 'animate-spin': isProcessing }"
                />
                <span>{{ getButtonLabel() }}</span>
                <svg
                    class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': showDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
                v-if="shouldShowDropdown"
                class="absolute top-full left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
            >
                <!-- PDF Export Option -->
                <button
                    @click="handlePDF"
                    class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-3 text-sm transition-colors duration-150"
                    :disabled="isProcessing"
                >
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <FileText
                            class="w-4 h-4 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="flex-1 text-left">
                        <div class="font-medium text-gray-900 dark:text-white">
                            Export ke PDF
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                        >
                            Format dokumen untuk cetak
                        </div>
                    </div>
                    <svg
                        v-if="isProcessing"
                        class="w-4 h-4 text-blue-500 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        />
                    </svg>
                </button>

                <!-- Excel Export Option -->
                <button
                    @click="handleExcel"
                    class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-3 text-sm transition-colors duration-150 border-t border-gray-100 dark:border-gray-700"
                    :disabled="isProcessing"
                >
                    <div
                        class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg"
                    >
                        <Table
                            class="w-4 h-4 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <div class="flex-1 text-left">
                        <div class="font-medium text-gray-900 dark:text-white">
                            Export ke Excel
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                        >
                            Format spreadsheet untuk analisis
                        </div>
                    </div>
                    <svg
                        v-if="isProcessing"
                        class="w-4 h-4 text-blue-500 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        />
                    </svg>
                </button>

                <!-- Filter Info Section -->
                <div
                    v-if="hasActiveFilters"
                    class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50"
                >
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <div class="font-medium mb-1">
                            Filter yang diterapkan:
                        </div>
                        <div class="space-y-0.5">
                            <div v-if="filters?.kategori" class="truncate">
                                Kategori: {{ filters.kategori }}
                            </div>
                            <div v-if="filters?.kesehatan" class="truncate">
                                Kesehatan: {{ filters.kesehatan }}
                            </div>
                            <div v-if="filters?.status" class="truncate">
                                Status: {{ filters.status }}
                            </div>
                            <div v-if="filters?.search" class="truncate">
                                Pencarian: "{{ filters.search }}"
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Info (hanya untuk development/debug mode) -->
                <div
                    v-if="(IS_DEVELOPMENT || debug) && hasActiveFilters"
                    class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20 text-xs text-blue-600 dark:text-blue-400"
                >
                    <div class="font-medium mb-1">Debug Info:</div>
                    <div class="font-mono text-xs truncate">
                        Endpoint: {{ endpoint }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Smooth dropdown animation */
.export-button-container .absolute {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
