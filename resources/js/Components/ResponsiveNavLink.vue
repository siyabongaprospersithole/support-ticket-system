<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#1455C0] text-start text-base font-medium text-[#1455C0] bg-blue-50 focus:outline-none focus:text-[#0F46A0] focus:bg-blue-100 focus:border-[#0F46A0] transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-[#1455C0] hover:bg-gray-50 hover:border-[#1455C0]/20 focus:outline-none focus:text-[#1455C0] focus:bg-gray-50 focus:border-[#1455C0]/20 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
