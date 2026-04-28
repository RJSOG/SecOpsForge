<template>
    <nav
        class="fixed left-0 right-0 top-0 z-50 w-full border-b border-slate-700/50 bg-slate-950 text-white backdrop-blur-sm"
    >
        <div class="mx-auto px-6">
            <div class="flex h-14 items-center justify-between">
                <Link href="/" class="text-lg font-semibold tracking-tight text-white transition hover:text-emerald-400">
                    SecOpsForge
                </Link>
                <div class="flex items-center space-x-1">
                    <Link
                        v-for="link in links"
                        :key="link.name"
                        :href="link.href"
                        class="px-3 py-1.5 text-sm font-medium transition-colors duration-150"
                        :class="getLinkClasses(link.href)"
                    >
                        {{ link.name }}
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const links = [
    { name: 'Home', href: '/' },
    { name: 'Red Team', href: '/redteam' },
    { name: 'Blue Team', href: '/blueteam' },
    { name: 'About', href: '/about' },
];

function getLinkClasses(href: string) {
    const isActive = isActiveLink(href);

    if (isActive) {
        return 'text-white bg-slate-800 rounded-md';
    }

    return 'text-slate-400 hover:text-white rounded-md hover:bg-slate-800/50';
}

function isActiveLink(href: string) {
    if (href === '/') {
        return page.url === '/';
    }
    return page.url.startsWith(href);
}
</script>
