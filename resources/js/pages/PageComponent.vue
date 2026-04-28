<template>
    <div>
        <h1>{{ page.path }}</h1>
        <div v-html="page.content"></div>
    </div>
</template>

<script setup lang='ts'>
import {usePage} from '@inertiajs/vue3'

const page = usePage().props.value.page
</script>


