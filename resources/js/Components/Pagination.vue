<script setup>
defineProps({
    links: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap justify-center gap-1 mt-4">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                 class="px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-md cursor-not-allowed"
                 v-html="link.label" />
            
            <Link v-else
                  :href="link.url"
                  :class="[
                      'px-4 py-2 text-sm rounded-md transition-all duration-200 hover:scale-105',
                      {
                          'bg-primary-500 text-white hover:bg-primary-600': link.active,
                          'bg-white text-gray-700 hover:bg-gray-100 hover:text-primary-600': !link.active
                      }
                  ]"
                  preserve-scroll
                  v-html="link.label" />
        </template>
    </div>
</template>

<style scoped>
:deep(.pagination-icon) {
    @apply w-5 h-5;
}

/* Animation for active state */
.router-link-active {
    @apply transform scale-105;
}

/* Hover effect */
a:hover {
    @apply shadow-md;
}

/* Transition for all interactive states */
a {
    @apply transition-all duration-200 ease-in-out;
}
</style> 