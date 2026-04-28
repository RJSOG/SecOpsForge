<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

interface NodeProps {
    name: string;
    type: string;
    path: string;
    children?: NodeProps[];
    isParent?: boolean;
}

const props = defineProps<{ node: NodeProps }>();

const open = ref(false);
const toggle = () => (open.value = !open.value);
</script>

<template>
    <li class="ml-2">
        <div
            v-if="props.node.type === 'folder' && props.node.name !== 'assets'"
            class="flex cursor-pointer select-none items-center font-semibold hover:text-blue-400"
            :class="props.node.isParent ? 'text-slate-200' : 'text-slate-400'"
            @click="toggle"
        >
            <svg
                :class="[
                    'mr-1 h-4 w-4 transition-transform duration-200',
                    open ? 'rotate-90' : 'rotate-0',
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
            {{ props.node.name }}
        </div>

        <ul
            v-if="props.node.type === 'folder' && open"
            class="mt-1 space-y-1 border-l-2 border-gray-700 pl-3"
        >
            <FileTreeNode
                v-for="child in props.node.children"
                :key="child.name + child.type"
                :node="child"
            />
        </ul>

        <div
            v-if="props.node.type === 'file'"
            class="ml-6 flex items-center py-1"
        >
            <Link
                :href="`/page${props.node.path}`"
                class="block text-sm text-slate-500 hover:text-blue-400"
            >
                {{ props.node.name }}
            </Link>
        </div>
    </li>
</template>
