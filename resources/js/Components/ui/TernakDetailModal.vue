<script setup>
import {
    X,
    Calendar,
    User,
    Tag,
    Clock,
    FileText,
    Share2,
    Printer,
    Edit,
    CheckCircle,
    XCircle,
    AlertCircle,
    MapPin,
    Hash,
    Clipboard,
    Activity,
    Shield,
    Heart,
    Baby,
    Weight,
    Target,
    TrendingUp,
} from "lucide-vue-next";
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    ternak: {
        type: Object,
        required: true,
        default: () => ({
            nama_ternak: "",
            kode_ternak: "",
            jenis_ternak: "",
            kategori: "",
            jenis_kelamin: "",
            status_kesehatan: "",
            status_aktif: "",
            foto: "",
            tanggal_lahir: "",
            lokasi: "",
            catatan: "",
            created_at: "",
            updated_at: "",
            breeding: null,
            fattening: null,
        }),
    },
});

defineEmits(["close", "edit", "print", "share"]);

// State untuk loading
const isLoading = ref(true);

// State untuk tooltip
const tooltip = ref({
    show: false,
    text: "",
    x: 0,
    y: 0,
    maxWidth: 300,
});

// Fungsi untuk menampilkan tooltip
const showTooltip = (event, text) => {
    if (!text || text === "-") return;

    const element = event.target;
    // Cek apakah teks terpotong
    if (element.scrollWidth > element.clientWidth) {
        tooltip.value = {
            show: true,
            text: text,
            x: event.clientX + 10,
            y: event.clientY + 10,
            maxWidth: Math.min(300, window.innerWidth - event.clientX - 20),
        };
    }
};

// Fungsi untuk menyembunyikan tooltip
const hideTooltip = () => {
    tooltip.value.show = false;
};

// Simulasi loading data
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 500);
});

// Helper function untuk kategori
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
        return "bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300";
    if (kategori === "fattening")
        return "bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300";
    return "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300";
};

// Computed properties untuk styling
const healthStatusColor = computed(() => {
    if (props.ternak.status_aktif === "nonaktif") {
        return "text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800";
    }

    switch (props.ternak.status_kesehatan) {
        case "sehat":
            return "text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20";
        case "perawatan":
            return "text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20";
        case "sakit":
            return "text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20";
        default:
            return "text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800";
    }
});

const activeStatusColor = computed(() => {
    return props.ternak.status_aktif === "aktif"
        ? "text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20"
        : "text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800";
});

const genderColor = computed(() => {
    return props.ternak.jenis_kelamin === "jantan"
        ? "text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20"
        : "text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-900/20";
});

const kategoriBadgeClass = computed(() => {
    return getKategoriClass(props.ternak.kategori);
});

// Computed untuk mengecek kategori
const isBreeding = computed(() => {
    return props.ternak.kategori === "breeding";
});

const isFattening = computed(() => {
    return props.ternak.kategori === "fattening";
});

const isReguler = computed(() => {
    return !props.ternak.kategori || props.ternak.kategori === "null";
});

// Helper functions untuk breeding
const getBreedingStatusLabel = (status) => {
    return (
        {
            kosong: "Kosong",
            kawin: "Sedang Kawin",
            bunting: "Bunting",
            nifas: "Nifas",
        }[status] || status
    );
};

const getBreedingColor = (status) => {
    return (
        {
            kosong: "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300",
            kawin: "bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300",
            bunting:
                "bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300",
            nifas: "bg-pink-100 text-pink-700 dark:bg-pink-900/20 dark:text-pink-300",
        }[status] || "bg-gray-100 text-gray-700"
    );
};

// Helper functions untuk fattening
const getFatteningProgress = (fattening) => {
    if (!fattening || !fattening.berat_awal || !fattening.target_berat) {
        return 0;
    }

    // Asumsikan ada berat saat ini, jika tidak pakai berat awal
    const beratSaatIni = fattening.berat_saat_ini || fattening.berat_awal;
    const progress =
        ((beratSaatIni - fattening.berat_awal) /
            (fattening.target_berat - fattening.berat_awal)) *
        100;
    return Math.min(100, Math.max(0, Math.round(progress)));
};

// Helper functions
const calculateAge = (birthDate) => {
    if (!birthDate) return null;

    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (
        monthDiff < 0 ||
        (monthDiff === 0 && today.getDate() < birth.getDate())
    ) {
        age--;
    }

    if (age === 0) {
        const monthAge = Math.max(0, monthDiff);
        return `${monthAge} bulan`;
    }

    return `${age} tahun`;
};

const formatDate = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};

const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return null;
    const date = new Date(dateTimeString);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Helper untuk menampilkan teks kesehatan
const healthStatusText = computed(() => {
    if (props.ternak.status_aktif === "nonaktif") {
        return "-";
    }
    return props.ternak.status_kesehatan || "Tidak diketahui";
});
</script>

<template>
    <!-- Modal Container -->
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-black/50 transition-opacity duration-300"
            @click="$emit('close')"
        />

        <!-- Tooltip -->
        <div
            v-if="tooltip.show"
            class="fixed z-[9999] pointer-events-none"
            :style="{
                left: tooltip.x + 'px',
                top: tooltip.y + 'px',
                maxWidth: tooltip.maxWidth + 'px',
            }"
        >
            <div class="relative">
                <!-- Tooltip Arrow -->
                <div class="absolute -top-2 left-3">
                    <div
                        class="w-3 h-3 bg-white dark:bg-gray-800 rotate-45 transform border-l border-t border-gray-200 dark:border-gray-700"
                    ></div>
                </div>
                <!-- Tooltip Content -->
                <div
                    class="relative bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 whitespace-normal break-words"
                >
                    {{ tooltip.text }}
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            class="fixed inset-0 z-50 flex min-h-full items-center justify-center p-4 pt-20 lg:pl-64 lg:pt-16"
        >
            <div class="relative w-full max-w-4xl">
                <!-- Modal Card -->
                <div
                    class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] lg:max-h-[80vh]"
                >
                    <!-- Header -->
                    <div class="border-b border-gray-200 dark:border-gray-800">
                        <div class="px-4 py-3 lg:px-6 lg:py-4">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <h2
                                        class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white truncate"
                                    >
                                        Detail Ternak
                                    </h2>
                                    <p
                                        class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mt-1 truncate"
                                    >
                                        <span v-if="isBreeding">
                                            Informasi Breeding</span
                                        >
                                        <span v-else-if="isFattening">
                                            Informasi Fattening</span
                                        >
                                        <span v-else> Informasi Reguler</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-1 lg:gap-2">
                                    <button
                                        @click="$emit('share')"
                                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        title="Bagikan"
                                    >
                                        <Share2 class="w-4 h-4 lg:w-5 lg:h-5" />
                                    </button>
                                    <button
                                        @click="$emit('print')"
                                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        title="Cetak"
                                    >
                                        <Printer
                                            class="w-4 h-4 lg:w-5 lg:h-5"
                                        />
                                    </button>
                                    <button
                                        @click="$emit('close')"
                                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                    >
                                        <X class="w-4 h-4 lg:w-5 lg:h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div
                        class="overflow-y-auto max-h-[calc(90vh-140px)] lg:max-h-[calc(80vh-140px)]"
                    >
                        <!-- Loading State -->
                        <div v-if="isLoading" class="p-8 text-center">
                            <div
                                class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"
                            ></div>
                            <p class="mt-2 text-sm text-gray-500">
                                Memuat data...
                            </p>
                        </div>

                        <!-- Content -->
                        <div v-else class="p-4 lg:p-6">
                            <!-- Profile Section -->
                            <div
                                class="flex flex-col lg:flex-row gap-6 lg:gap-8"
                            >
                                <!-- Left Column -->
                                <div class="lg:w-2/5 space-y-4 lg:space-y-6">
                                    <!-- Photo Card -->
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 lg:p-4"
                                    >
                                        <div class="relative">
                                            <img
                                                :src="
                                                    ternak.foto ||
                                                    '/images/default-ternak.jpg'
                                                "
                                                :alt="ternak.nama_ternak"
                                                class="w-full h-48 object-contain rounded-lg"
                                            />
                                            <!-- Kategori Badge di foto -->
                                            <div
                                                class="absolute top-3 left-3 lg:top-4 lg:left-4"
                                            >
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
                                                    :class="kategoriBadgeClass"
                                                >
                                                    <Tag class="w-3 h-3" />
                                                    {{
                                                        getKategoriDisplay(
                                                            ternak.kategori
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <div
                                                class="absolute bottom-3 right-3 lg:bottom-4 lg:right-4"
                                            >
                                                <span
                                                    class="inline-flex items-center gap-1 lg:gap-2 px-2 py-1 lg:px-3 lg:py-1.5 rounded-full text-xs font-medium"
                                                    :class="activeStatusColor"
                                                >
                                                    <CheckCircle
                                                        v-if="
                                                            ternak.status_aktif ===
                                                            'aktif'
                                                        "
                                                        class="w-3 h-3"
                                                    />
                                                    <XCircle
                                                        v-else
                                                        class="w-3 h-3"
                                                    />
                                                    <span
                                                        class="hidden sm:inline"
                                                    >
                                                        {{
                                                            ternak.status_aktif
                                                        }}
                                                    </span>
                                                    <span class="sm:hidden">
                                                        {{
                                                            ternak.status_aktif ===
                                                            "aktif"
                                                                ? "Aktif"
                                                                : "Nonaktif"
                                                        }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Name & ID -->
                                        <div class="mt-3 lg:mt-4">
                                            <h1
                                                class="text-lg lg:text-xl font-bold text-gray-900 dark:text-white mb-1 truncate"
                                                @mouseenter="
                                                    ($event) =>
                                                        showTooltip(
                                                            $event,
                                                            ternak.nama_ternak ||
                                                                'Tanpa Nama'
                                                        )
                                                "
                                                @mouseleave="hideTooltip"
                                                @mousemove="
                                                    ($event) =>
                                                        showTooltip(
                                                            $event,
                                                            ternak.nama_ternak ||
                                                                'Tanpa Nama'
                                                        )
                                                "
                                            >
                                                {{
                                                    ternak.nama_ternak ||
                                                    "Tanpa Nama"
                                                }}
                                            </h1>
                                            <div
                                                class="flex items-center gap-2 text-xs lg:text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                <Hash
                                                    class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0"
                                                />
                                                <span
                                                    class="font-mono truncate"
                                                    @mouseenter="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                ternak.kode_ternak
                                                            )
                                                    "
                                                    @mouseleave="hideTooltip"
                                                    @mousemove="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                ternak.kode_ternak
                                                            )
                                                    "
                                                >
                                                    {{
                                                        ternak.kode_ternak ||
                                                        "-"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Info Grid -->
                                    <div
                                        class="grid grid-cols-2 gap-2 lg:gap-3"
                                    >
                                        <!-- Age Card -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 lg:p-4"
                                        >
                                            <div
                                                class="flex items-center gap-2 lg:gap-3"
                                            >
                                                <div
                                                    class="p-1.5 lg:p-2 bg-white dark:bg-gray-800 rounded-lg flex-shrink-0"
                                                >
                                                    <Clock
                                                        class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600 dark:text-gray-400"
                                                    />
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                                    >
                                                        Umur
                                                    </p>
                                                    <p
                                                        class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    calculateAge(
                                                                        ternak.tanggal_lahir
                                                                    ) || '-'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    calculateAge(
                                                                        ternak.tanggal_lahir
                                                                    ) || '-'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            calculateAge(
                                                                ternak.tanggal_lahir
                                                            ) || "-"
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Birth Date Card -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 lg:p-4"
                                        >
                                            <div
                                                class="flex items-center gap-2 lg:gap-3"
                                            >
                                                <div
                                                    class="p-1.5 lg:p-2 bg-white dark:bg-gray-800 rounded-lg flex-shrink-0"
                                                >
                                                    <Calendar
                                                        class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600 dark:text-gray-400"
                                                    />
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                                    >
                                                        Tanggal Lahir
                                                    </p>
                                                    <p
                                                        class="text-sm lg:text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak.tanggal_lahir
                                                                    ) || '-'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak.tanggal_lahir
                                                                    ) || '-'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                ternak.tanggal_lahir
                                                            ) || "-"
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="lg:w-3/5 space-y-4 lg:space-y-6">
                                    <!-- Status Badges -->
                                    <div
                                        class="flex flex-col sm:flex-row gap-3"
                                    >
                                        <!-- Health Status -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 lg:p-4 flex-1"
                                        >
                                            <div
                                                class="flex items-center gap-2 lg:gap-3"
                                            >
                                                <div
                                                    class="p-1.5 lg:p-2 bg-white dark:bg-gray-800 rounded-lg flex-shrink-0"
                                                >
                                                    <Shield
                                                        class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600 dark:text-gray-400"
                                                    />
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        Status Kesehatan
                                                    </p>
                                                    <span
                                                        class="inline-flex items-center gap-1 lg:gap-2 px-2 py-1 lg:px-3 lg:py-1.5 rounded-full text-xs lg:text-sm font-medium mt-1 truncate"
                                                        :class="
                                                            healthStatusColor
                                                        "
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    healthStatusText
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    healthStatusText
                                                                )
                                                        "
                                                    >
                                                        <Activity
                                                            v-if="
                                                                ternak.status_aktif ===
                                                                'aktif'
                                                            "
                                                            class="w-3 h-3"
                                                        />
                                                        <span class="truncate">
                                                            {{
                                                                healthStatusText
                                                            }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 lg:p-4 flex-1"
                                        >
                                            <div
                                                class="flex items-center gap-2 lg:gap-3"
                                            >
                                                <div
                                                    class="p-1.5 lg:p-2 bg-white dark:bg-gray-800 rounded-lg flex-shrink-0"
                                                >
                                                    <User
                                                        class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600 dark:text-gray-400"
                                                    />
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        Jenis Kelamin
                                                    </p>
                                                    <span
                                                        class="inline-flex items-center gap-1 lg:gap-2 px-2 py-1 lg:px-3 lg:py-1.5 rounded-full text-xs lg:text-sm font-medium mt-1 truncate"
                                                        :class="genderColor"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak.jenis_kelamin ===
                                                                        'jantan'
                                                                        ? 'Jantan'
                                                                        : 'Betina'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak.jenis_kelamin ===
                                                                        'jantan'
                                                                        ? 'Jantan'
                                                                        : 'Betina'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            ternak.jenis_kelamin ===
                                                            "jantan"
                                                                ? "Jantan"
                                                                : "Betina"
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- === INFORMASI BERDASARKAN KATEGORI === -->

                                    <!-- Breeding Information -->
                                    <div
                                        v-if="isBreeding && ternak.breeding"
                                        class="space-y-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Heart
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-purple-600 dark:text-purple-400"
                                            />
                                            <h3
                                                class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Informasi Breeding
                                            </h3>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/10 dark:to-pink-900/10 rounded-xl p-4 lg:p-5 border border-purple-100 dark:border-purple-800/30"
                                        >
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6"
                                            >
                                                <!-- Status Reproduksi -->
                                                <div>
                                                    <p
                                                        class="text-xs lg:text-sm text-purple-600 dark:text-purple-400 mb-1 font-medium"
                                                    >
                                                        Status Reproduksi
                                                    </p>
                                                    <span
                                                        :class="[
                                                            'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium truncate',
                                                            getBreedingColor(
                                                                ternak.breeding
                                                                    .status_reproduksi
                                                            ),
                                                        ]"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    getBreedingStatusLabel(
                                                                        ternak
                                                                            .breeding
                                                                            .status_reproduksi
                                                                    )
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    getBreedingStatusLabel(
                                                                        ternak
                                                                            .breeding
                                                                            .status_reproduksi
                                                                    )
                                                                )
                                                        "
                                                    >
                                                        <Baby class="w-4 h-4" />
                                                        {{
                                                            getBreedingStatusLabel(
                                                                ternak.breeding
                                                                    .status_reproduksi
                                                            )
                                                        }}
                                                    </span>
                                                </div>

                                                <!-- Tanggal Kawin -->
                                                <div
                                                    v-if="
                                                        ternak.breeding
                                                            .tanggal_kawin
                                                    "
                                                >
                                                    <p
                                                        class="text-xs lg:text-sm text-purple-600 dark:text-purple-400 mb-1 font-medium"
                                                    >
                                                        Tanggal Kawin
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .breeding
                                                                            .tanggal_kawin
                                                                    )
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .breeding
                                                                            .tanggal_kawin
                                                                    )
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                ternak.breeding
                                                                    .tanggal_kawin
                                                            )
                                                        }}
                                                    </p>
                                                </div>

                                                <!-- Perkiraan Melahirkan -->
                                                <div
                                                    v-if="
                                                        ternak.breeding
                                                            .perkiraan_melahirkan
                                                    "
                                                >
                                                    <p
                                                        class="text-xs lg:text-sm text-purple-600 dark:text-purple-400 mb-1 font-medium"
                                                    >
                                                        Perkiraan Melahirkan
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .breeding
                                                                            .perkiraan_melahirkan
                                                                    )
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .breeding
                                                                            .perkiraan_melahirkan
                                                                    )
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                ternak.breeding
                                                                    .perkiraan_melahirkan
                                                            )
                                                        }}
                                                    </p>
                                                </div>

                                                <!-- Total Melahirkan -->
                                                <div>
                                                    <p
                                                        class="text-xs lg:text-sm text-purple-600 dark:text-purple-400 mb-1 font-medium"
                                                    >
                                                        Total Melahirkan
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white text-lg truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .breeding
                                                                        .total_melahirkan +
                                                                        ' kali'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .breeding
                                                                        .total_melahirkan +
                                                                        ' kali'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            ternak.breeding
                                                                .total_melahirkan
                                                        }}
                                                        kali
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Progress Bunting -->
                                            <div
                                                v-if="
                                                    ternak.breeding
                                                        .status_reproduksi ===
                                                        'bunting' &&
                                                    ternak.breeding
                                                        .tanggal_kawin
                                                "
                                                class="mt-4 pt-4 border-t border-purple-100 dark:border-purple-800/30"
                                            >
                                                <div
                                                    class="flex items-center justify-between mb-2"
                                                >
                                                    <p
                                                        class="text-xs font-medium text-purple-600 dark:text-purple-400"
                                                    >
                                                        Progress Bunting
                                                    </p>
                                                    <p
                                                        class="text-xs font-medium text-purple-600 dark:text-purple-400 truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    Math.round(
                                                                        (new Date() -
                                                                            new Date(
                                                                                ternak.breeding.tanggal_kawin
                                                                            )) /
                                                                            (1000 *
                                                                                60 *
                                                                                60 *
                                                                                24)
                                                                    ) + ' hari'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    Math.round(
                                                                        (new Date() -
                                                                            new Date(
                                                                                ternak.breeding.tanggal_kawin
                                                                            )) /
                                                                            (1000 *
                                                                                60 *
                                                                                60 *
                                                                                24)
                                                                    ) + ' hari'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            Math.round(
                                                                (new Date() -
                                                                    new Date(
                                                                        ternak.breeding.tanggal_kawin
                                                                    )) /
                                                                    (1000 *
                                                                        60 *
                                                                        60 *
                                                                        24)
                                                            )
                                                        }}
                                                        hari
                                                    </p>
                                                </div>
                                                <div
                                                    class="w-full bg-purple-100 dark:bg-purple-800/30 rounded-full h-2"
                                                >
                                                    <div
                                                        class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all duration-300"
                                                        :style="{
                                                            width: `${Math.min(
                                                                100,
                                                                (Math.round(
                                                                    (new Date() -
                                                                        new Date(
                                                                            ternak.breeding.tanggal_kawin
                                                                        )) /
                                                                        (1000 *
                                                                            60 *
                                                                            60 *
                                                                            24)
                                                                ) /
                                                                    150) *
                                                                    100
                                                            )}%`,
                                                        }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fattening Information -->
                                    <div
                                        v-else-if="
                                            isFattening && ternak.fattening
                                        "
                                        class="space-y-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <TrendingUp
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-amber-600 dark:text-amber-400"
                                            />
                                            <h3
                                                class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Informasi Fattening
                                            </h3>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 rounded-xl p-4 lg:p-5 border border-amber-100 dark:border-amber-800/30"
                                        >
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6"
                                            >
                                                <!-- Berat Awal -->
                                                <div>
                                                    <p
                                                        class="text-xs lg:text-sm text-amber-600 dark:text-amber-400 mb-1 font-medium"
                                                    >
                                                        Berat Awal
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white text-lg truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .fattening
                                                                        .berat_awal +
                                                                        ' kg'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .fattening
                                                                        .berat_awal +
                                                                        ' kg'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            ternak.fattening
                                                                .berat_awal
                                                        }}
                                                        kg
                                                    </p>
                                                </div>

                                                <!-- Target Berat -->
                                                <div>
                                                    <p
                                                        class="text-xs lg:text-sm text-amber-600 dark:text-amber-400 mb-1 font-medium"
                                                    >
                                                        Target Berat
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white text-lg truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .fattening
                                                                        .target_berat +
                                                                        ' kg'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    ternak
                                                                        .fattening
                                                                        .target_berat +
                                                                        ' kg'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            ternak.fattening
                                                                .target_berat
                                                        }}
                                                        kg
                                                    </p>
                                                </div>

                                                <!-- Tanggal Mulai -->
                                                <div
                                                    v-if="
                                                        ternak.fattening
                                                            .tanggal_mulai
                                                    "
                                                >
                                                    <p
                                                        class="text-xs lg:text-sm text-amber-600 dark:text-amber-400 mb-1 font-medium"
                                                    >
                                                        Tanggal Mulai
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .fattening
                                                                            .tanggal_mulai
                                                                    )
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .fattening
                                                                            .tanggal_mulai
                                                                    )
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                ternak.fattening
                                                                    .tanggal_mulai
                                                            )
                                                        }}
                                                    </p>
                                                </div>

                                                <!-- Estimasi Panen -->
                                                <div
                                                    v-if="
                                                        ternak.fattening
                                                            .estimasi_panen
                                                    "
                                                >
                                                    <p
                                                        class="text-xs lg:text-sm text-amber-600 dark:text-amber-400 mb-1 font-medium"
                                                    >
                                                        Estimasi Panen
                                                    </p>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .fattening
                                                                            .estimasi_panen
                                                                    )
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    formatDate(
                                                                        ternak
                                                                            .fattening
                                                                            .estimasi_panen
                                                                    )
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                ternak.fattening
                                                                    .estimasi_panen
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Progress Fattening -->
                                            <div
                                                class="mt-4 pt-4 border-t border-amber-100 dark:border-amber-800/30"
                                            >
                                                <div
                                                    class="flex items-center justify-between mb-2"
                                                >
                                                    <p
                                                        class="text-xs font-medium text-amber-600 dark:text-amber-400"
                                                    >
                                                        Progress Penggemukan
                                                    </p>
                                                    <p
                                                        class="text-xs font-medium text-amber-600 dark:text-amber-400 truncate"
                                                        @mouseenter="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    getFatteningProgress(
                                                                        ternak.fattening
                                                                    ) + '%'
                                                                )
                                                        "
                                                        @mouseleave="
                                                            hideTooltip
                                                        "
                                                        @mousemove="
                                                            ($event) =>
                                                                showTooltip(
                                                                    $event,
                                                                    getFatteningProgress(
                                                                        ternak.fattening
                                                                    ) + '%'
                                                                )
                                                        "
                                                    >
                                                        {{
                                                            getFatteningProgress(
                                                                ternak.fattening
                                                            )
                                                        }}%
                                                    </p>
                                                </div>
                                                <div
                                                    class="w-full bg-amber-100 dark:bg-amber-800/30 rounded-full h-2"
                                                >
                                                    <div
                                                        class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full transition-all duration-300"
                                                        :style="{
                                                            width: `${getFatteningProgress(
                                                                ternak.fattening
                                                            )}%`,
                                                        }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reguler Information -->
                                    <div
                                        v-else-if="isReguler"
                                        class="space-y-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Tag
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600 dark:text-gray-400"
                                            />
                                            <h3
                                                class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Ternak Reguler
                                            </h3>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800/50 dark:to-blue-900/10 rounded-xl p-4 lg:p-5 border border-gray-200 dark:border-gray-700"
                                        >
                                            <div class="text-center py-4">
                                                <div
                                                    class="w-16 h-16 mx-auto mb-3 rounded-full bg-gradient-to-br from-gray-100 to-blue-100 dark:from-gray-800 dark:to-blue-800/30 flex items-center justify-center"
                                                >
                                                    <Tag
                                                        class="w-8 h-8 text-gray-500 dark:text-gray-400"
                                                    />
                                                </div>
                                                <h4
                                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-2 truncate"
                                                    @mouseenter="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                'Ternak Reguler'
                                                            )
                                                    "
                                                    @mouseleave="hideTooltip"
                                                    @mousemove="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                'Ternak Reguler'
                                                            )
                                                    "
                                                >
                                                    Ternak Reguler
                                                </h4>
                                                <p
                                                    class="text-sm text-gray-600 dark:text-gray-400 truncate"
                                                    @mouseenter="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                'Ternak ini belum dikategorikan ke program breeding atau fattening. Anda dapat mengubah kategori melalui menu edit data.'
                                                            )
                                                    "
                                                    @mouseleave="hideTooltip"
                                                    @mousemove="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                'Ternak ini belum dikategorikan ke program breeding atau fattening. Anda dapat mengubah kategori melalui menu edit data.'
                                                            )
                                                    "
                                                >
                                                    Ternak ini belum
                                                    dikategorikan ke program
                                                    breeding atau fattening.
                                                    Anda dapat mengubah kategori
                                                    melalui menu edit data.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- General Information -->
                                    <div class="space-y-3 lg:space-y-4">
                                        <div class="flex items-center gap-2">
                                            <FileText
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-gray-500 dark:text-gray-400 flex-shrink-0"
                                            />
                                            <h3
                                                class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Informasi Umum
                                            </h3>
                                        </div>

                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 lg:p-5"
                                        >
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6"
                                            >
                                                <!-- Left Column -->
                                                <div
                                                    class="space-y-3 lg:space-y-4"
                                                >
                                                    <!-- Jenis Ternak -->
                                                    <div>
                                                        <p
                                                            class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-1"
                                                        >
                                                            Jenis Ternak
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white truncate"
                                                            @mouseenter="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        ternak.jenis_ternak ||
                                                                            '-'
                                                                    )
                                                            "
                                                            @mouseleave="
                                                                hideTooltip
                                                            "
                                                            @mousemove="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        ternak.jenis_ternak ||
                                                                            '-'
                                                                    )
                                                            "
                                                        >
                                                            {{
                                                                ternak.jenis_ternak ||
                                                                "-"
                                                            }}
                                                        </p>
                                                    </div>

                                                    <!-- Kategori -->
                                                    <div>
                                                        <p
                                                            class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-1"
                                                        >
                                                            Kategori
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white truncate"
                                                            @mouseenter="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        getKategoriDisplay(
                                                                            ternak.kategori
                                                                        )
                                                                    )
                                                            "
                                                            @mouseleave="
                                                                hideTooltip
                                                            "
                                                            @mousemove="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        getKategoriDisplay(
                                                                            ternak.kategori
                                                                        )
                                                                    )
                                                            "
                                                        >
                                                            {{
                                                                getKategoriDisplay(
                                                                    ternak.kategori
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div
                                                    class="space-y-3 lg:space-y-4"
                                                >
                                                    <!-- Tanggal Input -->
                                                    <div>
                                                        <p
                                                            class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-1"
                                                        >
                                                            Ditambahkan Pada
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white truncate"
                                                            @mouseenter="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        formatDateTime(
                                                                            ternak.created_at
                                                                        ) || '-'
                                                                    )
                                                            "
                                                            @mouseleave="
                                                                hideTooltip
                                                            "
                                                            @mousemove="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        formatDateTime(
                                                                            ternak.created_at
                                                                        ) || '-'
                                                                    )
                                                            "
                                                        >
                                                            {{
                                                                formatDateTime(
                                                                    ternak.created_at
                                                                ) || "-"
                                                            }}
                                                        </p>
                                                    </div>

                                                    <!-- Terakhir Diperbarui -->
                                                    <div>
                                                        <p
                                                            class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-1"
                                                        >
                                                            Terakhir Diperbarui
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white truncate"
                                                            @mouseenter="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        formatDateTime(
                                                                            ternak.updated_at
                                                                        ) || '-'
                                                                    )
                                                            "
                                                            @mouseleave="
                                                                hideTooltip
                                                            "
                                                            @mousemove="
                                                                ($event) =>
                                                                    showTooltip(
                                                                        $event,
                                                                        formatDateTime(
                                                                            ternak.updated_at
                                                                        ) || '-'
                                                                    )
                                                            "
                                                        >
                                                            {{
                                                                formatDateTime(
                                                                    ternak.updated_at
                                                                ) || "-"
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Health Alert -->
                                    <div
                                        v-if="
                                            ternak.status_aktif === 'aktif' &&
                                            ternak.status_kesehatan !==
                                                'sehat' &&
                                            ternak.status_kesehatan !== 'null'
                                        "
                                        class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-xl p-3 lg:p-4"
                                    >
                                        <div
                                            class="flex items-start gap-2 lg:gap-3"
                                        >
                                            <AlertCircle
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-amber-600 dark:text-amber-500 flex-shrink-0 mt-0.5"
                                            />
                                            <div class="min-w-0">
                                                <p
                                                    class="font-medium text-amber-800 dark:text-amber-300 mb-1 text-sm lg:text-base truncate"
                                                    @mouseenter="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                `Status Kesehatan: ${ternak.status_kesehatan}`
                                                            )
                                                    "
                                                    @mouseleave="hideTooltip"
                                                    @mousemove="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                `Status Kesehatan: ${ternak.status_kesehatan}`
                                                            )
                                                    "
                                                >
                                                    Status Kesehatan:
                                                    {{
                                                        ternak.status_kesehatan
                                                    }}
                                                </p>
                                                <p
                                                    class="text-xs lg:text-sm text-amber-700 dark:text-amber-400 truncate"
                                                    @mouseenter="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                ternak.status_kesehatan ===
                                                                    'perawatan'
                                                                    ? 'Ternak sedang dalam masa perawatan dan membutuhkan monitoring intensif.'
                                                                    : 'Ternak menunjukkan gejala sakit dan membutuhkan penanganan medis segera.'
                                                            )
                                                    "
                                                    @mouseleave="hideTooltip"
                                                    @mousemove="
                                                        ($event) =>
                                                            showTooltip(
                                                                $event,
                                                                ternak.status_kesehatan ===
                                                                    'perawatan'
                                                                    ? 'Ternak sedang dalam masa perawatan dan membutuhkan monitoring intensif.'
                                                                    : 'Ternak menunjukkan gejala sakit dan membutuhkan penanganan medis segera.'
                                                            )
                                                    "
                                                >
                                                    {{
                                                        ternak.status_kesehatan ===
                                                        "perawatan"
                                                            ? "Ternak sedang dalam masa perawatan dan membutuhkan monitoring intensif."
                                                            : "Ternak menunjukkan gejala sakit dan membutuhkan penanganan medis segera."
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes Section -->
                                    <div
                                        v-if="ternak.catatan"
                                        class="space-y-3 lg:space-y-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Clipboard
                                                class="w-4 h-4 lg:w-5 lg:h-5 text-gray-500 dark:text-gray-400 flex-shrink-0"
                                            />
                                            <h3
                                                class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Catatan Khusus
                                            </h3>
                                        </div>
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 lg:p-5"
                                        >
                                            <p
                                                class="text-gray-700 dark:text-gray-300 whitespace-pre-line text-sm lg:text-base truncate"
                                                @mouseenter="
                                                    ($event) =>
                                                        showTooltip(
                                                            $event,
                                                            ternak.catatan
                                                        )
                                                "
                                                @mouseleave="hideTooltip"
                                                @mousemove="
                                                    ($event) =>
                                                        showTooltip(
                                                            $event,
                                                            ternak.catatan
                                                        )
                                                "
                                            >
                                                {{ ternak.catatan }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="sticky bottom-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-4 py-3 lg:px-6 lg:py-4"
                        >
                            <div
                                class="flex flex-col sm:flex-row items-center justify-between gap-3 lg:gap-4"
                            >
                                <!-- Metadata -->
                                <div
                                    class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 w-full sm:w-auto"
                                >
                                    <div
                                        class="flex flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-4"
                                    >
                                        <span
                                            class="truncate"
                                            @mouseenter="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'ID: ' +
                                                            (ternak.kode_ternak ||
                                                                '-')
                                                    )
                                            "
                                            @mouseleave="hideTooltip"
                                            @mousemove="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'ID: ' +
                                                            (ternak.kode_ternak ||
                                                                '-')
                                                    )
                                            "
                                        >
                                            ID: {{ ternak.kode_ternak || "-" }}
                                        </span>
                                        <span class="hidden sm:inline">•</span>
                                        <span
                                            class="truncate"
                                            @mouseenter="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'Kategori: ' +
                                                            getKategoriDisplay(
                                                                ternak.kategori
                                                            )
                                                    )
                                            "
                                            @mouseleave="hideTooltip"
                                            @mousemove="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'Kategori: ' +
                                                            getKategoriDisplay(
                                                                ternak.kategori
                                                            )
                                                    )
                                            "
                                        >
                                            Kategori:
                                            {{
                                                getKategoriDisplay(
                                                    ternak.kategori
                                                )
                                            }}
                                        </span>
                                        <span class="hidden sm:inline">•</span>
                                        <span
                                            class="truncate"
                                            @mouseenter="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'Diperbarui: ' +
                                                            (formatDateTime(
                                                                ternak.updated_at
                                                            ) || '-')
                                                    )
                                            "
                                            @mouseleave="hideTooltip"
                                            @mousemove="
                                                ($event) =>
                                                    showTooltip(
                                                        $event,
                                                        'Diperbarui: ' +
                                                            (formatDateTime(
                                                                ternak.updated_at
                                                            ) || '-')
                                                    )
                                            "
                                        >
                                            Diperbarui:
                                            {{
                                                formatDateTime(
                                                    ternak.updated_at
                                                ) || "-"
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex items-center gap-2 lg:gap-3 w-full sm:w-auto justify-end"
                                >
                                    <button
                                        @click="$emit('close')"
                                        class="px-3 py-1.5 lg:px-5 lg:py-2.5 text-xs lg:text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg lg:rounded-xl transition-colors duration-200 flex-1 sm:flex-none"
                                    >
                                        Tutup
                                    </button>
                                    <button
                                        @click="$emit('edit')"
                                        class="px-3 py-1.5 lg:px-5 lg:py-2.5 text-xs lg:text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 rounded-lg lg:rounded-xl transition-colors duration-200 flex items-center gap-1 lg:gap-2 flex-1 sm:flex-none justify-center"
                                    >
                                        <Edit class="w-3 h-3 lg:w-4 lg:h-4" />
                                        Edit Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom Scrollbar */
.max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar,
.max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar {
    width: 6px;
}

.max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-track,
.max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}

.max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-thumb,
.max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-thumb:hover,
.max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-track,
.dark .max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-track {
    background: #1e293b;
}

.dark .max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-thumb,
.dark .max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-thumb {
    background: #475569;
}

.dark .max-h-\[calc\(90vh-140px\)\]::-webkit-scrollbar-thumb:hover,
.dark .max-h-\[calc\(80vh-140px\)\]::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, transform,
        opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Tooltip styles */
.tooltip-wrapper {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
}

.tooltip-content {
    position: relative;
    animation: tooltipFadeIn 0.2s ease-out;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.dark .tooltip-content {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3),
        0 10px 10px -5px rgba(0, 0, 0, 0.2);
}

@keyframes tooltipFadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
