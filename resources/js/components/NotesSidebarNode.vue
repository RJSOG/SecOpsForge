<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface NoteNode {
    name: string;
    type: 'file' | 'folder';
    path: string;
    children?: NoteNode[];
}

const props = defineProps<{
    node: NoteNode;
    team: string;
    accentColor: 'red' | 'blue';
    depth: number;
}>();

const page = usePage();
const open = ref(false);

const toggle = () => (open.value = !open.value);

const fileUrl = computed(() => `/${props.team}/${props.node.path}`);

const isActive = computed(() => {
    const url = decodeURIComponent(page.url);
    return url === fileUrl.value;
});

const accentStyles = computed(() => {
    if (props.accentColor === 'red') {
        return {
            active: 'bg-red-500/20 text-red-400',
            hover: 'hover:text-red-400 hover:bg-slate-800',
        };
    }
    return {
        active: 'bg-blue-500/20 text-blue-400',
        hover: 'hover:text-blue-400 hover:bg-slate-800',
    };
});
</script>

<template>
    <li>
        <!-- Folder -->
        <template v-if="node.type === 'folder'">
            <button
                class="flex w-full items-center rounded px-2 py-1.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
                @click="toggle"
            >
                <svg
                    :class="[
                        'mr-2 h-3.5 w-3.5 flex-shrink-0 transition-transform duration-200',
                        open ? 'rotate-90' : '',
                    ]"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M6 4l8 6-8 6V4z" clip-rule="evenodd" />
                </svg>
                {{ node.name }}
            </button>

            <ul
                v-if="open && node.children"
                class="ml-3 mt-1 space-y-1 border-l border-slate-700 pl-3"
            >
                <NotesSidebarNode
                    v-for="child in node.children"
                    :key="child.path"
                    :node="child"
                    :team="team"
                    :accentColor="accentColor"
                    :depth="depth + 1"
                />
            </ul>
        </template>

        <!-- File -->
        <template v-else>
            <Link
                :href="fileUrl"
                class="block rounded px-2 py-1.5 text-sm transition"
                :class="isActive ? accentStyles.active : `text-slate-400 ${accentStyles.hover}`"
                preserve-scroll
            >
                {{ node.name }}
            </Link>
        </template>
    </li>
</template>
