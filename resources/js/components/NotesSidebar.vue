<script setup lang="ts">
import NotesSidebarNode from '@/components/NotesSidebarNode.vue';
import { useTheme } from '@/composables/useTheme';
import { Link, usePage } from '@inertiajs/vue3';

interface NoteNode {
    name: string;
    type: 'file' | 'folder';
    path: string;
    children?: NoteNode[];
}

defineProps<{
    tree: NoteNode[];
    team: string;
    accentColor: 'red' | 'blue' | 'amber';
    // Mobile only: the sidebar is an off-canvas drawer below md.
    open?: boolean;
}>();

const { isDark } = useTheme();
const page = usePage();
const isAuthenticated = !!page.props.auth?.user;
</script>

<template>
    <aside
        id="notes-sidebar"
        class="fixed bottom-0 left-0 top-14 z-40 w-72 max-w-[85vw] flex-shrink-0 overflow-y-auto border-r transition-transform duration-200 md:sticky md:z-auto md:h-[calc(100vh-3.5rem)] md:w-64 md:translate-x-0"
        :class="[open ? 'translate-x-0' : '-translate-x-full', isDark ? 'border-slate-800 bg-slate-950' : 'border-slate-200 bg-slate-50']"
    >
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h3
                    class="text-[11px] font-semibold uppercase tracking-widest"
                    :class="isDark ? 'text-slate-600' : 'text-slate-400'"
                >
                    Notes
                </h3>
                <Link
                    v-if="isAuthenticated"
                    :href="`/editor?team=${team}`"
                    class="rounded-md p-1 transition"
                    :class="isDark ? 'text-slate-600 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-400 hover:bg-slate-200 hover:text-emerald-600'"
                    title="New note"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </Link>
            </div>

            <ul v-if="tree.length > 0" class="space-y-0.5">
                <NotesSidebarNode
                    v-for="node in tree"
                    :key="node.path"
                    :node="node"
                    :team="team"
                    :accentColor="accentColor"
                    :depth="0"
                />
            </ul>

            <p v-else class="text-sm italic" :class="isDark ? 'text-slate-600' : 'text-slate-400'">
                Aucune note pour le moment.
            </p>
        </div>
    </aside>
</template>
