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
                        <p>┌─────────────────────────────────────┐</p>
                        <p>│ <span class="font-bold text-emerald-400">RJSOG</span> — Security Analyst            │</p>
                        <p>│ Blue Team · Red Team · DevSecOps     │</p>
                        <p>└─────────────────────────────────────┘</p>
                    </div>
                </div>
            </div>

            <!-- Sections grid -->
            <div class="grid gap-6 md:grid-cols-2">

                <!-- Skills -->
                <section class="rounded-lg border p-6" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> skills --list
                    </h2>
                    <div class="space-y-3">
                        <SkillBar label="Network Security" :level="85" :isDark="isDark" />
                        <SkillBar label="Penetration Testing" :level="75" :isDark="isDark" />
                        <SkillBar label="SIEM / Log Analysis" :level="80" :isDark="isDark" />
                        <SkillBar label="Linux Administration" :level="85" :isDark="isDark" />
                        <SkillBar label="Python / Scripting" :level="70" :isDark="isDark" />
                        <SkillBar label="Web Security" :level="75" :isDark="isDark" />
                    </div>
                </section>

                <!-- Certifications -->
                <section class="rounded-lg border p-6" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> cat certs.log
                    </h2>
                    <ul class="space-y-3 font-mono text-sm">
                        <li v-for="cert in certs" :key="cert.name" class="flex items-start gap-3">
                            <span class="mt-0.5 text-emerald-500">✓</span>
                            <div>
                                <div :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ cert.name }}</div>
                                <div :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-xs">{{ cert.org }} · {{ cert.year }}</div>
                            </div>
                        </li>
                    </ul>
                </section>

                <!-- Experience -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> history | grep work
                    </h2>
                    <div class="space-y-6">
                        <div v-for="job in experience" :key="job.title" class="relative border-l-2 pl-5" :class="isDark ? 'border-emerald-500/30' : 'border-emerald-600/30'">
                            <div class="absolute -left-[5px] top-1 h-2 w-2 rounded-full" :class="isDark ? 'bg-emerald-500' : 'bg-emerald-600'"></div>
                            <div class="font-mono text-sm font-bold" :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ job.title }}</div>
                            <div class="font-mono text-xs" :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">{{ job.company }} · {{ job.period }}</div>
                            <p class="mt-2 text-sm leading-relaxed" :class="isDark ? 'text-slate-400' : 'text-slate-500'">{{ job.desc }}</p>
                        </div>
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
                        <a href="https://github.com/RJSOG" target="_blank" class="flex items-center gap-2 rounded-md px-3 py-2 transition" :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-600'">
                            <span>GitHub</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">@RJSOG</span>
                        </a>
                        <a href="https://linkedin.com/in/" target="_blank" class="flex items-center gap-2 rounded-md px-3 py-2 transition" :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-600'">
                            <span>LinkedIn</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">Eliott</span>
                        </a>
                        <div class="flex items-center gap-2 rounded-md px-3 py-2" :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                            <span>Email</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">contact@secopsforge.com</span>
                        </div>
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
import SkillBar from '@/components/SkillBar.vue';

defineOptions({ layout: false });

const { isDark } = useTheme();

const cardClasses = computed(() =>
    isDark.value
        ? 'border-slate-800 bg-slate-900/50'
        : 'border-slate-200 bg-white'
);

// TODO: Replace with your actual data
const certs = [
    { name: 'CompTIA Security+', org: 'CompTIA', year: '2025' },
    { name: 'CEH', org: 'EC-Council', year: '2025' },
];

const experience = [
    {
        title: 'Security Analyst',
        company: 'Company Name',
        period: '2024 — Present',
        desc: 'Blue team operations, SIEM monitoring, incident response, and vulnerability management.',
    },
    {
        title: 'Junior Pentester',
        company: 'Company Name',
        period: '2023 — 2024',
        desc: 'Web application security assessments, network penetration testing, and reporting.',
    },
];

const tools = [
    'Nmap', 'Burp Suite', 'Wireshark', 'Metasploit', 'Splunk',
    'Wazuh', 'Nessus', 'Kali Linux', 'Docker', 'Git',
    'Python', 'Bash', 'PowerShell', 'Terraform',
];
</script>
