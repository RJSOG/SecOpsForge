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
            active: 'bg-red-500/10 text-red-400',
            hover: 'hover:text-red-400 hover:bg-slate-800/50',
        };
    }
    return {
        active: 'bg-blue-500/10 text-blue-400',
        hover: 'hover:text-blue-400 hover:bg-slate-800/50',
    };
});
</script>

<template>
    <li>
        <!-- Folder -->
        <template v-if="node.type === 'folder'">
            <button
                class="flex w-full items-center rounded-md px-2 py-1.5 text-sm text-slate-400 transition-colors duration-150 hover:bg-slate-800/50 hover:text-slate-200"
                @click="toggle"
            >
                <svg
                    :class="[
                        'mr-2 h-3 w-3 flex-shrink-0 text-slate-600 transition-transform duration-200',
                        open ? 'rotate-90' : '',
                    ]"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M6 4l8 6-8 6V4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ node.name }}</span>
            </button>

            <ul
                v-if="open && node.children"
                class="ml-3 mt-0.5 space-y-0.5 border-l border-slate-800 pl-3"
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
                class="block rounded-md px-2 py-1.5 text-sm transition-colors duration-150"
                :class="isActive ? accentStyles.active : `text-slate-500 ${accentStyles.hover}`"
                preserve-scroll
            >
                {{ node.name }}
            </Link>
        </template>
    </li>
</template>
