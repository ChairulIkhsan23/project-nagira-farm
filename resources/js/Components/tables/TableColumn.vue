<script setup>
import { computed } from "vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    column: {
        type: Object,
        required: true,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const cellValue = computed(() => {
    if (typeof props.column.accessor === "function") {
        return props.column.accessor(props.item);
    } else if (typeof props.column.accessor === "string") {
        // Handle nested properties with dot notation
        return props.column.accessor
            .split(".")
            .reduce((obj, key) => obj?.[key], props.item);
    }
    return props.item[props.column.key];
});

const cellClasses = computed(() => {
    const classes = [];

    if (props.compact) {
        classes.push("py-2 px-2");
    } else {
        classes.push("py-3 px-3 sm:px-4 md:py-4 md:px-6");
    }

    if (props.column.cellClass) {
        if (typeof props.column.cellClass === "function") {
            classes.push(props.column.cellClass(props.item));
        } else {
            classes.push(props.column.cellClass);
        }
    }

    return classes.join(" ");
});

const cellStyles = computed(() => {
    if (props.column.cellStyle) {
        if (typeof props.column.cellStyle === "function") {
            return props.column.cellStyle(props.item);
        }
        return props.column.cellStyle;
    }
    return {};
});
</script>

<template>
    <td
        :class="cellClasses"
        :style="cellStyles"
        @click="$emit('cell-click', item, column, $event)"
    >
        <!-- Slot Custom -->
        <slot
            v-if="$slots.default || $slots[column.key]"
            :name="column.key"
            :item="item"
            :value="cellValue"
        >
            <slot :item="item" :value="cellValue"></slot>
        </slot>

        <!-- Render Custom Component -->
        <component
            v-else-if="column.component"
            :is="column.component"
            :item="item"
            :value="cellValue"
            :column="column"
        />

        <!-- Formatting Function -->
        <span
            v-else-if="column.format"
            v-html="column.format(cellValue, item)"
        ></span>

        <!-- Default Render -->
        <span v-else :title="String(cellValue)" class="truncate block">
            {{ cellValue }}
        </span>
    </td>
</template>
