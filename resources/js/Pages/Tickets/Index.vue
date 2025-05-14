<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tickets: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const priority = ref(props.filters?.priority || '');
const dateFrom = ref(props.filters?.dateFrom || '');
const dateTo = ref(props.filters?.dateTo || '');

const priorities = {
    critical: 'Critical',
    high: 'High',
    medium: 'Medium',
    low: 'Low'
};

const statuses = {
    all: 'All',
    open: 'Open',
    closed: 'Closed'
};

const getPriorityColor = (priority) => {
    switch(priority) {
        case 'critical':
            return 'priority-critical';
        case 'high':
            return 'priority-high';
        case 'medium':
            return 'priority-medium';
        case 'low':
            return 'priority-low';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const filter = debounce(() => {
    router.get(
        route('tickets.index'),
        { search: search.value, status: status.value, priority: priority.value, dateFrom: dateFrom.value, dateTo: dateTo.value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch([search, status, priority, dateFrom, dateTo], () => {
    filter();
});

const clearFilters = () => {
    search.value = '';
    status.value = '';
    priority.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    filter();
};
</script>

<template>
    <Head title="Support Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center animate-fade-in">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Support Tickets
                </h2>
                <Link :href="route('tickets.create')" class="custom-button inline-flex items-center bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create New Ticket
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-100 backdrop-filter backdrop-blur-lg animate-slide-up">
                    <div class="relative">
                        <!-- Decorative Header Bar -->
                        <div class="h-2 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                    <div class="p-6 text-gray-900">
                        <!-- Filters Section -->
                            <div class="mb-6 p-6 bg-white rounded-xl shadow-sm border border-gray-100 animate-fade-in hover-lift">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter Tickets
                                </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                <!-- Search -->
                                    <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        v-model="search"
                                        placeholder="Search tickets..."
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all duration-200 hover:shadow-sm"
                                    />
                                </div>

                                <!-- Status Filter -->
                                    <div class="relative group">
                                    <select
                                        v-model="status"
                                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg transition-all duration-200 hover:shadow-sm"
                                    >
                                        <option value="">All Status</option>
                                        <option v-for="(label, value) in statuses" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                            <svg class="w-4 h-4 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                </div>

                                <!-- Priority Filter -->
                                    <div class="relative group">
                                    <select
                                        v-model="priority"
                                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg transition-all duration-200 hover:shadow-sm"
                                    >
                                        <option value="">All Priorities</option>
                                        <option v-for="(label, value) in priorities" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                            <svg class="w-4 h-4 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                </div>

                                <!-- Date Range -->
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    <input
                                        type="date"
                                        v-model="dateFrom"
                                            class="block w-full pl-10 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg transition-all duration-200 hover:shadow-sm"
                                        placeholder="From Date"
                                    />
                                </div>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    <input
                                        type="date"
                                        v-model="dateTo"
                                            class="block w-full pl-10 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg transition-all duration-200 hover:shadow-sm"
                                        placeholder="To Date"
                                    />
                                </div>
                            </div>
                            
                            <!-- Clear Filters Button -->
                            <div class="mt-4 flex justify-end">
                                <button
                                    @click="clearFilters"
                                        class="glass-button"
                                >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    Clear Filters
                                </button>
                            </div>
                        </div>

                            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm animate-success-notification">
                            <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-100 rounded-full p-2 mr-3">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Success!</p>
                                        <p class="text-sm">{{ $page.props.flash.success }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="tickets.length === 0" class="text-center py-12 animate-fade-in bg-white rounded-xl border border-dashed border-gray-200 shadow-sm">
                                <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500 text-lg mb-4">No tickets found.</p>
                                <p class="text-gray-400 mb-6">Create your first support ticket to get started.</p>
                              
                            </div>
                            <div v-else>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                                    Ticket List 
                                    <span class="ml-2 text-sm bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">{{ tickets.length }}</span>
                                </h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(ticket, index) in tickets" :key="ticket.id" 
                                                class="hover:bg-gray-50 transition-all duration-300 animate-slide-in"
                                                :style="{ animationDelay: `${index * 0.05}s` }">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-md text-xs font-medium">
                                                        #{{ ticket.id }}
                                                    </span>
                                                </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors duration-200">{{ ticket.title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white text-xs font-medium shadow transform hover:scale-110 transition-all duration-200">
                                                            {{ ticket.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ ticket.user?.name || 'Unknown User' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['priority-badge', getPriorityColor(ticket.priority)]">
                                                {{ ticket.priority?.charAt(0)?.toUpperCase() + ticket.priority?.slice(1) || 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                        'status-badge flex items-center justify-center space-x-1': true,
                                                'status-open': ticket.status === 'open',
                                                'status-closed': ticket.status === 'closed'
                                            }">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path v-if="ticket.status === 'open'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>{{ ticket.status === 'open' ? 'Open' : 'Closed' }}</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ticket.created_at ? new Date(ticket.created_at).toLocaleDateString() : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('tickets.show', ticket.id)" 
                                                          class="view-button group">
                                                View
                                                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-button {
    @apply px-4 py-2 text-white text-sm font-medium rounded-md shadow-sm transition-all duration-300 transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500;
}

.glass-button {
    @apply flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md shadow-sm transition-all duration-300 transform hover:scale-105 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500;
}

.gradient-button {
    @apply flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-md shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.view-button {
    @apply inline-flex items-center px-3 py-1.5 rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200;
}

.priority-badge {
    @apply inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full shadow-sm;
}

.priority-critical {
    @apply bg-gradient-to-r from-red-500 to-pink-500 text-white;
}

.priority-high {
    @apply bg-gradient-to-r from-orange-400 to-pink-400 text-white;
}

.priority-medium {
    @apply bg-gradient-to-r from-yellow-400 to-orange-400 text-white;
}

.priority-low {
    @apply bg-gradient-to-r from-green-400 to-teal-400 text-white;
}

.status-badge {
    @apply px-2.5 py-1 text-xs font-medium rounded-full shadow-sm;
}

.status-open {
    @apply bg-gradient-to-r from-blue-500 to-indigo-500 text-white;
}

.status-closed {
    @apply bg-gradient-to-r from-gray-400 to-gray-500 text-white;
}

.hover-lift {
    @apply transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-lg;
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes success-notification {
    0% {
        opacity: 0;
        transform: translateX(40px);
    }
    10% {
        opacity: 1;
        transform: translateX(0);
    }
    90% {
        opacity: 1;
        transform: translateX(0);
    }
    100% {
        opacity: 0;
        transform: translateX(40px);
    }
}

.animate-slide-up {
    animation: slide-up 0.5s ease-out;
}

.animate-slide-in {
    animation: slide-in 0.4s ease-out forwards;
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}

.animate-success-notification {
    animation: success-notification 5s forwards;
}
</style> 