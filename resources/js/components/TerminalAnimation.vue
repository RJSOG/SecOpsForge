<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { onMounted, ref } from 'vue';

const { isDark } = useTheme();

interface TermLine {
    type: 'command' | 'output' | 'blank';
    text: string;
    delay?: number;
}

const lines: TermLine[] = [
    { type: 'command', text: 'whoami', delay: 800 },
    { type: 'output', text: 'security-analyst@secopsforge', delay: 300 },
    { type: 'blank', text: '', delay: 400 },
    { type: 'command', text: 'cat /etc/motd', delay: 700 },
    { type: 'output', text: '╔══════════════════════════════════════╗', delay: 50 },
    { type: 'output', text: '║  Welcome to SecOpsForge             ║', delay: 50 },
    { type: 'output', text: '║  Cybersecurity Knowledge Base        ║', delay: 50 },
    { type: 'output', text: '║  Red Team · Blue Team · DevSecOps    ║', delay: 50 },
    { type: 'output', text: '╚══════════════════════════════════════╝', delay: 300 },
    { type: 'blank', text: '', delay: 400 },
    { type: 'command', text: 'ls ~/notes/', delay: 600 },
    { type: 'output', text: 'redteam/    blueteam/    automation/', delay: 300 },
    { type: 'blank', text: '', delay: 400 },
    { type: 'command', text: 'echo "Ready to learn?"', delay: 500 },
    { type: 'output', text: 'Ready to learn?', delay: 200 },
];

const visibleLines = ref<{ type: string; text: string; typed: string }[]>([]);
const currentTyping = ref('');
const showCursor = ref(true);
const animationDone = ref(false);

function sleep(ms: number): Promise<void> {
    return new Promise((r) => setTimeout(r, ms));
}

async function typeText(text: string, speed = 40): Promise<string> {
    let result = '';
    for (const char of text) {
        result += char;
        currentTyping.value = result;
        await sleep(speed + Math.random() * 30);
    }
    return result;
}

async function runAnimation() {
    await sleep(600);

    for (const line of lines) {
        if (line.type === 'command') {
            // Show prompt, then type command
            visibleLines.value.push({ type: 'command', text: line.text, typed: '' });
            const idx = visibleLines.value.length - 1;

            for (const char of line.text) {
                visibleLines.value[idx].typed += char;
                await sleep(45 + Math.random() * 35);
            }

            await sleep(line.delay || 300);
        } else if (line.type === 'output') {
            visibleLines.value.push({ type: 'output', text: line.text, typed: line.text });
            await sleep(line.delay || 100);
        } else {
            visibleLines.value.push({ type: 'blank', text: '', typed: '' });
            await sleep(line.delay || 200);
        }
    }

    animationDone.value = true;
}

onMounted(() => {
    runAnimation();
});
</script>

<template>
    <div
        class="overflow-hidden rounded-lg border shadow-2xl"
        :class="isDark
            ? 'border-slate-700/50 bg-slate-950 shadow-emerald-500/5'
            : 'border-slate-300 bg-slate-900 shadow-slate-400/10'"
    >
        <!-- Title bar -->
        <div class="flex items-center gap-2 border-b px-4 py-2.5" :class="isDark ? 'border-slate-800 bg-slate-900' : 'border-slate-700 bg-slate-800'">
            <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
            <span class="h-3 w-3 rounded-full bg-yellow-500/80"></span>
            <span class="h-3 w-3 rounded-full bg-emerald-500/80"></span>
            <span class="ml-3 font-mono text-xs text-slate-500">guest@secopsforge:~</span>
        </div>

        <!-- Terminal body -->
        <div class="min-h-[320px] p-5 font-mono text-sm leading-relaxed">
            <div v-for="(line, i) in visibleLines" :key="i" class="whitespace-pre">
                <template v-if="line.type === 'command'">
                    <span class="text-emerald-500">❯</span>
                    <span class="text-slate-500"> ~ </span>
                    <span class="text-emerald-300">{{ line.typed }}</span>
                    <span v-if="i === visibleLines.length - 1 && !animationDone" class="inline-block w-2 animate-pulse bg-emerald-400 text-transparent">_</span>
                </template>
                <template v-else-if="line.type === 'output'">
                    <span class="text-slate-400">{{ line.typed }}</span>
                </template>
                <template v-else>
                    &nbsp;
                </template>
            </div>

            <!-- Final cursor -->
            <div v-if="animationDone" class="mt-1">
                <span class="text-emerald-500">❯</span>
                <span class="text-slate-500"> ~ </span>
                <span class="inline-block h-4 w-2 animate-pulse bg-emerald-400"></span>
            </div>
        </div>
    </div>
</template>
