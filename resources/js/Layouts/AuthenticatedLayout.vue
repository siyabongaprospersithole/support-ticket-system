<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const isScrolled = ref(false);
const isPageLoaded = ref(false);

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    isPageLoaded.value = true;
    return () => window.removeEventListener('scroll', handleScroll);
});

const handleScroll = () => {
    isScrolled.value = window.scrollY > 0;
};
</script>

<template>
    <div class="min-h-screen flex flex-col">
            <nav :class="['fixed w-full z-50 transition-all duration-300 ease-in-out', 
                         isScrolled ? 'bg-white/95 backdrop-blur-sm shadow-lg' : 'bg-white/80 backdrop-blur-sm']">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')" 
                                      class="flex items-center transform hover:scale-105 transition-all duration-200 hover:opacity-80">
                                    <!-- Animated Swiffy Logo SVG -->
                                    <div class="h-8 w-auto mr-2 animate-fade-in">
                                        <svg viewBox="0 0 100 50" class="w-full h-full filter drop-shadow-md">
                                            <path d="M20.5,15 C15,15 10.5,19.5 10.5,25 C10.5,30.5 15,35 20.5,35 L25,35 C27.5,35 29.5,33 29.5,30.5 C29.5,28 27.5,26 25,26 L22.5,26 C21.5,26 20.5,25 20.5,24 C20.5,23 21.5,22 22.5,22 L35,22 C37.5,22 39.5,20 39.5,17.5 C39.5,16 38.5,15 37.5,15 L20.5,15 Z" fill="url(#gradient1)" class="animate-path"/>
                                            <path d="M42.5,15 C40,15 38,17 38,19.5 C38,22 40,24 42.5,24 L57.5,24 C58.5,24 59.5,25 59.5,26 C59.5,27 58.5,28 57.5,28 L45,28 C42.5,28 40.5,30 40.5,32.5 C40.5,34 41.5,35 42.5,35 L57.5,35 C63,35 67.5,30.5 67.5,25 C67.5,19.5 63,15 57.5,15 L42.5,15 Z" fill="url(#gradient2)" class="animate-path"/>
                                            <path d="M72.5,15 C70,15 68,17 68,19.5 C68,22 70,24 72.5,24 L77.5,24 C78.5,24 79.5,25 79.5,26 C79.5,27 78.5,28 77.5,28 L70,28 C67.5,28 65.5,30 65.5,32.5 C65.5,34 66.5,35 67.5,35 L77.5,35 C83,35 87.5,30.5 87.5,25 C87.5,19.5 83,15 77.5,15 L72.5,15 Z" fill="url(#gradient3)" class="animate-path"/>
                                            <defs>
                                                <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#1455C0" />
                                                    <stop offset="100%" style="stop-color:#36a7f6" />
                                                </linearGradient>
                                                <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#1455C0" />
                                                    <stop offset="100%" style="stop-color:#36a7f6" />
                                                </linearGradient>
                                                <linearGradient id="gradient3" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#1455C0" />
                                                    <stop offset="100%" style="stop-color:#36a7f6" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-primary-600 hidden md:block tracking-wide transition-all duration-200">
                                        Support System
                                    </span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink v-for="(link, index) in [
                                    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                                    { name: 'Tickets', route: 'tickets.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
                                    { name: 'New Ticket', route: 'tickets.create', icon: 'M12 4v16m8-8H4' },
                                    { name: 'Activities', route: 'activities.index', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }
                                ]"
                                :key="index"
                                :href="route(link.route)"
                                :active="route().current(link.route)"
                                class="relative flex items-center px-1 pt-1 text-sm font-medium transition-all duration-200 ease-in-out group"
                                :style="{ animationDelay: `${index * 100}ms` }">
                                    <svg class="w-5 h-5 mr-1.5 transition-all duration-200" 
                                         :class="route().current(link.route) ? 'text-primary-500' : 'text-gray-400 group-hover:text-primary-500'"
                                         fill="none" 
                                         stroke="currentColor" 
                                         viewBox="0 0 24 24">
                                        <path :d="link.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                    </svg>
                                    <span :class="route().current(link.route) ? 'text-primary-600' : 'text-gray-500 group-hover:text-primary-600'">
                                        {{ link.name }}
                                    </span>
                                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200 ease-out"></span>
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full text-gray-600 bg-white border border-gray-200 hover:bg-primary-500 hover:text-white hover:border-primary-500 focus:outline-none transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 shadow-sm hover:shadow-md">
                                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                </svg>
                                                {{ $page.props.auth.user.name }}
                                                <svg class="ms-2 -me-0.5 h-4 w-4 transition-transform duration-200 group-hover:rotate-180"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                     fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                          clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden divide-y divide-gray-100">
                                            <div class="px-4 py-3">
                                                <p class="text-sm text-gray-900">Signed in as</p>
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $page.props.auth.user.email }}</p>
                                            </div>
                                            <div class="py-1">
                                                <DropdownLink :href="route('profile.edit')"
                                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-150 group">
                                                    <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                    </svg>
                                                    Profile
                                                </DropdownLink>
                                                <DropdownLink :href="route('logout')"
                                                            method="post"
                                                            as="button"
                                                            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-150 group">
                                                    <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                    </svg>
                                            Log Out
                                        </DropdownLink>
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-primary-500 hover:bg-primary-50 focus:outline-none transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95">
                                <svg class="h-6 w-6 transition-transform duration-200"
                                     :class="{ 'rotate-180': showingNavigationDropdown }"
                                     stroke="currentColor"
                                     fill="none"
                                     viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'transform translate-y-0 opacity-100': showingNavigationDropdown, 'transform -translate-y-full opacity-0': !showingNavigationDropdown }"
                     class="sm:hidden fixed w-full bg-white/95 backdrop-blur-sm transition-all duration-200 ease-in-out">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink v-for="(link, index) in [
                            { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                            { name: 'Tickets', route: 'tickets.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
                            { name: 'New Ticket', route: 'tickets.create', icon: 'M12 4v16m8-8H4' },
                            { name: 'Activities', route: 'activities.index', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }
                        ]"
                        :key="index"
                        :href="route(link.route)"
                        :active="route().current(link.route)"
                        class="flex items-center px-4 py-2 text-base font-medium transition-colors duration-150"
                        :class="[
                            route().current(link.route) 
                                ? 'text-primary-600 bg-primary-50' 
                                : 'text-gray-600 hover:text-primary-600 hover:bg-primary-50'
                        ]"
                        :style="{ animationDelay: `${index * 100}ms` }">
                            <svg class="w-5 h-5 mr-2" 
                                 :class="route().current(link.route) ? 'text-primary-500' : 'text-gray-400'"
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path :d="link.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                            {{ link.name }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center px-4 py-3">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-gray-400 bg-gray-200 rounded-full p-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"
                                             class="flex items-center px-4 py-2 text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')"
                                             method="post"
                                             as="button"
                                             class="flex items-center w-full text-left px-4 py-2 text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" 
                    class="pt-16 bg-white shadow-sm"
                    :class="{ 'opacity-0 translate-y-4': !isPageLoaded, 'opacity-100 translate-y-0': isPageLoaded }"
                    style="transition: all 0.5s ease-out;">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main :class="{ 'opacity-0': !isPageLoaded, 'opacity-100': isPageLoaded }"
                  style="transition: opacity 0.5s ease-out; transition-delay: 0.2s;"
              class="flex-grow ">
                <slot />
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100 py-8 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <svg viewBox="0 0 100 50" class="w-8 h-8">
                                <path d="M20.5,15 C15,15 10.5,19.5 10.5,25 C10.5,30.5 15,35 20.5,35 L25,35 C27.5,35 29.5,33 29.5,30.5 C29.5,28 27.5,26 25,26 L22.5,26 C21.5,26 20.5,25 20.5,24 C20.5,23 21.5,22 22.5,22 L35,22 C37.5,22 39.5,20 39.5,17.5 C39.5,16 38.5,15 37.5,15 L20.5,15 Z" fill="url(#gradient1)" class="opacity-50"/>
                            </svg>
                            <p class="text-sm text-gray-600">
                                &copy; {{ new Date().getFullYear() }} Swiffy. All rights reserved.
                            </p>
                        </div>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center text-sm text-gray-600 hover:text-primary-600 transition-colors duration-200 cursor-pointer group">
                                <svg class="w-5 h-5 text-primary-500 mr-2 transform group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Security Guaranteed
                            </div>
                            <div class="flex items-center text-sm text-gray-600 hover:text-primary-600 transition-colors duration-200 cursor-pointer group">
                                <svg class="w-5 h-5 text-primary-500 mr-2 transform group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                24/7 Support
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
    </div>
</template>

<style>
/* Base Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes pathDraw {
    from { stroke-dashoffset: 100; }
    to { stroke-dashoffset: 0; }
}

/* Utility Classes */
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.animate-slide-in {
    animation: slideIn 0.5s ease-out;
}

.animate-path {
    stroke-dasharray: 100;
    animation: pathDraw 1.5s ease-out forwards;
}

/* Active Navigation Styling */
.active-link {
    @apply border-b-2 border-primary-500 text-primary-600;
}

:deep(.active) {
    @apply text-primary-600 font-medium;
    position: relative;
}

:deep(.active)::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #1455C0;
    transform: scaleX(1);
    transition: transform 0.2s ease-in-out;
}

/* Hover Effects */
.nav-link {
    position: relative;
    transition: all 0.2s ease-in-out;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #1455C0;
    transform: scaleX(0);
    transition: transform 0.2s ease-in-out;
}

.nav-link:hover::after {
    transform: scaleX(1);
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #1455C0;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #0f3d8c;
}

/* Card and Button Styles */
.card {
    @apply bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-all duration-200 transform hover:scale-105 active:scale-95;
}

.btn-primary {
    @apply bg-primary-500 text-white hover:bg-primary-600 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2;
}

.btn-secondary {
    @apply bg-white text-primary-600 border border-primary-500 hover:bg-primary-50;
}

/* Input Styles */
.form-input {
    @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50;
}

/* Table Styles */
.table {
    @apply min-w-full divide-y divide-gray-200;
}

.table th {
    @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.table td {
    @apply px-6 py-4 whitespace-nowrap text-sm text-gray-500;
}

/* Badge Styles */
.badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.badge-success {
    @apply bg-green-100 text-green-800;
}

.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}

.badge-error {
    @apply bg-red-100 text-red-800;
}

/* Tooltip Styles */
.tooltip {
    @apply relative inline-block;
}

.tooltip-text {
    @apply invisible absolute z-10 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm opacity-0 -translate-y-1 transition-all duration-200;
}

.tooltip:hover .tooltip-text {
    @apply visible opacity-100 translate-y-0;
}

/* Layout Styles */
.min-h-screen {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.flex-grow {
    flex-grow: 1;
}
</style>
