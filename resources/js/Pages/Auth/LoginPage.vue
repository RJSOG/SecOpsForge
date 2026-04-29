<template>
    <div class="flex min-h-screen items-center justify-center transition-colors duration-200" :class="isDark ? 'bg-slate-900' : 'bg-slate-50'">
        <div class="w-full max-w-sm">
            <!-- Terminal style card -->
            <div class="overflow-hidden rounded-lg border" :class="isDark ? 'border-slate-700/50 bg-slate-950' : 'border-slate-200 bg-white'">
                <!-- Title bar -->
                <div class="flex items-center gap-2 border-b px-4 py-2.5" :class="isDark ? 'border-slate-800 bg-slate-900' : 'border-slate-100 bg-slate-50'">
                    <span class="h-2.5 w-2.5 rounded-full bg-red-500/80"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-500/80"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
                    <span class="ml-2 font-mono text-xs" :class="isDark ? 'text-slate-500' : 'text-slate-400'">auth@secopsforge</span>
                </div>

                <div class="p-6">
                    <h1 class="mb-1 font-mono text-lg font-bold" :class="isDark ? 'text-emerald-400' : 'text-emerald-600'">
                        > login
                    </h1>
                    <p class="mb-6 font-mono text-xs" :class="isDark ? 'text-slate-500' : 'text-slate-400'">
                        Authentication required.
                    </p>

                    <!-- Error -->
                    <div v-if="error" class="mb-4 rounded-md bg-red-500/10 px-3 py-2 text-xs text-red-400 ring-1 ring-red-500/20">
                        {{ error }}
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="mb-1 block font-mono text-xs font-medium" :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                                email
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                class="w-full rounded-md border px-3 py-2 font-mono text-sm focus:outline-none focus:ring-1"
                                :class="isDark
                                    ? 'border-slate-700 bg-slate-900 text-slate-300 focus:border-emerald-500/50 focus:ring-emerald-500/30'
                                    : 'border-slate-200 bg-slate-50 text-slate-800 focus:border-emerald-500 focus:ring-emerald-500/30'"
                            />
                        </div>

                        <div class="mb-6">
                            <label class="mb-1 block font-mono text-xs font-medium" :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                                password
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full rounded-md border px-3 py-2 font-mono text-sm focus:outline-none focus:ring-1"
                                :class="isDark
                                    ? 'border-slate-700 bg-slate-900 text-slate-300 focus:border-emerald-500/50 focus:ring-emerald-500/30'
                                    : 'border-slate-200 bg-slate-50 text-slate-800 focus:border-emerald-500 focus:ring-emerald-500/30'"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full rounded-md py-2 font-mono text-sm font-medium transition"
                            :class="isDark
                                ? 'bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/30 hover:bg-emerald-500/30'
                                : 'bg-emerald-500 text-white hover:bg-emerald-600'"
                        >
                            {{ loading ? 'Authenticating...' : '> authenticate' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <Link href="/" class="font-mono text-xs transition" :class="isDark ? 'text-slate-600 hover:text-emerald-400' : 'text-slate-400 hover:text-emerald-600'">
                    ← back to SecOpsForge
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

defineOptions({ layout: false });

const { isDark } = useTheme();

const form = ref({ email: '', password: '' });
const error = ref('');
const loading = ref(false);

async function submit() {
    loading.value = true;
    error.value = '';

    try {
        await axios.post('/login', form.value);
        router.visit('/');
    } catch (err: any) {
        error.value = err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Invalid credentials';
    }
    loading.value = false;
}
</script>
