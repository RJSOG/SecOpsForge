<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface NoteNode {
    name: string;
    type: 'file' | 'folder';
    path: string;
    children?: NoteNode[];
}

const props = defineProps<{
    tree: NoteNode[];
    team: string;
    accentColor: 'red' | 'blue';
}>();

const page = usePage();

const accentClasses = {
    red: {
        active: 'bg-red-500/20 text-red-400',
        hover: 'hover:text-red-400',
        folder: 'text-red-300',
    },
    blue: {
        active: 'bg-blue-500/20 text-blue-400',
        hover: 'hover:text-blue-400',
        folder: 'text-blue-300',
    },
};

const accent = accentClasses[props.accentColor];

// Track open folders
const openFolders = ref<Set<string>>(new Set());

function toggleFolder(path: string) {
    if (openFolders.value.has(path)) {
        openFolders.value.delete(path);
    } else {
        openFolders.value.add(path);
    }
}

function isActive(nodePath: string): boolean {
    const currentUrl = page.url;
    const noteUrl = `/${props.team}/${nodePath}`;
    return currentUrl === noteUrl || currentUrl === noteUrl.replace('.md', '');
}
</script>

<template>
    <aside
        class="fixed left-0 top-0 z-40 h-screen w-64 overflow-y-auto border-r border-slate-700 bg-slate-900 pt-16"
    >
        <div class="p-4">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                Notes
            </h3>

            <ul v-if="tree.length > 0" class="space-y-1">
                <template v-for="node in tree" :key="node.path">
                    <!-- Folder -->
                    <li v-if="node.type === 'folder'">
                        <button
                            class="flex w-full items-center rounded px-2 py-1.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
                            @click="toggleFolder(node.path)"
                        >
                            <svg
                                :class="[
                                    'mr-2 h-3.5 w-3.5 transition-transform duration-200',
                                    openFolders.has(node.path) ? 'rotate-90' : '',
                                ]"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 4l8 6-8 6V4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ node.name }}
                        </button>

                        <ul
                            v-if="openFolders.has(node.path) && node.children"
                            class="ml-3 mt-1 space-y-1 border-l border-slate-700 pl-3"
                        >
                            <li v-for="child in node.children" :key="child.path">
                                <Link
                                    v-if="child.type === 'file'"
                                    :href="`/${team}/${child.path}`"
                                    class="block rounded px-2 py-1 text-sm transition"
                                    :class="
                                        isActive(child.path)
                                            ? accent.active
                                            : `text-slate-400 ${accent.hover} hover:bg-slate-800`
                                    "
                                >
                                    {{ child.name }}
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- File at root -->
                    <li v-else>
                        <Link
                            :href="`/${team}/${node.path}`"
                            class="block rounded px-2 py-1.5 text-sm transition"
                            :class="
                                isActive(node.path)
                                    ? accent.active
                                    : `text-slate-400 ${accent.hover} hover:bg-slate-800`
                            "
                        >
                            {{ node.name }}
                        </Link>
                    </li>
                </template>
            </ul>

            <p v-else class="text-sm text-slate-500 italic">
                Aucune note pour le moment.
            </p>
        </div>
    </aside>
</template>
