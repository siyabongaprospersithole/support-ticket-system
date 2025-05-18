<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    users: Object
});
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search and Filter Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 max-w-lg">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Search users..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Sort by</option>
                                <option value="name">Name</option>
                                <option value="tickets">Most Tickets</option>
                                <option value="recent">Most Recent</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Users Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="user in users.data" :key="user.id" 
                         class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <!-- User Header -->
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-primary-600">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <Link :href="route('users.show', user.id)" 
                                          class="text-xl font-semibold text-gray-900 hover:text-primary-600 transition-colors duration-200">
                                        {{ user.name }}
                                    </Link>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary-600">{{ user.tickets_count }}</div>
                                    <div class="text-sm text-gray-500">Tickets</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary-600">{{ user.comments_count }}</div>
                                    <div class="text-sm text-gray-500">Comments</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary-600">{{ user.activities_count }}</div>
                                    <div class="text-sm text-gray-500">Activities</div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold text-gray-700">Recent Activity</h3>
                                <div v-if="user.activities.length > 0" class="space-y-2">
                                    <div v-for="activity in user.activities" :key="activity.id" 
                                         class="text-sm text-gray-600">
                                        <span class="font-medium">{{ activity.description }}</span>
                                        <span class="text-gray-400 text-xs ml-2">
                                            {{ new Date(activity.created_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 italic">
                                    No recent activity
                                </div>
                            </div>

                            <!-- View Profile Button -->
                            <div class="mt-4">
                                <Link :href="route('users.show', user.id)"
                                      class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    View Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <div class="flex justify-center">
                        <div v-for="link in users.links" :key="link.label"
                             :class="[
                                 'px-4 py-2 mx-1 rounded-md text-sm font-medium',
                                 link.active ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-50',
                                 !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                             ]"
                             v-html="link.label"
                             @click="link.url && $inertia.visit(link.url)"
                        ></div>
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