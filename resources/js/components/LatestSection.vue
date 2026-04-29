<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    title: string;
    notes: { title: string; url: string; date: string; team: string }[];
    isDark: boolean;
    colorClass: 'red' | 'blue' | 'amber';
}>();

const colors = {
    red: {
        title: 'text-red-500',
        badge: 'bg-red-500/10 ring-1 ring-red-500/20',
        hover: {
            dark: 'hover:border-red-500/30',
            light: 'hover:border-red-300',
        },
    },
    blue: {
        title: 'text-blue-500',
        badge: 'bg-blue-500/10 ring-1 ring-blue-500/20',
        hover: {
            dark: 'hover:border-blue-500/30',
            light: 'hover:border-blue-300',
        },
    },
    amber: {
        title: 'text-amber-500',
        badge: 'bg-amber-500/10 ring-1 ring-amber-500/20',
        hover: {
            dark: 'hover:border-amber-500/30',
            light: 'hover:border-amber-300',
        },
    },
};

const color = colors[props.colorClass];
</script>

<template>
    <div>
        <h3 class="mb-3 font-mono text-xs font-semibold uppercase tracking-wider" :class="color.title">
            {{ title }}
        </h3>
        <div class="space-y-2">
            <Link
                v-for="note in notes"
                :key="note.url"
                :href="note.url"
                class="block rounded-lg border p-3 transition"
                :class="isDark
                    ? `border-slate-800 bg-slate-900/50 ${color.hover.dark}`
                    : `border-slate-200 bg-white ${color.hover.light}`"
            >
                <div class="text-sm font-medium" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                    {{ note.title }}
                </div>
                <div class="mt-1 text-xs" :class="isDark ? 'text-slate-600' : 'text-slate-400'">
                    {{ note.date }}
                </div>
            </Link>
        </div>
    </div>
</template>
