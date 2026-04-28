<template>
    <div class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="mb-4 text-3xl font-bold tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">About</h1>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-500'">À propos de ce site.</p>
    </div>
</template>

<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';

const { isDark } = useTheme();
</script>
