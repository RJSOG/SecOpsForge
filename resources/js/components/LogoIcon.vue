<template>
    <img src="/logo.svg" :alt="alt" :class="sizeClass" />
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    alt?: string;
    sizeClass?: string;
}>(), {
    alt: 'SecOpsForge',
    sizeClass: 'h-8 w-8',
});
</script>
