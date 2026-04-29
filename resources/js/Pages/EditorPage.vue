<template>
    <div class="min-h-screen transition-colors duration-200" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">
        <AppNavbar />

        <div class="mx-auto max-w-6xl px-6 pt-20">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="font-mono text-lg font-bold" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                    {{ isNew ? '> new note' : '> edit note' }}
                </h1>
                <div class="flex items-center gap-3">
                    <button
                        v-if="!isNew"
                        @click="deleteNote"
                        class="rounded-md px-3 py-1.5 text-xs font-medium transition"
                        :class="isDark
                            ? 'text-red-400 hover:bg-red-500/10'
                            : 'text-red-600 hover:bg-red-50'"
                    >
                        Delete
                    </button>
                    <button
                        @click="saveNote"
                        :disabled="saving"
                        class="rounded-md px-4 py-1.5 text-xs font-medium transition"
                        :class="isDark
                            ? 'bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/30 hover:bg-emerald-500/30'
                            : 'bg-emerald-500 text-white hover:bg-emerald-600'"
                    >
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>

            <!-- Meta -->
            <div class="mb-4 flex gap-4">
                <div>
                    <label class="mb-1 block text-xs font-medium" :class="isDark ? 'text-slate-500' : 'text-slate-400'">Section</label>
                    <select
                        v-model="selectedTeam"
                        class="rounded-md border px-3 py-1.5 text-sm"
                        :class="isDark
                            ? 'border-slate-700 bg-slate-800 text-slate-300'
                            : 'border-slate-200 bg-white text-slate-700'"
                    >
                        <option v-for="t in teams" :key="t" :value="t">{{ teamLabels[t] }}</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium" :class="isDark ? 'text-slate-500' : 'text-slate-400'">
                        Path <span class="text-slate-600">(ex: Folder/note-name)</span>
                    </label>
                    <input
                        v-model="notePath"
                        type="text"
                        placeholder="Folder/note-name"
                        class="w-full rounded-md border px-3 py-1.5 font-mono text-sm"
                        :class="isDark
                            ? 'border-slate-700 bg-slate-800 text-slate-300 placeholder-slate-600'
                            : 'border-slate-200 bg-white text-slate-700 placeholder-slate-400'"
                    />
                </div>
            </div>

            <!-- Editor + Preview -->
            <div class="grid h-[calc(100vh-14rem)] gap-4" :class="showPreview ? 'grid-cols-2' : 'grid-cols-1'">
                <!-- Textarea -->
                <div class="flex flex-col">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-medium" :class="isDark ? 'text-slate-500' : 'text-slate-400'">Markdown</span>
                        <button
                            @click="showPreview = !showPreview"
                            class="text-xs transition"
                            :class="isDark ? 'text-slate-500 hover:text-emerald-400' : 'text-slate-400 hover:text-emerald-600'"
                        >
                            {{ showPreview ? 'Hide preview' : 'Show preview' }}
                        </button>
                    </div>
                    <textarea
                        v-model="content"
                        class="flex-1 resize-none rounded-lg border p-4 font-mono text-sm leading-relaxed focus:outline-none focus:ring-1"
                        :class="isDark
                            ? 'border-slate-700 bg-slate-950 text-slate-300 focus:ring-emerald-500/30'
                            : 'border-slate-200 bg-slate-50 text-slate-800 focus:ring-emerald-500/30'"
                        spellcheck="false"
                        @input="onInput"
                    ></textarea>
                </div>

                <!-- Preview -->
                <div v-if="showPreview" class="flex flex-col">
                    <span class="mb-2 text-xs font-medium" :class="isDark ? 'text-slate-500' : 'text-slate-400'">Preview</span>
                    <div
                        class="flex-1 overflow-y-auto rounded-lg border p-6"
                        :class="isDark ? 'border-slate-700 bg-slate-950' : 'border-slate-200 bg-white'"
                    >
                        <article
                            v-if="isDark"
                            class="prose prose-invert max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-slate-100 prose-p:text-slate-300 prose-a:text-blue-400 prose-code:rounded prose-code:bg-slate-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:text-slate-300 prose-pre:border prose-pre:border-slate-800 prose-pre:bg-slate-900"
                            v-html="previewHtml"
                        />
                        <article
                            v-else
                            class="prose max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-slate-900 prose-p:text-slate-600 prose-code:rounded prose-code:bg-slate-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:text-slate-700 prose-pre:border prose-pre:border-slate-200 prose-pre:bg-slate-50"
                            v-html="previewHtml"
                        />
                    </div>
                </div>
            </div>

            <!-- Keyboard shortcuts -->
            <div class="mt-3 text-xs" :class="isDark ? 'text-slate-700' : 'text-slate-300'">
                <kbd class="rounded border px-1" :class="isDark ? 'border-slate-700' : 'border-slate-300'">Ctrl+S</kbd> save
                · <kbd class="rounded border px-1" :class="isDark ? 'border-slate-700' : 'border-slate-300'">Ctrl+P</kbd> toggle preview
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import { useHighlight } from '@/composables/useHighlight';
import { useTheme } from '@/composables/useTheme';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

defineOptions({ layout: false });

const props = defineProps<{
    team: string;
    path: string;
    content: string;
    isNew: boolean;
    teams: string[];
}>();

const { isDark } = useTheme();
const { highlightAll } = useHighlight();

const teamLabels: Record<string, string> = {
    redteam: 'Red Team',
    blueteam: 'Blue Team',
    automation: 'Automation',
};

const selectedTeam = ref(props.team);
const notePath = ref(props.path);
const content = ref(props.content);
const previewHtml = ref('');
const showPreview = ref(true);
const saving = ref(false);

let previewTimer: ReturnType<typeof setTimeout>;

function onInput() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(updatePreview, 400);
}

async function updatePreview() {
    if (!content.value) {
        previewHtml.value = '';
        return;
    }
    try {
        const { data } = await axios.post('/api/editor/preview', { content: content.value });
        previewHtml.value = data.html;
        nextTick(() => highlightAll());
    } catch {
        // silent
    }
}

async function saveNote() {
    if (!notePath.value.trim()) {
        alert('Please enter a path for the note');
        return;
    }

    saving.value = true;
    try {
        const { data } = await axios.post('/api/editor/save', {
            team: selectedTeam.value,
            path: notePath.value,
            content: content.value,
        });
        if (data.success) {
            router.visit(data.url);
        }
    } catch (err: any) {
        alert(err.response?.data?.error || 'Failed to save');
    }
    saving.value = false;
}

async function deleteNote() {
    if (!confirm('Delete this note?')) return;

    try {
        await axios.post('/api/editor/delete', {
            team: selectedTeam.value,
            path: notePath.value,
        });
        router.visit('/' + selectedTeam.value);
    } catch (err: any) {
        alert(err.response?.data?.error || 'Failed to delete');
    }
}

function onKeydown(e: KeyboardEvent) {
    if ((e.metaKey || e.ctrlKey) && e.key === 's') {
        e.preventDefault();
        saveNote();
    }
    if ((e.metaKey || e.ctrlKey) && e.key === 'p') {
        e.preventDefault();
        showPreview.value = !showPreview.value;
    }
}

onMounted(() => {
    document.addEventListener('keydown', onKeydown);
    if (content.value) updatePreview();
});
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>
