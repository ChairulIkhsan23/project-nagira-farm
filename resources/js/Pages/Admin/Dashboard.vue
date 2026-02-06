<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import {
    Database,
    PackagePlus,
    HeartPulse,
    ShoppingCart,
    Plus,
} from "lucide-vue-next";

// contoh data stat
const stats = [
    {
        title: "Total Ternak",
        value: 120,
        icon: Database,
        description: "Jumlah total ternak yang tercatat",
    },
    {
        title: "Pakan Masuk",
        value: 45,
        icon: PackagePlus,
        description: "Total transaksi pakan masuk bulan ini",
    },
    {
        title: "Kesehatan Aktif",
        value: 12,
        icon: HeartPulse,
        description: "Jumlah ternak dalam perawatan / pemeriksaan",
    },
    {
        title: "Penjualan",
        value: 27,
        icon: ShoppingCart,
        description: "Transaksi penjualan ternak atau produk",
    },
];

const recentActivities = [
    { time: "10 menit lalu", text: "Penambahan ternak sapi baru." },
    { time: "1 jam lalu", text: "Stok pakan ditambah 50kg." },
    { time: "3 jam lalu", text: "Pemeriksaan kesehatan selesai." },
];

defineOptions({
    layout: AdminLayout,
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6 transition-all duration-300 ease-in-out">
        <!-- HEADER - SERAGAM DENGAN INDEX -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h1
                    class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
                >
                    Dashboard
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 md:mt-2">
                    Selamat datang di sistem manajemen peternakan
                </p>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div
                v-for="stat in stats"
                :key="stat.title"
                class="p-6 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm transition-all duration-300"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="font-semibold text-gray-600 dark:text-gray-300"
                    >
                        {{ stat.title }}
                    </span>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <component
                            :is="stat.icon"
                            class="w-8 h-8 text-gray-700"
                        />
                    </div>
                </div>

                <p
                    class="mt-3 text-3xl font-bold text-gray-900 dark:text-white"
                >
                    {{ stat.value }}
                </p>
            </div>
        </div>

        <!-- CHART + ACTIVITY -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- CHART -->
            <div
                class="xl:col-span-2 p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm transition-all"
            >
                <h2
                    class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                >
                    Grafik Perkembangan Ternak
                </h2>

                <div
                    class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400"
                >
                    <span
                        >Chart placeholder — nanti bisa diganti ApexCharts</span
                    >
                </div>
            </div>

            <!-- RECENT ACTIVITY -->
            <div
                class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm transition-all"
            >
                <h2
                    class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                >
                    Aktivitas Terbaru
                </h2>

                <ul class="space-y-4">
                    <li
                        v-for="item in recentActivities"
                        :key="item.time"
                        class="flex flex-col"
                    >
                        <span class="text-sm text-gray-400">{{
                            item.time
                        }}</span>
                        <span class="text-gray-700 dark:text-gray-300">{{
                            item.text
                        }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div
            class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm transition-all"
        >
            <h2
                class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
            >
                Daftar Ternak Terbaru
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700"
                        >
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Berat</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="i in 5"
                            :key="i"
                            class="border-b border-gray-100 dark:border-gray-800"
                        >
                            <td class="py-3 px-4">Sapi Bali</td>
                            <td class="py-3 px-4">250 Kg</td>
                            <td class="py-3 px-4">
                                <span
                                    class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700"
                                >
                                    Sehat
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <Link
                                    href="/admin/ternak/detail/1"
                                    class="text-blue-600 hover:underline"
                                >
                                    Detail
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
