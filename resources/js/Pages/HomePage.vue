<template>
    <div class="flex min-h-[calc(100vh-3.5rem)] flex-col items-center justify-center px-6">

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
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
                Red Team
            </Link>
            <Link
                href="/blueteam"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20 hover:bg-blue-500/20'
                    : 'bg-blue-50 text-blue-600 ring-1 ring-blue-200 hover:bg-blue-100'"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                Blue Team
            </Link>
            <Link
                href="/automation"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 hover:bg-amber-500/20'
                    : 'bg-amber-50 text-amber-600 ring-1 ring-amber-200 hover:bg-amber-100'"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                </svg>
                Automation
            </Link>
            <Link
                href="/whoami"
                class="group flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition"
                :class="isDark
                    ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 hover:bg-emerald-500/20'
                    : 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200 hover:bg-emerald-100'"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Whoami
            </Link>
        </div>

        <!-- Subtle footer -->
        <div class="mt-16 font-mono text-xs" :class="isDark ? 'text-slate-700' : 'text-slate-300'">
            &lt;/&gt; with ☕ by RJSOG
        </div>

    </div>
</template>

<script setup lang="ts">
import LogoIcon from '@/components/LogoIcon.vue';
import TerminalAnimation from '@/components/TerminalAnimation.vue';
import { useTheme } from '@/composables/useTheme';
import { Link } from '@inertiajs/vue3';

const { isDark } = useTheme();
</script>
