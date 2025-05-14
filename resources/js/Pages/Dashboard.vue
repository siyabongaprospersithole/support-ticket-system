<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight animate-fade-in">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg animate-slide-up">
                    <div class="p-8 text-gray-900">
                        <div class="text-center mb-12">
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Welcome to the Support Ticket System!</h3>
                            <p class="text-gray-600 text-lg">Your one-stop solution for managing support requests</p>
                        </div>
                        
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="custom-card hover-scale animate-slide-in" style="animation-delay: 0.1s">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Support Tickets</h3>
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 mb-6">View and manage all support tickets in the system. Track progress and respond to user requests efficiently.</p>
                                <Link :href="route('tickets.index')" class="custom-button inline-flex items-center">
                                    View All Tickets
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                            
                            <div class="custom-card hover-scale animate-slide-in" style="animation-delay: 0.2s">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Create Ticket</h3>
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 mb-6">Submit a new support request for assistance. Our team will help you resolve your issues quickly.</p>
                                <Link :href="route('tickets.create')" class="custom-button inline-flex items-center bg-green-600 hover:bg-green-700">
                                    Create New Ticket
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </Link>
                            </div>
                        </div>

                        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="custom-card hover-scale animate-slide-in" style="animation-delay: 0.3s">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Quick Response</h4>
                                    <p class="text-gray-600">Get fast responses to your support requests</p>
                                </div>
                            </div>

                            <div class="custom-card hover-scale animate-slide-in" style="animation-delay: 0.4s">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Track Progress</h4>
                                    <p class="text-gray-600">Monitor the status of your tickets in real-time</p>
                                </div>
                            </div>

                            <div class="custom-card hover-scale animate-slide-in" style="animation-delay: 0.5s">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h4>
                                    <p class="text-gray-600">Round-the-clock assistance for all your needs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
