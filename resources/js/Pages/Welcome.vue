<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const showAnimation = ref(true);
const animationCompleted = ref(false);

onMounted(() => {
    setTimeout(() => {
        showAnimation.value = false;
        setTimeout(() => {
            animationCompleted.value = true;
        }, 1000);
    }, 3000);
});
</script>

<template>
    <Head title="Welcome to Support Ticket System" />

    <div class="landing-page">
        <!-- Initial Animation (3 seconds) -->
        <div v-if="showAnimation" class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <div class="animate-logo mb-6">
                    <svg class="w-32 h-32 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="45" stroke="white" stroke-width="2" class="animate-circle" />
                        <path d="M30 50L45 65L70 40" stroke="white" stroke-width="5" class="animate-checkmark" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white animate-fade-in">Support Ticket System</h1>
                <p class="text-gray-300 mt-4 animate-fade-in-delayed">Loading your experience...</p>
            </div>
        </div>

        <!-- Actual Content (after animation) -->
        <div v-else :class="['w-full transition-opacity duration-1000', animationCompleted ? 'opacity-100' : 'opacity-0']">
            <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="glass-button bg-white bg-opacity-10 hover:bg-opacity-20">
                    Dashboard
                </Link>

                <template v-else>
                    <a :href="route('login')" class="glass-button bg-white bg-opacity-10 hover:bg-opacity-20 text-white">
                        Log in
                    </a>

                    <a v-if="canRegister" :href="route('register')" class="ml-4 glass-button bg-indigo-500 hover:bg-indigo-600 text-white">
                        Register
                    </a>
                </template>
            </div>

            <div class="landing-header">
                <div class="flex flex-col items-center">
                    <!-- Logo -->
                    <div class="landing-logo">
                        <svg class="w-24 h-24 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.5 3.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2l-1.5 1.5L9 2 7.5 3.5 6 2 4.5 3.5 3 2v20l1.5-1.5L6 22l1.5-1.5L9 22l1.5-1.5L12 22l1.5-1.5L15 22l1.5-1.5L18 22l1.5-1.5L21 22V2l-1.5 1.5zM19 19.09H5V4.91h14v14.18zM6 15h12v2H6zm0-4h12v2H6zm0-4h12v2H6z" />
                        </svg>
                    </div>

                    <!-- Title and Subtitle -->
                    <h1 class="text-4xl font-bold text-white mb-4">Support Ticket System</h1>
                    <p class="text-lg text-indigo-200 text-center max-w-2xl mb-8">A modern, efficient platform for managing support tickets and providing outstanding customer service.</p>

                    <!-- Call to Action -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-16">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="glass-button bg-indigo-500 hover:bg-indigo-600">
                            <i class="fas fa-tachometer-alt mr-2"></i> Go to Dashboard
                        </Link>
                        <Link v-else :href="route('register')" class="glass-button bg-indigo-500 hover:bg-indigo-600 text-white">
                            <i class="fas fa-user-plus mr-2 "></i> Create an Account
                        </Link>
                        <Link :href="route('login')" class="glass-button bg-white bg-opacity-10 hover:bg-opacity-20 text-white">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </Link>
                    </div>

                    <!-- Features Section -->
                    <div class="landing-features">
                        <div class="feature-card">
                            <div class="feature-icon bg-indigo-600">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mt-4 mb-2">Ticket Management</h3>
                            <p class="text-indigo-200">Create, track, and manage support tickets with ease. Assign priorities and monitor status changes.</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon bg-blue-600">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mt-4 mb-2">Seamless Communication</h3>
                            <p class="text-indigo-200">Comment on tickets, receive real-time notifications, and maintain clear communication with customers.</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon bg-purple-600">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mt-4 mb-2">Insightful Dashboard</h3>
                            <p class="text-indigo-200">Get a comprehensive overview of all ongoing tickets, priorities, and recent activities.</p>
                        </div>
                    </div>

                    <!-- Getting Started Section -->
                    <div class="getting-started-section">
                        <h2 class="text-2xl font-bold text-white mb-6">Getting Started</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-indigo-600 rounded-full h-8 w-8 flex items-center justify-center text-white font-bold mr-4 flex-shrink-0">
                                    1
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-1">Create an Account</h3>
                                    <p class="text-indigo-200">Register to get access to all features. If you already have an account, simply log in.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-indigo-600 rounded-full h-8 w-8 flex items-center justify-center text-white font-bold mr-4 flex-shrink-0">
                                    2
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-1">Submit a Ticket</h3>
                                    <p class="text-indigo-200">Create a new ticket with details about your issue. Add a clear title and thorough description.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-indigo-600 rounded-full h-8 w-8 flex items-center justify-center text-white font-bold mr-4 flex-shrink-0">
                                    3
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-1">Track Progress</h3>
                                    <p class="text-indigo-200">Monitor your ticket status, add comments, and receive notifications as updates occur.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tech Stack -->
                    <div class="tech-stack">
                        <div class="text-center text-white">
                            <i class="fab fa-laravel tech-icon"></i>
                            <p class="tech-label text-white">Laravel {{ laravelVersion }}</p>
                        </div>
                        <div class="text-cente">
                            <i class="fab fa-vuejs tech-icon"></i>
                            <p class="tech-label text-white">Vue.js 3</p>
                        </div>
                        <div class="text-cente">
                             <i class="fab fa-php tech-icon"></i>
                            <p class="tech-label text-white">PHP {{ phpVersion }}</p>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-npm tech-icon"></i>
                            <p class="tech-label text-white">Inertia.js</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="landing-footer ">
                        <p class="text-white">Support Ticket System &copy; {{ new Date().getFullYear() }}</p>
                        <p class="mt-2 text-white">Made with <i class="fas fa-heart text-red-500"></i> by Team Swiffy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
