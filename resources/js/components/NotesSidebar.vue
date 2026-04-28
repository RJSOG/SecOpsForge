<script setup lang="ts">
import NotesSidebarNode from '@/components/NotesSidebarNode.vue';
import { useTheme } from '@/composables/useTheme';

interface NoteNode {
    name: string;
    type: 'file' | 'folder';
    path: string;
    children?: NoteNode[];
}

defineProps<{
    tree: NoteNode[];
    team: string;
    accentColor: 'red' | 'blue';
}>();

const { isDark } = useTheme();
</script>

<template>
    <aside
        class="sticky top-14 h-[calc(100vh-3.5rem)] w-64 flex-shrink-0 overflow-y-auto border-r transition-colors duration-200"
        :class="isDark ? 'border-slate-800 bg-slate-950' : 'border-slate-200 bg-slate-50'"
    >
        <div class="p-4">
            <h3
                class="mb-4 text-[11px] font-semibold uppercase tracking-widest"
                :class="isDark ? 'text-slate-600' : 'text-slate-400'"
            >
                Notes
            </h3>

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
