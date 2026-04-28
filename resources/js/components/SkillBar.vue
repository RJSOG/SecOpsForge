<script setup lang="ts">
const props = defineProps<{
    label: string;
    level: number;
    isDark: boolean;
}>();
</script>

<template>
    <div>
        <div class="mb-1 flex justify-between font-mono text-xs">
            <span :class="isDark ? 'text-slate-300' : 'text-slate-700'">{{ label }}</span>
            <span :class="isDark ? 'text-slate-500' : 'text-slate-400'">{{ level }}%</span>
        </div>
        <div class="h-1.5 overflow-hidden rounded-full" :class="isDark ? 'bg-slate-800' : 'bg-slate-200'">
            <div
                class="h-full rounded-full transition-all duration-700"
                :class="isDark ? 'bg-emerald-500' : 'bg-emerald-500'"
                :style="{ width: `${level}%` }"
            ></div>
        </div>
    </div>
</template>
