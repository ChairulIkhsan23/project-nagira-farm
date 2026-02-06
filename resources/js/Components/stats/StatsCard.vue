<!-- resources/js/Components/stats/StatsCard.vue -->
<script setup>
import { computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    description: {
        type: String,
        default: "",
    },
    icon: {
        type: [Object, Function],
        required: true,
    },
    iconClass: {
        type: String,
        default: "text-gray-600 dark:text-gray-300",
    },
    iconBgClass: {
        type: String,
        default: "bg-gray-100 dark:bg-gray-700",
    },
    loading: {
        type: Boolean,
        default: false,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const cardClasses = computed(() => {
    const classes = [
        "bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900",
        "rounded-xl p-4 border border-gray-200 dark:border-gray-700",
        "shadow-sm hover:shadow-md transition-all duration-200",
    ];

    if (props.compact) {
        classes.push("p-3");
    }

    return classes.join(" ");
});
</script>

<template>
    <div :class="cardClasses">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    {{ title }}
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <template v-if="!loading">
                        {{ value }}
                    </template>
                    <template v-else>
                        <span
                            class="inline-block w-16 h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"
                        ></span>
                    </template>
                </p>
                <p
                    v-if="description"
                    class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                >
                    {{ description }}
                </p>
            </div>
            <div :class="['p-3 rounded-lg flex-shrink-0', iconBgClass]">
                <component :is="icon" :class="['w-5 h-5', iconClass]" />
            </div>
        </div>
    </div>
</template>
