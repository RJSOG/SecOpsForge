<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import { useTheme } from '@/composables/useTheme';

const { isDark } = useTheme();
</script>

<template>
    <div class="min-h-screen transition-colors duration-200" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">
        <AppNavbar />
        <main class="pt-14">
            <slot />
        </main>
    </div>
</template>
