<template>
    <div
        v-if="isMobileOpen"
        class="fixed inset-0 bg-transparent z-[9997] lg:hidden"
        @click="toggleMobileSidebar"
    ></div>
</template>

<script setup lang="ts">
import { useSidebar } from "@/composables/useSidebar";

const { toggleMobileSidebar, isMobileOpen } = useSidebar();
</script>
