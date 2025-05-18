<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object
});

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: 'Overview' },
    { id: 'tickets', label: 'Tickets' },
    { id: 'comments', label: 'Comments' },
    { id: 'activity', label: 'Activity' }
];
</script>

<template>
    <Head :title="user.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ user.name }}'s Profile
                </h2>
                <Link :href="route('users.index')"
                      class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Back to Users
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div class="h-24 w-24 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-primary-600">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                                <p class="text-gray-500">{{ user.email }}</p>
                                <p class="text-sm text-gray-500 mt-1">Member since {{ new Date(user.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="flex space-x-4">
                                <div class="text-center px-4">
                                    <div class="text-3xl font-bold text-primary-600">{{ user.tickets_count }}</div>
                                    <div class="text-sm text-gray-500">Tickets</div>
                                </div>
                                <div class="text-center px-4">
                                    <div class="text-3xl font-bold text-primary-600">{{ user.comments_count }}</div>
                                    <div class="text-sm text-gray-500">Comments</div>
                                </div>
                                <div class="text-center px-4">
                                    <div class="text-3xl font-bold text-primary-600">{{ user.activities_count }}</div>
                                    <div class="text-sm text-gray-500">Activities</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button v-for="tab in tabs" 
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'px-6 py-3 text-sm font-medium border-b-2',
                                        activeTab === tab.id
                                            ? 'border-primary-500 text-primary-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    ]">
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Recent Tickets -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Tickets</h3>
                                    <div class="space-y-4">
                                        <div v-for="ticket in user.tickets" :key="ticket.id" 
                                             class="bg-white rounded-lg p-4 shadow-sm">
                                            <Link :href="route('tickets.show', ticket.id)"
                                                  class="text-primary-600 hover:text-primary-700 font-medium">
                                                {{ ticket.title }}
                                            </Link>
                                            <p class="text-sm text-gray-500 mt-1">{{ ticket.description }}</p>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <span>{{ new Date(ticket.created_at).toLocaleDateString() }}</span>
                                                <span class="mx-2">•</span>
                                                <span :class="{
                                                    'text-green-600': ticket.status === 'open',
                                                    'text-yellow-600': ticket.status === 'pending',
                                                    'text-red-600': ticket.status === 'closed'
                                                }">{{ ticket.status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Comments -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Comments</h3>
                                    <div class="space-y-4">
                                        <div v-for="comment in user.comments" :key="comment.id" 
                                             class="bg-white rounded-lg p-4 shadow-sm">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-900">{{ comment.content }}</p>
                                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                                        <span>On ticket:</span>
                                                        <Link :href="route('tickets.show', comment.ticket_id)"
                                                              class="ml-1 text-primary-600 hover:text-primary-700">
                                                            #{{ comment.ticket_id }}
                                                        </Link>
                                                        <span class="mx-2">•</span>
                                                        <span>{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tickets Tab -->
                        <div v-if="activeTab === 'tickets'" class="space-y-4">
                            <div v-for="ticket in user.tickets" :key="ticket.id" 
                                 class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <Link :href="route('tickets.show', ticket.id)"
                                              class="text-lg font-medium text-primary-600 hover:text-primary-700">
                                            {{ ticket.title }}
                                        </Link>
                                        <p class="text-sm text-gray-500 mt-1">{{ ticket.description }}</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span :class="[
                                            'px-3 py-1 rounded-full text-xs font-medium',
                                            ticket.status === 'open' ? 'bg-green-100 text-green-800' : 
                                            ticket.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-red-100 text-red-800'
                                        ]">{{ ticket.status }}</span>
                                        <span class="text-sm text-gray-500">
                                            {{ new Date(ticket.created_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Tab -->
                        <div v-if="activeTab === 'comments'" class="space-y-4">
                            <div v-for="comment in user.comments" :key="comment.id" 
                                 class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-1">
                                        <p class="text-gray-900">{{ comment.content }}</p>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <span>On ticket:</span>
                                            <Link :href="route('tickets.show', comment.ticket_id)"
                                                  class="ml-1 text-primary-600 hover:text-primary-700">
                                                #{{ comment.ticket_id }}
                                            </Link>
                                            <span class="mx-2">•</span>
                                            <span>{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Tab -->
                        <div v-if="activeTab === 'activity'" class="space-y-4">
                            <div v-for="activity in user.activities" :key="activity.id" 
                                 class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <p class="text-gray-900">{{ activity.description }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ new Date(activity.created_at).toLocaleDateString() }}
                                        </p>
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style> 