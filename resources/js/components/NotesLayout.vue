<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import NotesSidebar from '@/components/NotesSidebar.vue';

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
        badge: 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
        heading: 'text-red-400',
        prose: 'prose-red',
    },
    blue: {
        badge: 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20',
        heading: 'text-blue-400',
        prose: 'prose-blue',
    },
};

const accent = accentClasses[props.accentColor];
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-white">
        <AppNavbar />

        <div class="flex pt-14">
            <NotesSidebar :tree="tree" :team="team" :accentColor="accentColor" />

            <div class="flex-1 overflow-y-auto p-8">
                <!-- Note content -->
                <div v-if="note" class="mx-auto max-w-4xl">
                    <div class="mb-8">
                        <span :class="['inline-block rounded-full px-3 py-1 text-xs font-medium', accent.badge]">
                            {{ teamLabel }}
                        </span>
                        <h1 class="mt-3 text-2xl font-bold tracking-tight text-white">{{ note.title }}</h1>
                    </div>
                    <article
                        class="prose prose-invert max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-slate-100 prose-p:text-slate-300 prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-200 prose-code:rounded prose-code:bg-slate-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:text-slate-300 prose-pre:border prose-pre:border-slate-800 prose-pre:bg-slate-950 prose-th:text-slate-300 prose-td:text-slate-400"
                        :class="accent.prose"
                        v-html="note.content"
                    />
                </div>

                <!-- Empty state -->
                <div v-else class="flex h-[70vh] items-center justify-center">
                    <div class="text-center">
                        <h2 :class="['mb-2 text-2xl font-bold tracking-tight', accent.heading]">{{ teamLabel }}</h2>
                        <p class="text-slate-500">
                            Sélectionnez une note dans la sidebar.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
