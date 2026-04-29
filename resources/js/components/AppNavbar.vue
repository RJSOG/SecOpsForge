<template>
    <nav
        class="fixed left-0 right-0 top-0 z-50 w-full border-b transition-colors duration-200"
        :class="isDark
            ? 'border-slate-700/50 bg-slate-950'
            : 'border-slate-200 bg-white'"
    >
        <div class="mx-auto px-6">
            <div class="flex h-14 items-center justify-between">
                <Link href="/" class="flex items-center gap-2.5 transition hover:opacity-80">
                    <LogoIcon sizeClass="h-7 w-7" />
                    <span class="text-lg font-semibold tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">SecOpsForge</span>
                </Link>
                <div class="flex items-center space-x-1">
                    <Link
                        v-for="link in links"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors duration-150"
                        :class="getLinkClasses(link.href)"
                    >
                        {{ link.name }}
                    </Link>
                    <div class="ml-3 border-l pl-3" :class="isDark ? 'border-slate-700' : 'border-slate-200'">
                        <ThemeToggle />
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import LogoIcon from '@/components/LogoIcon.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { useTheme } from '@/composables/useTheme';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const { isDark } = useTheme();

const links = [
    { name: 'Home', href: '/' },
    { name: 'Red Team', href: '/redteam' },
    { name: 'Blue Team', href: '/blueteam' },
    { name: 'Automation', href: '/automation' },
    { name: 'Whoami', href: '/whoami' },
];

function getLinkClasses(href: string) {
    const isActive = isActiveLink(href);

    if (isDark.value) {
        return isActive
            ? 'text-white bg-slate-800'
            : 'text-slate-400 hover:text-white hover:bg-slate-800/50';
    }
    return isActive
        ? 'text-slate-900 bg-slate-100'
        : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100';
}

function isActiveLink(href: string) {
    if (href === '/') return page.url === '/';
    return page.url.startsWith(href);
}
</script>
