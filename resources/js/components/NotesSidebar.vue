<script setup lang="ts">
import NotesSidebarNode from '@/components/NotesSidebarNode.vue';

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
</script>

<template>
    <aside
        class="sticky top-14 h-[calc(100vh-3.5rem)] w-64 flex-shrink-0 overflow-y-auto border-r border-slate-700 bg-slate-900"
    >
        <div class="p-4">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                Notes
            </h3>

            <ul v-if="tree.length > 0" class="space-y-1">
                <NotesSidebarNode
                    v-for="node in tree"
                    :key="node.path"
                    :node="node"
                    :team="team"
                    :accentColor="accentColor"
                    :depth="0"
                />
            </ul>

            <p v-else class="text-sm italic text-slate-500">
                Aucune note pour le moment.
            </p>
        </div>
    </aside>
</template>
