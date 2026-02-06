<script setup>
import { ref, computed, watch } from "vue";
import TableColumn from "./TableColumn.vue";

const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    columns: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    emptyState: {
        type: Object,
        default: () => ({
            title: "Data tidak ditemukan",
            message: "Belum ada data yang tersedia",
            icon: null,
        }),
    },
    striped: {
        type: Boolean,
        default: true,
    },
    hoverable: {
        type: Boolean,
        default: true,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["row-click"]);

const handleRowClick = (item, event) => {
    emit("row-click", item, event);
};

const tableClasses = computed(() => {
    const classes = ["w-full table-fixed text-sm"];

    if (props.compact) {
        classes.push("text-xs");
    }

    return classes;
});

const rowClasses = computed(() => {
    const classes = [];

    if (props.striped) {
        classes.push(
            "odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-900/50"
        );
    }

    if (props.hoverable) {
        classes.push(
            "hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors duration-150"
        );
    }

    return classes.join(" ");
});
</script>

<template>
    <div class="overflow-y-auto overflow-x-hidden">
        <table :class="tableClasses">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        :class="[
                            'text-left py-3 px-3 sm:px-4 md:py-4 md:px-6',
                            'text-xs md:text-sm font-semibold',
                            'text-gray-900 dark:text-white',
                            column.headerClass,
                            column.minWidth ? `min-w-[${column.minWidth}]` : '',
                        ]"
                        :style="column.width ? { width: column.width } : {}"
                    >
                        {{ column.title }}
                    </th>
                </tr>
            </thead>

            <tbody
                v-if="!loading && data.length > 0"
                class="divide-y divide-gray-200 dark:divide-gray-700"
            >
                <tr
                    v-for="(item, index) in data"
                    :key="item.id || index"
                    :class="[rowClasses, item.rowClass]"
                    @click="handleRowClick(item, $event)"
                >
                    <TableColumn
                        v-for="column in columns"
                        :key="column.key"
                        :item="item"
                        :column="column"
                        :compact="compact"
                    />
                </tr>
            </tbody>

            <!-- Loading State -->
            <tbody v-if="loading">
                <tr v-for="i in 5" :key="`skeleton-${i}`">
                    <td
                        v-for="column in columns"
                        :key="`${column.key}-${i}`"
                        :class="[
                            'py-3 px-3 sm:px-4 md:py-4 md:px-6',
                            column.cellClass,
                        ]"
                        :colspan="columns.length"
                    >
                        <div class="flex items-center space-x-4">
                            <div
                                class="flex-1 space-y-3"
                                v-if="column.key === columns[0].key"
                            >
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                                ></div>
                                <div
                                    class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-3/4"
                                ></div>
                            </div>
                            <div
                                v-else
                                class="h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20"
                            ></div>
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Empty State -->
            <tbody v-if="!loading && data.length === 0">
                <tr>
                    <td :colspan="columns.length" class="py-8 px-4 text-center">
                        <slot name="empty-state" :empty-state="emptyState">
                            <div
                                class="flex flex-col items-center justify-center"
                            >
                                <component
                                    :is="emptyState.icon"
                                    v-if="emptyState.icon"
                                    class="w-12 h-12 md:w-16 md:h-16 text-gray-300 dark:text-gray-600 mb-3"
                                />
                                <h3
                                    class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-1 md:mb-2"
                                >
                                    {{ emptyState.title }}
                                </h3>
                                <p
                                    class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-4 md:mb-6 max-w-xs"
                                >
                                    {{ emptyState.message }}
                                </p>
                                <slot name="empty-action"></slot>
                            </div>
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
