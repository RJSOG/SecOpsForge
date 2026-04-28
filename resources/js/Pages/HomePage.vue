<template>
    <div class="flex min-h-[calc(100vh-3.5rem)] items-center justify-center">
        <div class="text-center">
            <h1 class="mb-3 text-4xl font-bold tracking-tight text-white">SecOpsForge</h1>
            <p class="text-lg text-slate-400">Notes de cybersécurité — Red Team & Blue Team</p>
            <div class="mt-8 flex justify-center gap-4">
                <Link href="/redteam" class="rounded-md bg-red-500/10 px-5 py-2.5 text-sm font-medium text-red-400 ring-1 ring-red-500/20 transition hover:bg-red-500/20">
                    Red Team
                </Link>
                <Link href="/blueteam" class="rounded-md bg-blue-500/10 px-5 py-2.5 text-sm font-medium text-blue-400 ring-1 ring-blue-500/20 transition hover:bg-blue-500/20">
                    Blue Team
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
