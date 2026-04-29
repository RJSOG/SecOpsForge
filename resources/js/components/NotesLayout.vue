<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import NotesSidebar from '@/components/NotesSidebar.vue';
import { useTheme } from '@/composables/useTheme';
import { Link, usePage } from '@inertiajs/vue3';

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
    accentColor: 'red' | 'blue' | 'amber';
}>();

const { isDark } = useTheme();
const page = usePage();
const isAuthenticated = !!page.props.auth?.user;

const accentClasses = {
    red: {
        badge: 'bg-red-500/10 text-red-500 ring-1 ring-red-500/20',
        heading: 'text-red-500',
    },
    blue: {
        badge: 'bg-blue-500/10 text-blue-500 ring-1 ring-blue-500/20',
        heading: 'text-blue-500',
    },
    amber: {
        badge: 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20',
        heading: 'text-amber-500',
    },
};

const accent = accentClasses[props.accentColor];
</script>

<template>
    <div class="min-h-screen transition-colors duration-200" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">
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
                        <div class="mt-3 flex items-center gap-3">
                            <h1 class="text-2xl font-bold tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
                                {{ note.title }}
                            </h1>
                            <Link
                                v-if="isAuthenticated && note"
                                :href="`/editor?team=${team}&path=${note.path}`"
                                class="rounded-md px-2.5 py-1 text-xs font-medium transition"
                                :class="isDark
                                    ? 'text-slate-500 hover:bg-slate-800 hover:text-emerald-400'
                                    : 'text-slate-400 hover:bg-slate-100 hover:text-emerald-600'"
                            >
                                Edit
                            </Link>
                        </div>
                    </div>
                    <article
                        v-if="isDark"
                        class="prose prose-invert max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-slate-100 prose-p:text-slate-300 prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-200 prose-code:rounded prose-code:bg-slate-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:text-slate-300 prose-pre:border prose-pre:border-slate-800 prose-pre:bg-slate-950 prose-th:text-slate-300 prose-td:text-slate-400"
                        v-html="note.content"
                    />
                    <article
                        v-else
                        class="prose max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-slate-900 prose-p:text-slate-600 prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-800 prose-code:rounded prose-code:bg-slate-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:text-slate-700 prose-pre:border prose-pre:border-slate-200 prose-pre:bg-slate-50 prose-th:text-slate-700 prose-td:text-slate-600"
                        v-html="note.content"
                    />
                </div>

                <!-- Empty state -->
                <div v-else class="flex h-[70vh] items-center justify-center">
                    <div class="text-center">
                        <h2 :class="['mb-2 text-2xl font-bold tracking-tight', accent.heading]">{{ teamLabel }}</h2>
                        <p :class="isDark ? 'text-slate-500' : 'text-slate-400'">
                            Sélectionnez une note dans la sidebar.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
