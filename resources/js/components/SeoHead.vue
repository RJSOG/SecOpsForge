<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Tab title for the current page, from the "seo" prop set by the controller.
const page = usePage();
const title = computed(() => (page.props.seo as { title?: string } | undefined)?.title);
</script>

<template>
    <Head v-if="title" :title="title" />
</template>
