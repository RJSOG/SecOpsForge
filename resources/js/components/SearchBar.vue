<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, onUnmounted, ref, watch } from 'vue';

const { isDark } = useTheme();

const query = ref('');
const results = ref<any[]>([]);
const isOpen = ref(false);
const isLoading = ref(false);
const inputRef = ref<HTMLInputElement>();

let debounceTimer: ReturnType<typeof setTimeout>;

const teamLabels: Record<string, string> = {
    redteam: 'Red Team',
    blueteam: 'Blue Team',
    automation: 'Automation',
};

const teamColors: Record<string, string> = {
    redteam: 'text-red-400',
    blueteam: 'text-blue-400',
    automation: 'text-amber-400',
};

watch(query, (val) => {
    clearTimeout(debounceTimer);
    if (val.length < 2) {
        results.value = [];
        isOpen.value = false;
        return;
    }
    isLoading.value = true;
    debounceTimer = setTimeout(async () => {
        try {
            const { data } = await axios.get('/api/search', { params: { q: val } });
            results.value = data.results;
            isOpen.value = data.results.length > 0;
        } catch {
            results.value = [];
        }
        isLoading.value = false;
    }, 250);
});

function close() {
    setTimeout(() => {
        isOpen.value = false;
    }, 200);
}

function onKeydown(e: KeyboardEvent) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        inputRef.value?.focus();
    }
    if (e.key === 'Escape') {
        isOpen.value = false;
        inputRef.value?.blur();
    }
}

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>

<template>
    <div class="relative">
        <div class="relative">
            <svg class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
                ref="inputRef"
                v-model="query"
                type="text"
                placeholder="Search... ⌘K"
                class="w-44 rounded-md border py-1.5 pl-8 pr-3 text-xs font-medium transition-all focus:w-64 focus:outline-none focus:ring-1"
                :class="isDark
                    ? 'border-slate-700 bg-slate-800/50 text-slate-300 placeholder-slate-500 focus:border-emerald-500/50 focus:ring-emerald-500/30'
                    : 'border-slate-200 bg-slate-50 text-slate-700 placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500/30'"
                @focus="query.length >= 2 && results.length > 0 && (isOpen = true)"
                @blur="close"
            />
        </div>

        <!-- Dropdown -->
        <div
            v-if="isOpen"
            class="absolute right-0 top-full z-50 mt-2 w-96 overflow-hidden rounded-lg border shadow-xl"
            :class="isDark ? 'border-slate-700 bg-slate-900' : 'border-slate-200 bg-white'"
        >
            <div class="max-h-80 overflow-y-auto">
                <Link
                    v-for="result in results"
                    :key="result.url"
                    :href="result.url"
                    class="block border-b px-4 py-3 transition last:border-0"
                    :class="isDark
                        ? 'border-slate-800 hover:bg-slate-800'
                        : 'border-slate-100 hover:bg-slate-50'"
                    @click="isOpen = false; query = ''"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium" :class="isDark ? 'text-slate-200' : 'text-slate-800'">
                            {{ result.title }}
                        </span>
                        <span class="text-[10px] font-semibold uppercase" :class="teamColors[result.team]">
                            {{ teamLabels[result.team] }}
                        </span>
                    </div>
                    <p v-if="result.snippet" class="mt-1 line-clamp-2 text-xs" :class="isDark ? 'text-slate-500' : 'text-slate-400'">
                        {{ result.snippet }}
                    </p>
                </Link>
            </div>
        </div>
    </div>
</template>
