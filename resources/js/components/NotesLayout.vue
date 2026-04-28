<script setup lang="ts">
import NotesSidebar from '@/components/NotesSidebar.vue';
import { Link } from '@inertiajs/vue3';

interface NoteNode {
    name: string;
    type: 'file' | 'folder';
    path: string;
    children?: NoteNode[];
}

const props = defineProps<{
    team: string;
    teamLabel: string;
    tree: NoteNode[];
    note: { title: string; content: string; path: string } | null;
    accentColor: 'red' | 'blue';
}>();

const accentClasses = {
    red: {
        badge: 'bg-red-500/20 text-red-400 border border-red-500/30',
        heading: 'text-red-400',
        link: 'hover:text-red-400',
        prose: 'prose-red',
    },
    blue: {
        badge: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
        heading: 'text-blue-400',
        link: 'hover:text-blue-400',
        prose: 'prose-blue',
    },
};

const accent = accentClasses[props.accentColor];
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <NotesSidebar :tree="tree" :team="team" :accentColor="accentColor" />

        <!-- Main content -->
        <div class="ml-64 flex-1 p-8 pt-20">
            <!-- Note content -->
            <div v-if="note" class="mx-auto max-w-4xl">
                <div class="mb-6">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-medium', accent.badge]">
                        {{ teamLabel }}
                    </span>
                </div>
                <article
                    class="prose prose-invert max-w-none prose-headings:text-slate-100 prose-a:text-blue-400 prose-code:rounded prose-code:bg-slate-700 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-slate-200 prose-pre:bg-slate-900 prose-pre:border prose-pre:border-slate-700"
                    :class="accent.prose"
                    v-html="note.content"
                />
            </div>

            <!-- Empty state -->
            <div v-else class="flex h-full items-center justify-center">
                <div class="text-center">
                    <h2 :class="['mb-2 text-2xl font-bold', accent.heading]">{{ teamLabel }}</h2>
                    <p class="text-slate-400">
                        Sélectionnez une note dans la sidebar pour commencer.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
