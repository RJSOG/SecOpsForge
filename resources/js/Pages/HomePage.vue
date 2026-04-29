<template>
    <div class="flex min-h-[calc(100vh-3.5rem)] flex-col items-center px-6 pt-16">

        <!-- Logo + Title -->
        <div class="mb-10 text-center">
            <LogoIcon sizeClass="mx-auto mb-6 h-20 w-20" />
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl" :class="isDark ? 'text-white' : 'text-slate-900'">
                Sec<span :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">Ops</span>Forge
            </h1>
            <p class="mt-3 font-mono text-sm" :class="isDark ? 'text-slate-500' : 'text-slate-400'">
                Cybersecurity knowledge base — built by operators, for operators.
            </p>
        </div>

        <!-- Terminal Animation -->
        <div class="w-full max-w-2xl">
            <TerminalAnimation />
        </div>

        <!-- Action buttons -->
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <Link
                href="/redteam"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20 hover:bg-red-500/20'
                    : 'bg-red-50 text-red-600 ring-1 ring-red-200 hover:bg-red-100'"
            >
                Red Team
            </Link>
            <Link
                href="/blueteam"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20 hover:bg-blue-500/20'
                    : 'bg-blue-50 text-blue-600 ring-1 ring-blue-200 hover:bg-blue-100'"
            >
                Blue Team
            </Link>
            <Link
                href="/automation"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 hover:bg-amber-500/20'
                    : 'bg-amber-50 text-amber-600 ring-1 ring-amber-200 hover:bg-amber-100'"
            >
                Automation
            </Link>
            <Link
                href="/whoami"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 hover:bg-emerald-500/20'
                    : 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200 hover:bg-emerald-100'"
            >
                Whoami
            </Link>
        </div>

        <!-- Latest notes sections -->
        <div v-if="hasLatest" class="mt-16 w-full max-w-4xl">
            <h2 class="mb-6 text-center font-mono text-sm font-bold uppercase tracking-widest" :class="isDark ? 'text-slate-600' : 'text-slate-400'">
                > ls --latest
            </h2>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Red Team latest -->
                <LatestSection
                    v-if="latest.redteam?.length"
                    title="Red Team"
                    :notes="latest.redteam"
                    :isDark="isDark"
                    colorClass="red"
                />

                <!-- Blue Team latest -->
                <LatestSection
                    v-if="latest.blueteam?.length"
                    title="Blue Team"
                    :notes="latest.blueteam"
                    :isDark="isDark"
                    colorClass="blue"
                />

                <!-- Automation latest -->
                <LatestSection
                    v-if="latest.automation?.length"
                    title="Automation"
                    :notes="latest.automation"
                    :isDark="isDark"
                    colorClass="amber"
                />
            </div>
        </div>

        <!-- Subtle footer -->
        <div class="mb-8 mt-16 font-mono text-xs" :class="isDark ? 'text-slate-700' : 'text-slate-300'">
            &lt;/&gt; with ☕ by RJSOG
        </div>

    </div>
</template>

<script setup lang="ts">
import LatestSection from '@/components/LatestSection.vue';
import LogoIcon from '@/components/LogoIcon.vue';
import TerminalAnimation from '@/components/TerminalAnimation.vue';
import { useTheme } from '@/composables/useTheme';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { isDark } = useTheme();

const props = defineProps<{
    latest: Record<string, any[]>;
}>();

const hasLatest = computed(() => {
    return Object.values(props.latest).some(arr => arr && arr.length > 0);
});
</script>
