<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted, nextTick, onUnmounted } from 'vue';

const props = defineProps({
    ticket: Object,
    statuses: Object,
    comments: Array,
});

const form = useForm({
    status: props.ticket.status,
});

const commentForm = useForm({
    content: '',
});

const isStatusFormVisible = ref(false);
const ticketContainerRef = ref(null);
const commentInputRef = ref(null);
const showFloatingButton = ref(false);
const isDescriptionExpanded = ref(false);
const isAnimating = ref(true);
const statusFormRef = ref(null);

const toggleStatusForm = (event) => {
    event.stopPropagation();
    isStatusFormVisible.value = !isStatusFormVisible.value;
};

const closeStatusForm = (event) => {
    // Check if the click is outside the form
    if (statusFormRef.value && !statusFormRef.value.contains(event.target)) {
        isStatusFormVisible.value = false;
    }
};

onMounted(() => {
    window.addEventListener('scroll', checkScrollPosition);
    document.addEventListener('click', closeStatusForm);
    
    // Set initial animation state
    setTimeout(() => {
        isAnimating.value = false;
    }, 2000);
});

const checkScrollPosition = () => {
    if (!ticketContainerRef.value) return;
    
    const rect = ticketContainerRef.value.getBoundingClientRect();
    showFloatingButton.value = rect.bottom < 0;
};

const scrollToComments = () => {
    const commentsSection = document.getElementById('comments-section');
    if (commentsSection) {
        commentsSection.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            if (commentInputRef.value) {
                commentInputRef.value.focus();
            }
        }, 600);
    }
};

const submit = () => {
    form.patch(route('tickets.update', props.ticket.id));
    isStatusFormVisible.value = false;
};

const submitComment = () => {
    commentForm.post(route('tickets.comments.store', props.ticket.id), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
        },
    });
};

const toggleDescription = () => {
    isDescriptionExpanded.value = !isDescriptionExpanded.value;
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

const getStatusIcon = (status) => {
    return status === 'open' 
        ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'  // Open icon
        : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'; // Closed icon
};

const getTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'just now';
    
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
    
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
    
    const days = Math.floor(hours / 24);
    if (days < 30) return `${days} ${days === 1 ? 'day' : 'days'} ago`;
    
    const months = Math.floor(days / 30);
    if (months < 12) return `${months} ${months === 1 ? 'month' : 'months'} ago`;
    
    const years = Math.floor(months / 12);
    return `${years} ${years === 1 ? 'year' : 'years'} ago`;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Clean up event listeners
onUnmounted(() => {
    window.removeEventListener('scroll', checkScrollPosition);
    document.removeEventListener('click', closeStatusForm);
});
</script>

<template>
    <Head title="Ticket Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center animate-fade-in">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <span class="mr-2 bg-indigo-100 text-indigo-800 px-2 py-1 rounded-md">#{{ ticket.id }}</span>
                    <span class="ml-2 truncate">{{ ticket.title }}</span>
                </h2>
                <Link :href="route('tickets.index')" class="custom-button inline-flex items-center bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Tickets
                </Link>
            </div>
        </template>

        <!-- Floating Action Button -->
        <div class="fixed bottom-8 right-8 z-50" v-show="showFloatingButton">
            <button @click="scrollToComments" class="flex items-center justify-center w-14 h-14 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 transition-all duration-300 transform hover:scale-110 hover:rotate-3 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </button>
        </div>

        <div class="py-12 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" ref="ticketContainerRef">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" 
                     class="fixed top-24 right-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg shadow-lg animate-notification z-50 max-w-md">
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

                <!-- Ticket Details Card -->
                <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-100 backdrop-filter backdrop-blur-lg animate-slide-up"
                     :class="{ 'animate-pulse-subtle': isAnimating }">
                    <div class="relative">
                        <!-- Decorative Header Bar -->
                        <div class="h-2 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                        <div class="p-8">
                            <!-- Ticket Header -->
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between space-y-4 md:space-y-0">
                                <div class="flex-1">
                                    <h1 class="text-3xl font-bold text-gray-900 mb-4 hover-lift">{{ ticket.title }}</h1>
                                <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-3 hover-lift relative group cursor-pointer">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white text-xl font-semibold shadow-lg transform group-hover:scale-110 transition-all duration-300">
                                                {{ ticket.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                            <div>
                                            <p class="text-sm font-medium text-gray-900">{{ ticket.user.name }}</p>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="text-xs text-gray-500">{{ getTimeAgo(ticket.created_at) }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Floating tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 z-10">
                                                Created on {{ formatDate(ticket.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status and Priority Badges -->
                            <div class="flex flex-wrap gap-3">
                                    <div class="relative">
                                        <span :class="{
                                            'status-badge flex items-center transition-all duration-300 cursor-pointer': true,
                                            'status-open': ticket.status === 'open',
                                            'status-closed': ticket.status === 'closed'
                                        }"
                                        @click="toggleStatusForm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(ticket.status)" />
                                            </svg>
                                            Status: {{ ticket.status === 'open' ? 'Open' : 'Closed' }}
                                        </span>
                                        
                                        <!-- Floating Status Form -->
                                        <div v-show="isStatusFormVisible" 
                                             ref="statusFormRef"
                                             class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-xl border border-gray-100 p-4 transform transition-all duration-300 animate-pop-up z-10">
                                            <div class="flex justify-between items-center mb-3">
                                                <p class="text-xs text-gray-500">Update ticket status:</p>
                                                <button @click="toggleStatusForm" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <form @submit.prevent="submit">
                                                <div class="flex flex-col space-y-3">
                                                    <div v-for="(label, value) in statuses" :key="value"
                                                         class="flex items-center space-x-2">
                                                        <input type="radio" 
                                                               :id="value" 
                                                               :value="value" 
                                                               v-model="form.status"
                                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" />
                                                        <label :for="value" class="text-sm text-gray-700">{{ label }}</label>
                                                    </div>
                                                </div>
                                                <button type="submit" 
                                                        class="mt-3 w-full px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                                    Update
                                                </button>
                                            </form>
                            </div>
                        </div>

                                    <span :class="['priority-badge flex items-center transform hover:scale-105 transition-all duration-200', getPriorityColor(ticket.priority)]">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Priority: {{ ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Ticket Description -->
                            <div class="mt-8 p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover-lift">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    Description
                                </h3>
                                <div :class="{ 'max-h-40 overflow-hidden': !isDescriptionExpanded && ticket.description.length > 300 }" class="relative">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ ticket.description }}</p>
                                    
                                    <!-- Fade gradient -->
                                    <div v-if="!isDescriptionExpanded && ticket.description.length > 300" 
                                         class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white to-transparent"></div>
                        </div>

                                <!-- Expand button -->
                                <button v-if="ticket.description.length > 300" 
                                        @click="toggleDescription" 
                                        class="mt-3 text-sm text-indigo-600 font-medium hover:text-indigo-800 transition-colors duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path v-if="!isDescriptionExpanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    {{ isDescriptionExpanded ? 'Show less' : 'Read more' }}
                                </button>
                            </div>

                            <!-- Comments Section -->
                            <div id="comments-section" class="mt-12">
                                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Comments <span class="ml-2 text-sm bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">{{ comments?.length || 0 }}</span>
                                </h3>

                                <!-- Comment Form -->
                                <form @submit.prevent="submitComment" class="mb-8 transform hover:scale-[1.01] transition-all duration-200 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                                    <div class="mb-4">
                                        <textarea
                                            ref="commentInputRef"
                                            v-model="commentForm.content"
                                            rows="3"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-all duration-200"
                                            placeholder="Add your thoughts..."
                                            required
                                        ></textarea>
                                        <InputError :message="commentForm.errors.content" class="mt-2" />
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="glass-button"
                                            :class="{ 'opacity-75 cursor-not-allowed': commentForm.processing }"
                                            :disabled="commentForm.processing"
                                        >
                                            <svg v-if="!commentForm.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                            {{ commentForm.processing ? 'Posting...' : 'Send' }}
                                        </button>
                                    </div>
                                </form>

                                <!-- Comments List -->
                                <div class="space-y-6">
                                    <div v-if="comments?.length === 0" class="text-center py-12 bg-gray-50 rounded-xl">
                                        <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-lg">No comments yet. Start the conversation!</p>
                                    </div>
                                    <div v-for="(comment, index) in comments" 
                                         :key="comment.id" 
                                         class="flex space-x-4 animate-slide-up"
                                         :style="{ animationDelay: `${0.05 * index}s` }">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white text-sm font-semibold shadow-lg transform hover:scale-110 transition-transform duration-200">
                                                {{ comment.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="comment-card">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="font-medium text-gray-900">{{ comment.user.name }}</div>
                                                    <div class="text-xs text-gray-500 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ getTimeAgo(comment.created_at) }}
                                                    </div>
                                                </div>
                                                <p class="text-gray-700 whitespace-pre-line">{{ comment.content }}</p>
                                            </div>
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