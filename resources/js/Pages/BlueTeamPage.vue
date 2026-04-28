<template>
    <NotesLayout team="blueteam" teamLabel="Blue Team" :tree="tree" :note="note" accentColor="blue" />
</template>

<script setup lang="ts">
import NotesLayout from '@/components/NotesLayout.vue';

defineOptions({ layout: false });

defineProps<{
    tree: any[];
    note: { title: string; content: string; path: string } | null;
}>();
</script>
