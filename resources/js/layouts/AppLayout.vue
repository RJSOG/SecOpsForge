<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-white">
        <AppNavbar />
        <main class="pt-14">
            <slot />
        </main>
    </div>
</template>
