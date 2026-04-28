<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-800 text-white">
        <AppNavbar class="w-full" />
        <main class="mt-14">
            <slot />
        </main>
    </div>
</template>
