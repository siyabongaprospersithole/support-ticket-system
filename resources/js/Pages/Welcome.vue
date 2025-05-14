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

    <div class="relative min-h-screen bg-gradient-to-br from-indigo-900 to-blue-900 flex justify-center">
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
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-300 backdrop-blur-sm">
                    Dashboard
                </Link>

                <template v-else>
                    <a :href="route('login')" class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-300 backdrop-blur-sm">
                        Log in
                    </a>

                    <a v-if="canRegister" :href="route('register')" class="ml-4 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-300">
                        Register
                    </a>
                </template>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-32 pb-20">
                <div class="flex flex-col items-center">
                    <!-- Logo -->
                    <div class="rounded-full bg-white p-4 shadow-xl mb-10">
                        <svg class="w-24 h-24 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.5 3.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2l-1.5 1.5L9 2 7.5 3.5 6 2 4.5 3.5 3 2v20l1.5-1.5L6 22l1.5-1.5L9 22l1.5-1.5L12 22l1.5-1.5L15 22l1.5-1.5L18 22l1.5-1.5L21 22V2l-1.5 1.5zM19 19.09H5V4.91h14v14.18zM6 15h12v2H6zm0-4h12v2H6zm0-4h12v2H6z" />
                        </svg>
                    </div>

                    <!-- Title and Subtitle -->
                    <h1 class="text-5xl font-extrabold text-white mb-6 text-center animate-text-shimmer bg-clip-text text-transparent bg-gradient-to-r from-indigo-200 via-blue-100 to-indigo-200 background-animate">
                        Support Ticket System
                    </h1>
                    <p class="text-xl text-indigo-200 text-center max-w-3xl mb-12">
                        A modern, efficient platform for managing support tickets and providing outstanding customer service.
                    </p>

                    <!-- Call to Action -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-16">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="glass-button bg-indigo-500 hover:bg-indigo-600">
                            <i class="fas fa-tachometer-alt mr-2"></i> Go to Dashboard
                        </Link>
                        <Link v-else :href="route('register')" class="glass-button bg-indigo-500 hover:bg-indigo-600">
                            <i class="fas fa-user-plus mr-2"></i> Create an Account
                        </Link>
                        <Link :href="route('login')" class="glass-button bg-white bg-opacity-10 hover:bg-opacity-20">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </Link>
                    </div>

                    <!-- Features Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
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
                    <div class="w-full max-w-4xl rounded-2xl bg-white bg-opacity-10 backdrop-blur-sm p-8 mb-16">
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
                    <div class="flex justify-center items-center space-x-8 mb-16">
                        <div class="text-center">
                            <i class="fab fa-laravel text-4xl text-white mb-2"></i>
                            <p class="text-indigo-200 text-sm">Laravel {{ laravelVersion }}</p>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-vuejs text-4xl text-white mb-2"></i>
                            <p class="text-indigo-200 text-sm">Vue.js 3</p>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-php text-4xl text-white mb-2"></i>
                            <p class="text-indigo-200 text-sm">PHP {{ phpVersion }}</p>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-npm text-4xl text-white mb-2"></i>
                            <p class="text-indigo-200 text-sm">Inertia.js</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center text-indigo-300 text-sm">
                        <p>Support Ticket System &copy; {{ new Date().getFullYear() }}</p>
                        <p class="mt-2">Made with <i class="fas fa-heart text-red-500"></i> by Team Swiffy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Base Animations */
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes delayed-fade-in {
    0% { opacity: 0; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes circle-draw {
    from { stroke-dashoffset: 300; }
    to { stroke-dashoffset: 0; }
}

@keyframes checkmark-draw {
    0% { stroke-dashoffset: 100; }
    60% { stroke-dashoffset: 100; }
    100% { stroke-dashoffset: 0; }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes shimmer {
    0% { background-position: -500px 0; }
    100% { background-position: 500px 0; }
}

.animate-circle {
    stroke-dasharray: 300;
    stroke-dashoffset: 300;
    animation: circle-draw 2s ease forwards;
}

.animate-checkmark {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: checkmark-draw 2s ease forwards;
}

.animate-fade-in {
    animation: fade-in 1s ease forwards;
}

.animate-fade-in-delayed {
    animation: delayed-fade-in 2s ease forwards;
}

.animate-logo {
    animation: pulse 2s infinite;
}

.animate-text-shimmer {
    background-size: 200% 100%;
    animation: shimmer 2s infinite linear;
}

.background-animate {
    background-size: 200%;
}

/* Component Styles */
.glass-button {
    @apply text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-300 transform hover:scale-105 active:scale-100 text-center backdrop-blur-sm;
}

.feature-card {
    @apply p-6 rounded-xl bg-white bg-opacity-10 backdrop-blur-sm transform transition duration-300 hover:translate-y-[-5px] hover:shadow-lg;
}

.feature-icon {
    @apply w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl;
}
</style>
