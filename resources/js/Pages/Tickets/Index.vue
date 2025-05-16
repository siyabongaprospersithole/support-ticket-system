<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const props = defineProps({
    tickets: {
        type: Object,
        required: true
    },
    filters: Object,
    users: {
        type: Array,
        default: () => []
    }
});

// Transform users for vue-select
const userOptions = computed(() => {
    return [
        { label: 'All Users', value: '' },
        ...props.users.map(user => ({
            label: user.name,
            value: user.id
        }))
    ];
});

// Transform statuses for vue-select
const statusOptions = computed(() => [
    { label: 'All Status', value: '' },
    { label: 'Open', value: 'open' },
    { label: 'Closed', value: 'closed' }
]);

// Transform priorities for vue-select
const priorityOptions = computed(() => [
    { label: 'All Priorities', value: '' },
    { label: 'Critical', value: 'critical' },
    { label: 'High', value: 'high' },
    { label: 'Medium', value: 'medium' },
    { label: 'Low', value: 'low' }
]);

const search = ref(props.filters?.search || '');
const userFilter = ref(userOptions.value.find(option => 
    option.value.toString() === (props.filters?.user_filter || '').toString()
) || userOptions.value[0]);
const status = ref(statusOptions.value.find(option => 
    option.value === props.filters?.status
) || statusOptions.value[0]);
const priority = ref(priorityOptions.value.find(option => 
    option.value === props.filters?.priority
) || priorityOptions.value[0]);
const dateFrom = ref(props.filters?.dateFrom || '');
const dateTo = ref(props.filters?.dateTo || '');
const sortBy = ref(props.filters?.sortBy || '');
const sortDirection = ref(props.filters?.sortDirection || 'desc');

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

const sortableColumns = {
    id: 'ID',
    title: 'Title',
    priority: 'Priority',
    status: 'Status',
    created_at: 'Date'
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

const sort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDirection.value = 'desc';
    }
    filter();
};

const getSortIcon = (column) => {
    if (sortBy.value !== column) return 'none';
    return sortDirection.value === 'asc' ? 'asc' : 'desc';
};

const filter = debounce(() => {
    router.get(
        route('tickets.index'),
        { 
            search: search.value, 
            status: status.value?.value || '',
            priority: priority.value?.value || '',
            dateFrom: dateFrom.value, 
            dateTo: dateTo.value,
            sortBy: sortBy.value,
            sortDirection: sortDirection.value,
            user_filter: userFilter.value?.value || '',
            page: props.tickets?.current_page
        },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch([search, userFilter, status, priority, dateFrom, dateTo], () => {
    filter();
});

const clearFilters = () => {
    search.value = '';
    userFilter.value = userOptions.value[0];
    status.value = statusOptions.value[0];
    priority.value = priorityOptions.value[0];
    dateFrom.value = '';
    dateTo.value = '';
    filter();
};

const selectedTickets = ref(new Set());
const isSelectAll = ref(false);

const toggleSelectAll = () => {
    if (isSelectAll.value) {
        selectedTickets.value.clear();
    } else {
        tickets.forEach(ticket => selectedTickets.value.add(ticket.id));
    }
    isSelectAll.value = !isSelectAll.value;
};

const toggleTicketSelection = (ticketId) => {
    if (selectedTickets.value.has(ticketId)) {
        selectedTickets.value.delete(ticketId);
        isSelectAll.value = false;
    } else {
        selectedTickets.value.add(ticketId);
        isSelectAll.value = selectedTickets.value.size === tickets.length;
    }
};

const bulkUpdateStatus = (newStatus) => {
    router.patch(
        route('tickets.bulk-update'),
        { 
            ids: Array.from(selectedTickets.value),
            status: newStatus 
        },
        { 
            preserveState: true,
            onSuccess: () => {
                selectedTickets.value.clear();
                isSelectAll.value = false;
            }
        }
    );
};

const bulkUpdatePriority = (newPriority) => {
    router.patch(
        route('tickets.bulk-update'),
        { 
            ids: Array.from(selectedTickets.value),
            priority: newPriority 
        },
        { 
            preserveState: true,
            onSuccess: () => {
                selectedTickets.value.clear();
                isSelectAll.value = false;
            }
        }
    );
};

const bulkDelete = () => {
    if (confirm('Are you sure you want to delete the selected tickets? This action cannot be undone.')) {
        router.delete(
            route('tickets.bulk-delete'),
            {
                data: { ids: Array.from(selectedTickets.value) },
                preserveState: true,
                onSuccess: () => {
                    selectedTickets.value.clear();
                    isSelectAll.value = false;
                }
            }
        );
    }
};

const showAdvancedFilters = ref(false);
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
                        <div class="mb-6 space-y-4">
                            <!-- Main Search Bar -->
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
                                        placeholder="Search tickets by title or description..."
                                        class="block w-full pl-10 pr-3 py-3 text-base border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 hover:shadow-sm"
                                    />
                                </div>
                                <button
                                    @click="showAdvancedFilters = !showAdvancedFilters"
                                    class="inline-flex items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
                                >
                                    <svg 
                                        class="h-5 w-5 mr-2 text-gray-400" 
                                        :class="{ 'rotate-180': showAdvancedFilters }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                    {{ showAdvancedFilters ? 'Hide Filters' : 'Show Filters' }}
                                </button>
                                <button
                                    v-if="search || userFilter.value || status.value || priority.value || dateFrom || dateTo"
                                    @click="clearFilters"
                                    class="inline-flex items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
                                >
                                    <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear Filters
                                </button>
                            </div>

                            <!-- Advanced Filters -->
                            <div 
                                v-show="showAdvancedFilters"
                                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 transition-all duration-300"
                            >
                                <h3 class="text-lg font-medium text-gray-900 flex items-center mb-4">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Advanced Filters
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- User Filter -->
                                    <div class="relative group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <v-select
                                                v-model="userFilter"
                                                :options="userOptions"
                                                :reduce="option => option"
                                                label="label"
                                                class="vs-select-container"
                                                :clearable="false"
                                            />
                                        </div>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="relative group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <v-select
                                                v-model="status"
                                                :options="statusOptions"
                                                :reduce="option => option"
                                                label="label"
                                                class="vs-select-container"
                                                :clearable="false"
                                            />
                                        </div>
                                    </div>

                                    <!-- Priority Filter -->
                                    <div class="relative group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <v-select
                                                v-model="priority"
                                                :options="priorityOptions"
                                                :reduce="option => option"
                                                label="label"
                                                class="vs-select-container"
                                                :clearable="false"
                                            />
                                        </div>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="space-y-4">
                                        <div class="relative">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <input
                                                    type="date"
                                                    v-model="dateFrom"
                                                    class="block w-full pl-10 pr-3 py-2 text-base border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all duration-200 hover:shadow-sm"
                                                />
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <input
                                                    type="date"
                                                    v-model="dateTo"
                                                    class="block w-full pl-10 pr-3 py-2 text-base border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all duration-200 hover:shadow-sm"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                            <div v-if="tickets.data.length === 0" class="text-center py-12 animate-fade-in bg-white rounded-xl border border-dashed border-gray-200 shadow-sm">
                                <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500 text-lg mb-4">No tickets found.</p>
                                <p class="text-gray-400 mb-6">Create your first support ticket to get started.</p>
                              
                            </div>
                            <div v-else>
                                <!-- Selection Toolbar -->
                                <div v-if="selectedTickets.size > 0" 
                                     class="mb-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 animate-slide-down flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600">
                                            {{ selectedTickets.size }} ticket{{ selectedTickets.size > 1 ? 's' : '' }} selected
                                        </span>
                                        <div class="flex items-center space-x-2">
                                            <select 
                                                class="bulk-action-select"
                                                @change="($event) => bulkUpdateStatus($event.target.value)"
                                            >
                                                <option value="">Change Status</option>
                                                <option v-for="(label, value) in statuses" :key="value" :value="value">
                                                    {{ label }}
                                                </option>
                                            </select>
                                            <select 
                                                class="bulk-action-select"
                                                @change="($event) => bulkUpdatePriority($event.target.value)"
                                            >
                                                <option value="">Change Priority</option>
                                                <option v-for="(label, value) in priorities" :key="value" :value="value">
                                                    {{ label }}
                                                </option>
                                            </select>
                                            <button 
                                                @click="bulkDelete"
                                                class="bulk-delete-button"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <button 
                                        @click="selectedTickets.clear(); isSelectAll = false;"
                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Ticket List 
                                    <span class="ml-2 text-sm bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">{{ tickets.total }}</span>
                                </h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-100 shadow-sm">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <div class="flex items-center">
                                                        <input
                                                            type="checkbox"
                                                            :checked="isSelectAll"
                                                            @change="toggleSelectAll"
                                                            class="checkbox-input"
                                                        />
                                                    </div>
                                                </th>
                                                <th v-for="(label, column) in sortableColumns" 
                                                    :key="column" 
                                                    scope="col" 
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                                    @click="sort(column)">
                                                    <div class="flex items-center space-x-1">
                                                        <span>{{ label }}</span>
                                                        <span v-if="getSortIcon(column) !== 'none'" class="ml-2">
                                                            <svg v-if="getSortIcon(column) === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                            </svg>
                                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(ticket, index) in tickets.data" :key="ticket.id" 
                                                class="hover:bg-gray-50 transition-all duration-300 animate-slide-in group cursor-pointer"
                                                :class="{ 'bg-indigo-50': selectedTickets.has(ticket.id) }"
                                                :style="{ animationDelay: `${index * 0.05}s` }"
                                                @click.stop="($event) => {
                                                    // Only navigate if we didn't click on the checkbox
                                                    if (!$event.target.closest('.checkbox-wrapper')) {
                                                        router.visit(route('tickets.show', ticket.id))
                                                    }
                                                }">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center checkbox-wrapper">
                                                        <input
                                                            type="checkbox"
                                                            :checked="selectedTickets.has(ticket.id)"
                                                            @change="toggleTicketSelection(ticket.id)"
                                                            class="checkbox-input"
                                                        />
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-md text-xs font-medium">
                                                        #{{ ticket.id }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition-colors duration-200">
                                                        {{ ticket.title }}
                                                        <span class="inline-block ml-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </span>
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
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span :class="{'comment-count': true, 'has-comments': ticket.comments_count > 0}">
                                                            {{ ticket.comments_count }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-if="tickets.data.length > 0" class="px-6 py-4 bg-white border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-gray-500">
                                            Showing {{ tickets.from }} to {{ tickets.to }} of {{ tickets.total }} tickets
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button 
                                                v-for="link in tickets.links" 
                                                :key="link.label"
                                                @click="link.url ? router.get(link.url) : null"
                                                :disabled="!link.url"
                                                :class="[
                                                    'px-4 py-2 text-sm rounded-md transition-colors duration-200',
                                                    link.active 
                                                        ? 'bg-primary-600 text-white' 
                                                        : link.url 
                                                            ? 'bg-white text-gray-700 hover:bg-gray-50' 
                                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                ]"
                                                v-html="link.label"
                                            ></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

/* Custom styles for status options */
.vs-select-container .vs__dropdown-option[data-status="open"] {
    @apply text-green-700;
}

.vs-select-container .vs__dropdown-option[data-status="closed"] {
    @apply text-red-700;
}

/* Custom styles for priority options */
.vs-select-container .vs__dropdown-option[data-priority="critical"] {
    @apply text-red-700;
}

.vs-select-container .vs__dropdown-option[data-priority="high"] {
    @apply text-orange-700;
}

.vs-select-container .vs__dropdown-option[data-priority="medium"] {
    @apply text-yellow-700;
}

.vs-select-container .vs__dropdown-option[data-priority="low"] {
    @apply text-green-700;
}
</style> 