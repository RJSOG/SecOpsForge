<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
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
    accentColor: 'red' | 'blue' | 'amber';
    depth: number;
}>();

const page = usePage();
const { isDark } = useTheme();
const open = ref(false);

const toggle = () => (open.value = !open.value);

const fileUrl = computed(() => `/${props.team}/${props.node.path}`);

const isActive = computed(() => {
    return decodeURIComponent(page.url) === fileUrl.value;
});

const colorMap = {
    red: { active: 'bg-red-500/10 text-red-500', darkHover: 'hover:text-red-400', lightHover: 'hover:text-red-600' },
    blue: { active: 'bg-blue-500/10 text-blue-500', darkHover: 'hover:text-blue-400', lightHover: 'hover:text-blue-600' },
    amber: { active: 'bg-amber-500/10 text-amber-500', darkHover: 'hover:text-amber-400', lightHover: 'hover:text-amber-600' },
};

const fileClasses = computed(() => {
    const c = colorMap[props.accentColor];
    if (isActive.value) return c.active;
    if (isDark.value) return `text-slate-500 ${c.darkHover} hover:bg-slate-800/50`;
    return `text-slate-500 ${c.lightHover} hover:bg-slate-100`;
});

const folderClasses = computed(() => {
    if (isDark.value) {
        return 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200';
    }
    return 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
});

const borderClass = computed(() => isDark.value ? 'border-slate-800' : 'border-slate-200');
</script>

<template>
    <li>
        <!-- Folder -->
        <template v-if="node.type === 'folder'">
            <button
                class="flex w-full items-center rounded-md px-2 py-1.5 text-sm transition-colors duration-150"
                :class="folderClasses"
                @click="toggle"
            >
                <svg
                    :class="[
                        'mr-2 h-3 w-3 flex-shrink-0 transition-transform duration-200',
                        isDark ? 'text-slate-600' : 'text-slate-400',
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
                class="ml-3 mt-0.5 space-y-0.5 border-l pl-3"
                :class="borderClass"
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
                :class="fileClasses"
                preserve-scroll
            >
                {{ node.name }}
            </Link>
        </template>
    </li>
</template>
