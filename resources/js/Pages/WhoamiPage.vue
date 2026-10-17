<template>
    <div class="min-h-screen transition-colors duration-200" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">
    <AppNavbar />
    <div class="min-h-[calc(100vh-3.5rem)] px-4 pb-12 pt-20 sm:px-6">
        <div class="mx-auto max-w-4xl">

            <!-- Terminal header -->
            <div class="mb-8 overflow-hidden rounded-lg border" :class="isDark ? 'border-emerald-500/30 bg-slate-950' : 'border-emerald-600/30 bg-slate-900'">
                <div class="flex items-center gap-2 border-b px-4 py-2" :class="isDark ? 'border-emerald-500/20 bg-slate-900' : 'border-emerald-600/20 bg-slate-800'">
                    <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
                    <span class="h-3 w-3 rounded-full bg-yellow-500/80"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-500/80"></span>
                    <span class="ml-2 font-mono text-xs text-slate-500">guest@secopsforge ~ $ whoami</span>
                </div>
                <div class="p-4 font-mono sm:p-6">
                    <div class="mb-1 text-emerald-400">
                        <span class="text-slate-500">$</span> cat /etc/profile
                    </div>
                    <div class="mt-4 space-y-1 text-sm leading-relaxed text-emerald-300/90">
                        <p><span class="font-bold text-emerald-400">Eliott Donatien</span> <span class="text-slate-500">aka</span> RJSOG</p>
                        <p>Cybersecurity Engineer · SOC · Pentest · Dev</p>
                        <p class="text-slate-400">
                            <span class="text-slate-500">location:</span> Singapore
                            <span class="text-slate-500">· status:</span> Extended SOC Analyst @ Orange Cyberdefense (VIE)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sections grid -->
            <div class="grid gap-6 md:grid-cols-2 [&>section]:min-w-0">

                <!-- Experience -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> history | grep work
                    </h2>
                    <div class="space-y-6">
                        <div v-for="job in experience" :key="job.title + job.period" class="relative border-l-2 pl-5" :class="isDark ? 'border-emerald-500/30' : 'border-emerald-600/30'">
                            <div class="absolute -left-[5px] top-1 h-2 w-2 rounded-full" :class="isDark ? 'bg-emerald-500' : 'bg-emerald-600'"></div>
                            <div class="font-mono text-sm font-bold" :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ job.title }}</div>
                            <div class="font-mono text-xs" :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">{{ job.company }} · {{ job.location }} · {{ job.period }}</div>
                            <ul class="mt-2 space-y-1 text-sm leading-relaxed" :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                                <li v-for="line in job.desc" :key="line" class="flex gap-2">
                                    <span class="text-emerald-500/60">-</span>
                                    <span>{{ line }}</span>
                                </li>
                            </ul>
                        </div>
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

                <!-- Education -->
                <section class="rounded-lg border p-6" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> cat education.txt
                    </h2>
                    <div class="font-mono text-sm">
                        <div :class="isDark ? 'text-slate-200' : 'text-slate-800'">Master's degree in Engineering, Computer Science</div>
                        <div :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-xs">CESI Engineering School · 2018 — 2023</div>
                        <p class="mt-2 font-sans" :class="isDark ? 'text-slate-400' : 'text-slate-500'">Computer science, networks and cybersecurity. President of the school's Root-Me association.</p>
                    </div>
                    <div class="mt-5 font-mono text-sm">
                        <div class="mb-1 text-xs uppercase tracking-wider" :class="isDark ? 'text-slate-500' : 'text-slate-400'">locale -a</div>
                        <div :class="isDark ? 'text-slate-300' : 'text-slate-700'">French (native) · English (fluent)</div>
                    </div>
                </section>

                <!-- Security stack -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> ps aux | grep security
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="product in securityStack" :key="product" class="rounded-md px-3 py-1 font-mono text-xs" :class="tagClasses">{{ product }}</span>
                    </div>
                </section>

                <!-- Skills -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> skills --list
                    </h2>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div v-for="skill in skills" :key="skill.domain">
                            <dt class="font-mono text-sm font-bold" :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ skill.domain }}</dt>
                            <dd class="mt-1 text-sm leading-relaxed" :class="isDark ? 'text-slate-400' : 'text-slate-500'">{{ skill.items }}</dd>
                        </div>
                    </dl>
                </section>

                <!-- Toolkit -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> ls ~/toolkit/
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="tool in tools" :key="tool" class="rounded-md px-3 py-1 font-mono text-xs" :class="tagClasses">{{ tool }}</span>
                    </div>
                </section>

                <!-- Contact -->
                <section class="rounded-lg border p-6 md:col-span-2" :class="cardClasses">
                    <h2 class="mb-4 flex items-center gap-2 font-mono text-sm font-bold uppercase tracking-wider" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        <span class="text-slate-500">&gt;</span> cat .contact
                    </h2>
                    <div class="grid gap-3 font-mono text-sm md:grid-cols-3">
                        <a
                            v-for="link in contact"
                            :key="link.label"
                            :href="link.href"
                            :target="link.href.startsWith('mailto:') ? undefined : '_blank'"
                            rel="noopener noreferrer"
                            class="flex min-w-0 items-center gap-2 rounded-md px-3 py-2 transition"
                            :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-emerald-400' : 'text-slate-500 hover:bg-slate-100 hover:text-emerald-600'"
                        >
                            <span>{{ link.label }}</span>
                            <span :class="isDark ? 'text-slate-600' : 'text-slate-300'">→</span>
                            <span class="truncate" :class="isDark ? 'text-emerald-400/70' : 'text-emerald-600/70'">{{ link.value }}</span>
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

const tagClasses = computed(() =>
    isDark.value
        ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20'
        : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
);

const experience = [
    {
        title: 'Extended SOC Analyst Engineer (VIE)',
        company: 'Orange Cyberdefense',
        location: 'Singapore',
        period: 'Nov 2025 — Present',
        desc: ['Extended SOC analysis for Orange Cyberdefense in Singapore.'],
    },
    {
        title: 'Junior Software Engineer',
        company: 'Orange Cyberdefense',
        location: 'La Défense, France',
        period: 'Oct 2024 — Nov 2025',
        desc: [
            'Built a Laravel REST API managing the tenant lifecycle (creation, configuration, migration) across internal platforms and security vendors such as SentinelOne and CrowdStrike.',
            'Led the migration of 4,800 BVPNi clients from Juniper to Cato Networks with minimal downtime.',
            'Deployed and integrated CrowdStrike endpoint protection across the infrastructure.',
        ],
    },
    {
        title: 'Cybersecurity Engineer · SOC Analyst',
        company: 'Orange Cyberdefense',
        location: 'La Défense, France',
        period: 'Feb 2023 — Oct 2024',
        desc: [
            'Threat detection, analysis and incident response for SMB clients with SentinelOne, CrowdStrike and Vectra.',
            'Client kick-off meetings and webinar speaker for Orange Cyberdefense.',
        ],
    },
    {
        title: 'Developer / Administrator',
        company: 'BKK FrenchTouch',
        location: 'Bangkok, Thailand',
        period: 'Sep 2021 — Mar 2022',
        desc: ['Managed and secured 24 WordPress sites and developed a fully decentralized (IPFS) website.'],
    },
    {
        title: 'System Administrator & Developer',
        company: 'Omnitech Security',
        location: 'Bordeaux, France',
        period: 'Apr 2020 — Sep 2021',
        desc: [
            'Built and ran a supervision infrastructure with Centreon, Graphite and Grafana.',
            'Wrote a Python middleware from Centreon to Grafana and automated REST API acceptance tests (Node.js, Mocha).',
        ],
    },
];

const certs = [
    { name: 'OffSec Certified Professional (OSCP)', org: 'OffSec', year: '2026' },
    { name: 'eLearnSecurity Junior Penetration Tester (eJPT)', org: 'INE', year: '2023' },
    { name: 'SentinelOne Sales Engineer Expert', org: 'SentinelOne', year: '2023 — 2025' },
];

const securityStack = [
    'CrowdStrike Falcon', 'SentinelOne', 'Palo Alto Cortex XSIAM', 'Palo Alto Cortex XDR',
    'Vectra', 'Vade for M365', 'Cato Networks',
];

const skills = [
    { domain: 'SOC & Incident Response', items: 'EDR / XDR, NDR, email security, alert triage, incident response, malware analysis.' },
    { domain: 'Offensive Security', items: 'Active Directory lateral movement and exploitation, web exploits, Windows and Linux privilege escalation, pivoting, AV bypass.' },
    { domain: 'Forensics', items: 'SIEM and log analysis, Volatility, Autopsy.' },
    { domain: 'Systems & Networks', items: 'Server management, monitoring (Centreon, Grafana), DNS, VPN, routing, firewalls, MPLS, SD-WAN.' },
];

const tools = [
    'Python', 'PHP / Laravel', 'Node.js', 'C / C#', 'Java', 'SQL',
    'Bash', 'PowerShell', 'Linux', 'Windows', 'Docker', 'Git',
];

const contact = [
    { label: 'GitHub', value: '@RJSOG', href: 'https://github.com/RJSOG' },
    { label: 'LinkedIn', value: 'Eliott Donatien', href: 'https://www.linkedin.com/in/eliott-donatien-615295181/' },
    { label: 'Email', value: 'contact@rjsog.tech', href: 'mailto:contact@rjsog.tech' },
];
</script>
