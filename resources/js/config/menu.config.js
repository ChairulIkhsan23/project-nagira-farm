import {
    GridIcon,
    CalenderIcon,
    ListIcon,
    TableIcon,
    PieChartIcon,
    PageIcon,
    UserCircleIcon,
    BarChartIcon,
    DocsIcon,
    SettingsIcon,
} from "@/Components/icons";

export const menuGroups = [
    {
        title: "Dashboard",
        items: [
            {
                name: "Dashboard",
                icon: GridIcon,
                path: "/admin/dashboard",
            },
        ],
    },
    // @/config/menu.config.js
    {
        title: "Data Ternak",
        items: [
            {
                name: "Ternak",
                icon: UserCircleIcon,
                children: [
                    {
                        name: "Semua Ternak",
                        path: "/admin/ternak",
                        exact: true,
                    },
                    {
                        name: "Breeding",
                        path: "/admin/ternak/breeding",
                        exact: true,
                    },
                    {
                        name: "Fattening",
                        path: "/admin/ternak/fattening",
                        exact: true,
                    },
                ],
            },
        ],
    },
    {
        title: "Kesehatan & Pakan",
        items: [
            {
                name: "Kesehatan Ternak",
                icon: CalenderIcon,
                path: "/admin/kesehatan",
            },
            {
                name: "Pakan & Pemeliharaan",
                icon: TableIcon,
                path: "/admin/pakan",
            },
        ],
    },

    {
        title: "Produksi",
        items: [
            {
                name: "Produksi Ternak",
                icon: PieChartIcon,
                children: [
                    { name: "Kelahiran", path: "/admin/produksi/kelahiran" },
                    { name: "Kematian", path: "/admin/produksi/kematian" },
                ],
            },
            {
                name: "Hasil Produksi",
                icon: PageIcon,
            },
        ],
    },

    {
        title: "Konten",
        items: [
            {
                name: "Artikel & Berita",
                icon: DocsIcon, // DocumentTextIcon tidak ada → pakai DocsIcon
                path: "/admin/artikel",
            },
        ],
    },

    {
        title: "Pengaduan",
        items: [
            {
                name: "Pesan Masuk",
                icon: CalenderIcon,
                path: "/admin/pengaduan",
            },
        ],
    },

    {
        title: "Pengaturan",
        items: [
            {
                name: "Profil Peternakan",
                icon: SettingsIcon,
                path: "/admin/profil",
            },
        ],
    },

    {
        title: "Laporan",
        items: [
            {
                name: "Laporan",
                icon: BarChartIcon,
                children: [
                    { name: "Laporan Pakan", path: "/admin/laporan/pakan" },
                    {
                        name: "Laporan Produksi",
                        path: "/admin/laporan/produksi",
                    },
                    { name: "Laporan Ternak", path: "/admin/laporan/ternak" },
                ],
            },
        ],
    },
];
