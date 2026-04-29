<template>
    <NotesLayout team="automation" teamLabel="Automation" :tree="tree" :note="note" accentColor="amber" />
</template>

<script setup lang="ts">
import NotesLayout from '@/components/NotesLayout.vue';

defineOptions({ layout: false });

defineProps<{
    tree: any[];
    note: { title: string; content: string; path: string } | null;
}>();
</script>
