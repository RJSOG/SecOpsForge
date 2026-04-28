<template>
    <nav
        class="fixed left-0 right-0 top-0 z-50 w-full rounded-[0.8vw] border-b border-b-slate-700 bg-slate-800 text-white"
    >
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex-shrink-0 text-xl font-bold">
                    <Link href="/">SecOpsForge</Link>
                </div>
                <div class="flex space-x-6">
                    <Link
                        v-for="link in links"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3 py-2 text-sm font-medium transition"
                        @mouseenter="hovered = link.href"
                        @mouseleave="hovered = null"
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
import { ref, watch } from 'vue';

const page = usePage();
const hovered = ref<string | null>(null);

const links = [
    { name: 'Home', href: '/' },
    { name: 'Red Team', href: '/redteam' },
    { name: 'Blue Team', href: '/blueteam' },
    { name: 'About', href: '/about' },
];

watch(
    () => page.url,
    () => {
        hovered.value = null;
    },
);

function getLinkClasses(href: string) {
    const isActive = isActiveLink(href);

    if (isActive && !hovered.value) {
        return 'bg-slate-700 rounded-full text-white';
    }

    if (isActive && hovered.value !== href) {
        return 'text-gray-300';
    }

    if (hovered.value === href) {
        return 'bg-slate-700 rounded-full text-white';
    }

    return 'text-gray-300 hover:bg-slate-700 hover:text-white';
}

function isActiveLink(href: string) {
    if (href === '/') {
        return page.url === '/'; // strict pour la home
    }
    return page.url.startsWith(href);
}
</script>
