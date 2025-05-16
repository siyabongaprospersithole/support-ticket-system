<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import debounce from 'lodash/debounce';
import Pagination from '@/Components/Pagination.vue';
import vSelect from 'vue-select';

const props = defineProps({
    activities: {
        type: Object,
        required: true
    },
    filters: Object
});

const search = ref(props.filters?.search || '');

// Activity type options for vue-select
const typeOptions = computed(() => [
    { label: 'All Types', value: '' },
    { label: 'Created', value: 'created' },
    { label: 'Updated', value: 'updated' },
    { label: 'Commented', value: 'commented' },
    { label: 'Status Changed', value: 'status_changed' }
]);

const type = ref(typeOptions.value.find(option => 
    option.value === props.filters?.type
) || typeOptions.value[0]);

const activityTypes = {
    created: 'Created',
    updated: 'Updated',
    deleted: 'Deleted',
    commented: 'Commented',
    status_changed: 'Status Changed',
    priority_changed: 'Priority Changed',
    assigned: 'Assigned'
};

const filter = debounce(() => {
    router.get(
        route('activities.index'),
        { 
            search: search.value,
            type: type.value?.value || '',
        },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

const clearFilters = () => {
    search.value = '';
    type.value = typeOptions.value[0];
    filter();
};

watch([search, type], () => {
    filter();
});

const getActivityIcon = (activityType) => {
    return {
        created: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        updated: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        deleted: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        commented: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        status_changed: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        priority_changed: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        assigned: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'
    }[activityType] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
};
</script>

<template>
    <Head title="Activity Log" />

    <AuthenticatedLayout>
        <template #header>
            <div class="activity-header">
                <h2 class="activity-title">
                    <svg class="w-8 h-8 mr-3 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Activity Log
                </h2>

                <div class="activity-filters">
                    <!-- Filters Section -->
                    <div class="mb-6">
                        <!-- Search Bar and Type Filter -->
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Search activities by description..."
                                    class="block w-full pl-10 pr-3 py-3 text-base border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 hover:shadow-sm"
                                />
                            </div>
                            <!-- Activity Type Filter -->
                            <div class="relative group w-64">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                        </svg>
                                    </div>
                                    <v-select
                                        v-model="type"
                                        :options="typeOptions"
                                        :reduce="option => option"
                                        label="label"
                                        class="vs-select-container"
                                        :clearable="false"
                                        placeholder="Filter by type..."
                                    />
                                </div>
                            </div>
                            <button
                                v-if="search || type.value"
                                @click="clearFilters"
                                class="inline-flex items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
                            >
                                <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="activity-page">
            <div class="activity-timeline">
                <ul v-if="activities.data.length" class="activity-list">
                    <li v-for="(activity, index) in activities.data" :key="activity.id" class="activity-item">
                        <div class="activity-line"></div>
                        <div class="flex activity-wrapper">
                            <div :class="['activity-icon-wrapper', `activity-icon-${activity.type}`]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path :d="getActivityIcon(activity.type)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header-content">
                                    <div class="mb-1">
                                        <Link 
                                            v-if="activity.ticket"
                                            :href="activity.ticket.url"
                                            class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                        >
                                            #{{ activity.ticket.number }} - {{ activity.ticket.title }}
                                        </Link>
                                    </div>
                                    <p class="activity-description">
                                        {{ activity.description }}
                                    </p>
                                    <div class="activity-meta">
                                        <span>{{ activity.created_at }}</span>
                                        <span>•</span>
                                        <div v-if="activity.causer" class="activity-user">
                                            <div class="activity-avatar">
                                                {{ activity.causer.name.charAt(0) }}
                                            </div>
                                            <span>{{ activity.causer.name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="activity.properties && Object.keys(activity.properties).length > 0" class="activity-changes">
                                    <div class="activity-changes-list">
                                        <!-- Comment content -->
                                        <div v-if="activity.type === 'commented' && activity.details?.content" class="mt-2 p-4 bg-gray-50 rounded-lg">
                                            <p class="text-gray-700 whitespace-pre-wrap">{{ activity.details.content }}</p>
                                        </div>

                                        <!-- Status changes -->
                                        <div v-if="activity.type === 'status_changed' && activity.details" class="mt-2">
                                            <span class="text-gray-600">Status changed from </span>
                                            <span class="font-medium">{{ activity.details.old_status }}</span>
                                            <span class="text-gray-600"> to </span>
                                            <span class="font-medium">{{ activity.details.new_status }}</span>
                                        </div>

                                        <!-- Priority changes -->
                                        <div v-if="activity.type === 'priority_changed' && activity.details" class="mt-2">
                                            <span class="text-gray-600">Priority changed from </span>
                                            <span class="font-medium">{{ activity.details.old_priority }}</span>
                                            <span class="text-gray-600"> to </span>
                                            <span class="font-medium">{{ activity.details.new_priority }}</span>
                                        </div>

                                        <!-- General updates -->
                                        <div v-if="activity.type === 'updated' && activity.details?.changes" class="mt-2 space-y-1">
                                            <div v-for="(change, field) in activity.details.changes" :key="field" class="text-sm">
                                                <span class="text-gray-600">{{ change.field }} changed from </span>
                                                <span class="font-medium">{{ change.old }}</span>
                                                <span class="text-gray-600"> to </span>
                                                <span class="font-medium">{{ change.new }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <div v-else class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No activities found</h3>
                    <p class="text-gray-500">Activities will appear here as you interact with the system.</p>
                </div>

                <div v-if="activities.data.length" class="mt-8">
                    <Pagination :links="activities.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.vs-select-container {
    @apply w-full;
}

.vs-select-container .vs__dropdown-toggle {
    @apply pl-10 pr-3 py-[0.4rem] text-base border-gray-300 rounded-lg transition-all duration-200 hover:shadow-sm bg-white;
}

.vs-select-container .vs__dropdown-toggle:focus-within {
    @apply ring-2 ring-primary-500 border-primary-500;
}

.vs-select-container .vs__selected {
    @apply m-0 text-sm;
}

.vs-select-container .vs__search {
    @apply text-sm;
}

.vs-select-container .vs__dropdown-menu {
    @apply mt-1 border border-gray-100 rounded-lg shadow-lg;
}

.vs-select-container .vs__dropdown-option {
    @apply py-2 px-3 text-sm text-gray-700;
}

.vs-select-container .vs__dropdown-option--highlight {
    @apply bg-primary-50 text-primary-500;
}

.vs-select-container .vs__clear {
    @apply hidden;
}

.vs-select-container .vs__open-indicator {
    @apply fill-gray-400;
}

.vs-select-container .vs__actions {
    @apply p-0;
}

/* Custom styles for activity type options */
.vs-select-container .vs__dropdown-option[data-type="created"] {
    @apply text-green-700;
}

.vs-select-container .vs__dropdown-option[data-type="updated"] {
    @apply text-blue-700;
}

.vs-select-container .vs__dropdown-option[data-type="commented"] {
    @apply text-orange-700;
}

.vs-select-container .vs__dropdown-option[data-type="status_changed"] {
    @apply text-purple-700;
}
</style> 