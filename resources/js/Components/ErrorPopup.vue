<template>
    <div
        v-if="show"
        class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50"
    >
        <div class="bg-red-600 text-white px-6 py-4 rounded-xl shadow-xl">
            <p>{{ message }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    message: String,
});
</script>
