<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import { route } from 'ziggy-js';

function isSideBarVisible() {
    return !(route().current('home') || route().current('about'));
}
</script>

<template>
    <div class="flex min-h-screen bg-slate-800 text-white">
        <AppSidebar class="w-64" v-if="isSideBarVisible()" />
        <div class="flex flex-1 flex-col overflow-hidden">
            <AppNavbar class="w-full" />
            <main
                class="mt-14 flex-1 overflow-y-auto p-4"
                :class="{ 'ml-64': isSideBarVisible() }"
            >
                <slot />
            </main>
        </div>
    </div>
</template>
