<template>
    <div class="min-h-screen transition-colors duration-200" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">
    <AppNavbar />
    <div class="min-h-[calc(100vh-3.5rem)] px-6 py-12 pt-20">
        <div class="mx-auto max-w-4xl">

            <!-- Terminal header -->
            <div class="mb-8 overflow-hidden rounded-lg border" :class="isDark ? 'border-emerald-500/30 bg-slate-950' : 'border-emerald-600/30 bg-slate-900'">
                <div class="flex items-center gap-2 border-b px-4 py-2" :class="isDark ? 'border-emerald-500/20 bg-slate-900' : 'border-emerald-600/20 bg-slate-800'">
                    <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
                    <span class="h-3 w-3 rounded-full bg-yellow-500/80"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-500/80"></span>
                    <span class="ml-2 font-mono text-xs text-slate-500">guest@secopsforge ~ $ whoami</span>
                </div>
                <div class="p-6 font-mono">
                    <div class="mb-1 text-emerald-400">
                        <span class="text-slate-500">$</span> cat /etc/profile
                    </div>
                    <div class="mt-4 space-y-1 text-sm leading-relaxed text-emerald-300/90">
                        <p><span class="font-bold text-emerald-400">RJSOG</span></p>
                        <p>Développeur · Sysadmin · Cybersécurité</p>
                    </div>
                </div>
            </div>

            <!-- Sections grid -->
            <div class="grid gap-6 md:grid-cols-2">

                <!-- Projects -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> ls ~/projects/
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <a
                            v-for="project in projects"
                            :key="project.name"
                            :href="`https://github.com/RJSOG/${project.name}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block rounded-md border-l-2 py-1 pl-4 transition"
                            :class="isDark ? 'border-emerald-500/30 hover:border-emerald-400' : 'border-emerald-600/30 hover:border-emerald-600'"
                        >
                            <div class="font-mono text-sm font-bold" :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ project.name }}</div>
                            <div class="font-mono text-xs" :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">{{ project.lang }}</div>
                            <p class="mt-1 text-sm leading-relaxed" :class="isDark ? 'text-slate-400' : 'text-slate-500'">{{ project.desc }}</p>
                        </a>
                    </div>
                </section>

                <!-- Tools -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> ls ~/toolkit/
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="tool in tools"
                            :key="tool"
                            class="rounded-md px-3 py-1 font-mono text-xs transition"
                            :class="isDark
                                ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20'
                                : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'"
                        >
                            {{ tool }}
                        </span>
                    </div>
                </section>

                <!-- Contact -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> cat .contact
                    </h2>
                    <div class="grid gap-3 font-mono text-sm sm:grid-cols-3">
                        <a href="https://github.com/RJSOG" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 rounded-md px-3 py-2 transition" :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-600'">
                            <span>GitHub</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">@RJSOG</span>
                        </a>
                        <a href="https://www.linkedin.com/in/eliott-donatien-615295181/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 rounded-md px-3 py-2 transition" :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-600'">
                            <span>LinkedIn</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">Eliott Donatien</span>
                        </a>
                    </div>
                </section>

            </div>

            <!-- Footer terminal -->
            <div class="mt-8 font-mono text-xs" :class="isDark ? 'text-slate-600' : 'text-slate-400'">
                <span class="text-emerald-500/50">$</span> echo "Last updated: 2026" <span class="animate-pulse">█</span>
            </div>

        </div>
    </div>
    </div>
</template>

<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { computed } from 'vue';
import AppNavbar from '@/components/AppNavbar.vue';

defineOptions({ layout: false });

const { isDark } = useTheme();

const cardClasses = computed(() =>
    isDark.value
        ? 'border-slate-800 bg-slate-900/50'
        : 'border-slate-200 bg-white'
);

const projects = [
    { name: 'cve-scrapper', lang: 'JavaScript', desc: 'Scraper de CVE automatisé en Node.js.' },
    { name: 'DumbCrawler', lang: 'Python', desc: "Crawler qui aspire l'intégralité d'un site web." },
    { name: 'ExternApiTester', lang: 'JavaScript', desc: "Interpréteur et testeur automatisé d'API." },
    { name: 'CustomHTTPServerThread', lang: 'Python', desc: 'Serveur HTTP multithread écrit from scratch.' },
];

const tools = [
    'Python', 'JavaScript / Node.js', 'PHP / Laravel', 'Vue', 'Java',
    'Linux', 'Docker', 'Traefik', 'Git',
];
</script>
