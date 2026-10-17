<template>
    <SeoHead />
    <nav
        class="fixed left-0 right-0 top-0 z-50 w-full border-b transition-colors duration-200"
        :class="isDark
            ? 'border-slate-700/50 bg-slate-950'
            : 'border-slate-200 bg-white'"
    >
        <div class="mx-auto px-4 sm:px-6">
            <div class="flex h-14 items-center justify-between">
                <Link href="/" class="flex items-center gap-2.5 transition hover:opacity-80">
                    <LogoIcon sizeClass="h-7 w-7" />
                    <span class="text-lg font-semibold tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">SecOpsForge</span>
                </Link>

                <!-- Desktop -->
                <div class="hidden items-center space-x-1 lg:flex">
                    <Link
                        v-for="link in visibleLinks"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors duration-150"
                        :class="getLinkClasses(link.href)"
                    >
                        {{ link.name }}
                    </Link>
                    <div class="ml-3 border-l pl-3" :class="isDark ? 'border-slate-700' : 'border-slate-200'">
                        <SearchBar />
                    </div>
                    <div class="ml-2">
                        <ThemeToggle />
                    </div>
                    <div class="ml-2 border-l pl-3" :class="isDark ? 'border-slate-700' : 'border-slate-200'">
                        <button
                            v-if="isAuthenticated"
                            type="button"
                            class="rounded-md px-2.5 py-1.5 text-xs font-medium transition"
                            :class="isDark ? 'text-slate-500 hover:text-red-400' : 'text-slate-400 hover:text-red-500'"
                            @click="logout"
                        >
                            Logout
                        </button>
                        <Link
                            v-else
                            href="/login"
                            class="rounded-md px-2.5 py-1.5 text-xs font-medium transition"
                            :class="isDark ? 'text-slate-500 hover:text-emerald-400' : 'text-slate-400 hover:text-emerald-600'"
                        >
                            Login
                        </Link>
                    </div>
                </div>

                <!-- Mobile -->
                <div class="flex items-center gap-1 lg:hidden">
                    <ThemeToggle />
                    <button
                        type="button"
                        class="rounded-md p-2 transition-colors duration-150"
                        :class="isDark ? 'text-slate-400 hover:bg-slate-800 hover:text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900'"
                        :aria-expanded="menuOpen"
                        aria-controls="mobile-menu"
                        aria-label="Menu"
                        @click="menuOpen = !menuOpen"
                    >
                        <svg v-if="!menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="menuOpen"
            id="mobile-menu"
            class="border-t px-4 pb-4 pt-3 lg:hidden"
            :class="isDark ? 'border-slate-800 bg-slate-950' : 'border-slate-100 bg-white'"
        >
            <SearchBar block />
            <div class="mt-3 space-y-1">
                <Link
                    v-for="link in visibleLinks"
                    :key="link.name"
                    :href="link.href"
                    class="block rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
                    :class="getLinkClasses(link.href)"
                >
                    {{ link.name }}
                </Link>
            </div>
            <div class="mt-3 border-t pt-3" :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <button
                    v-if="isAuthenticated"
                    type="button"
                    class="block w-full rounded-md px-3 py-2 text-left text-sm font-medium transition"
                    :class="isDark ? 'text-slate-500 hover:text-red-400' : 'text-slate-400 hover:text-red-500'"
                    @click="logout"
                >
                    Logout
                </button>
                <Link
                    v-else
                    href="/login"
                    class="block rounded-md px-3 py-2 text-sm font-medium transition"
                    :class="isDark ? 'text-slate-500 hover:text-emerald-400' : 'text-slate-400 hover:text-emerald-600'"
                >
                    Login
                </Link>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import LogoIcon from '@/components/LogoIcon.vue';
import SearchBar from '@/components/SearchBar.vue';
import SeoHead from '@/components/SeoHead.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { useTheme } from '@/composables/useTheme';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const { isDark } = useTheme();
const isAuthenticated = !!page.props.auth?.user;
const menuOpen = ref(false);

// Close the mobile menu after navigating.
watch(() => page.url, () => {
    menuOpen.value = false;
});
// is_admin is shared via HandleInertiaRequests -> auth.user. Only the admin
// account gets a link to /editor; the backend enforces this independently
// (EnsureUserIsAdmin), this is purely a UX shortcut.
const isAdmin = computed(() => !!(page.props.auth?.user as { is_admin?: boolean } | undefined)?.is_admin);

function logout() {
    router.post('/logout', {}, {
        onSuccess: () => router.visit('/'),
    });
}

const links = [
    { name: 'Home', href: '/' },
    { name: 'Red Team', href: '/redteam' },
    { name: 'Blue Team', href: '/blueteam' },
    { name: 'Automation', href: '/automation' },
    { name: 'Whoami', href: '/whoami' },
];

const visibleLinks = computed(() =>
    isAdmin.value ? [...links, { name: 'Editor', href: '/editor' }] : links
);

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
