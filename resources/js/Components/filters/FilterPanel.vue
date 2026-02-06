<script setup>
import { ref, computed, watch } from "vue";
import {
    Filter,
    X,
    CheckCircle,
    AlertCircle,
    XCircle,
    HelpCircle,
} from "lucide-vue-next";

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    filterConfig: {
        type: Array,
        required: true,
    },
    activeFilterCount: {
        type: Number,
        default: 0,
    },
    position: {
        type: String,
        default: "right",
        validator: (value) =>
            ["left", "right", "top", "bottom"].includes(value),
    },
    variant: {
        type: String,
        default: "dropdown",
        validator: (value) => ["dropdown", "inline", "modal"].includes(value),
    },
    title: {
        type: String,
        default: "Filter Data",
    },
});

const emit = defineEmits(["update:filters", "apply", "reset", "close"]);

const localFilters = ref({ ...props.filters });
const isOpen = ref(false);

const panelPosition = computed(() => {
    const positions = {
        right: "right-0",
        left: "left-0",
        top: "top-full left-0",
        bottom: "top-full left-0",
    };
    return positions[props.position];
});

const panelClasses = computed(() => {
    const classes = [
        "bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50",
    ];

    if (props.variant === "dropdown") {
        classes.push("absolute mt-2 w-80", panelPosition.value);
    } else if (props.variant === "modal") {
        classes.push(
            "fixed inset-0 md:inset-auto md:absolute md:mt-2 md:w-80 m-4 md:m-0"
        );
    }

    return classes.join(" ");
});

const updateFilter = (key, value) => {
    localFilters.value[key] = localFilters.value[key] === value ? "" : value;
};

const handleApply = () => {
    emit("update:filters", { ...localFilters.value });
    emit("apply", { ...localFilters.value });
    isOpen.value = false;
};

const handleReset = () => {
    const resetFilters = {};
    props.filterConfig.forEach((config) => {
        resetFilters[config.key] = "";
    });
    localFilters.value = resetFilters;
    emit("update:filters", resetFilters);
    emit("reset", resetFilters);
    isOpen.value = false;
};

const handleClose = () => {
    localFilters.value = { ...props.filters };
    isOpen.value = false;
    emit("close");
};

// Reset local filters when props change
watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...newFilters };
    },
    { deep: true }
);

defineExpose({
    open: () => (isOpen.value = true),
    close: () => (isOpen.value = false),
    toggle: () => (isOpen.value = !isOpen.value),
});
</script>

<template>
    <!-- Trigger Button (for dropdown variant) -->
    <div v-if="variant === 'dropdown'" class="relative">
        <slot name="trigger" :open="isOpen" :toggle="() => (isOpen = !isOpen)">
            <button
                @click="isOpen = !isOpen"
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
        </slot>

        <!-- Filter Panel -->
        <div v-if="isOpen" :class="panelClasses" v-click-outside="handleClose">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        {{ title }}
                    </h3>
                    <button
                        v-if="activeFilterCount > 0"
                        @click="handleReset"
                        class="text-sm text-blue-500 hover:text-blue-600"
                    >
                        Reset
                    </button>
                </div>

                <!-- Filter Sections -->
                <div
                    v-for="config in filterConfig"
                    :key="config.key"
                    class="mb-4 last:mb-6"
                >
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        {{ config.label }}
                    </label>

                    <!-- Button Group Type -->
                    <div
                        v-if="config.type === 'button-group'"
                        class="grid gap-2"
                        :class="`grid-cols-${config.columns || 3}`"
                    >
                        <button
                            v-for="option in config.options"
                            :key="option.value"
                            @click="updateFilter(config.key, option.value)"
                            class="px-3 py-2 rounded-lg border text-sm transition-all duration-200"
                            :class="
                                localFilters[config.key] === option.value
                                    ? option.classes
                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                            "
                        >
                            {{ option.label }}
                        </button>
                    </div>

                    <!-- Toggle Type -->
                    <div
                        v-else-if="config.type === 'toggle'"
                        class="flex gap-2"
                    >
                        <button
                            v-for="option in config.options"
                            :key="option.value"
                            @click="updateFilter(config.key, option.value)"
                            class="flex-1 px-3 py-2 rounded-lg border text-sm transition-all duration-200 flex items-center justify-center gap-2"
                            :class="
                                localFilters[config.key] === option.value
                                    ? option.classes
                                    : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600'
                            "
                        >
                            <component
                                v-if="option.icon"
                                :is="option.icon"
                                class="w-4 h-4"
                            />
                            <span>{{ option.label }}</span>
                        </button>
                    </div>

                    <!-- Select Type -->
                    <select
                        v-else-if="config.type === 'select'"
                        v-model="localFilters[config.key]"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua</option>
                        <option
                            v-for="option in config.options"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>

                    <!-- Date Range Type -->
                    <div
                        v-else-if="config.type === 'date-range'"
                        class="flex gap-2"
                    >
                        <input
                            type="date"
                            v-model="localFilters[config.key].start"
                            class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                        />
                        <input
                            type="date"
                            v-model="localFilters[config.key].end"
                            class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button
                        @click="handleApply"
                        class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium"
                    >
                        Terapkan
                    </button>
                    <button
                        @click="handleClose"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inline Variant -->
    <div v-else-if="variant === 'inline'" :class="panelClasses">
        <div class="p-4">
            <!-- Same content as dropdown but without trigger -->
            <slot></slot>
        </div>
    </div>

    <!-- Modal Variant -->
    <div
        v-else-if="variant === 'modal' && isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
        <div
            :class="panelClasses"
            class="max-w-md w-full max-h-[90vh] overflow-y-auto"
        >
            <div class="p-4">
                <!-- Same content as dropdown -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        {{ title }}
                    </h3>
                    <button
                        @click="handleClose"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <!-- Filter content here -->
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for modal */
.modal-content {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.modal-content::-webkit-scrollbar {
    width: 6px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
