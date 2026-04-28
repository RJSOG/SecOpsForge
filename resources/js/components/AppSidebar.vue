<script setup lang="ts">
import FileTreeNode from '@/components/FileTreeNode.vue';
import { FileTree } from '@/composables/fileTree';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const page = usePage();
const path = ref(page.url.replace(/^\//, ''));
const tree = ref<any[]>([]);

onMounted(() => {
    const storedTree = localStorage.getItem(path.value);

    if (storedTree) {
        tree.value = JSON.parse(storedTree).tree;
    } else {
        const fileTree = FileTree.instance;
        fileTree.updateInstance(path, tree);
        fileTree.fetch(path.value, 'md');
    }
});

watch(
    () => page.url,
    async (newPath) => {
        const fileTree = FileTree.instance;
        path.value = newPath.replace(/^\//, '');

        try {
            const storedTree = localStorage.getItem(path.value);
            const isUpToDate = await fileTree.isUpToDate(
                path.value,
                storedTree,
            );

            if (storedTree && isUpToDate) {
                tree.value = JSON.parse(storedTree).tree;
            } else {
                await fileTree.fetch(path.value, 'md');
            }
        } catch (e) {
            console.error(e);
        }
    },
);
</script>

<template>
    <aside
        class="fixed left-0 top-0 min-h-screen w-64 overflow-y-auto rounded-[1vw] border-r border-r-slate-700 bg-slate-800 pt-14 text-gray-200"
    >
        <div class="p-4">
            <ul class="space-y-2">
                <FileTreeNode
                    id="pageSidebar"
                    v-for="item in tree"
                    :key="item.path || item.name"
                    :node="item"
                />
            </ul>
        </div>
    </aside>
</template>
