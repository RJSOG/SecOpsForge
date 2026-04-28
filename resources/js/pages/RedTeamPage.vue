<template>
    <NotesLayout team="redteam" teamLabel="Red Team" :tree="tree" :note="note" accentColor="red" />
</template>

<script setup lang="ts">
import NotesLayout from '@/components/NotesLayout.vue';

defineProps<{
    tree: any[];
    note: { title: string; content: string; path: string } | null;
}>();
</script>
