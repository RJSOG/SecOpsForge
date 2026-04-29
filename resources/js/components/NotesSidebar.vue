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
}>();

const { isDark } = useTheme();
const page = usePage();
const isAuthenticated = !!page.props.auth?.user;
</script>

<template>
    <aside
        class="sticky top-14 h-[calc(100vh-3.5rem)] w-64 flex-shrink-0 overflow-y-auto border-r transition-colors duration-200"
        :class="isDark ? 'border-slate-800 bg-slate-950' : 'border-slate-200 bg-slate-50'"
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
