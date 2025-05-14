<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    priorities: Object,
});

const form = useForm({
    title: '',
    description: '',
    priority: '',
});

const submit = () => {
    form.post(route('tickets.store'));
};
</script>

<template>
    <Head title="Create Ticket" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center animate-fade-in">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Support Ticket</h2>
                <Link :href="route('tickets.index')" class="custom-button inline-flex items-center bg-gray-600 hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Tickets
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg animate-slide-up">
                    <div class="p-8 text-gray-900">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-8 text-center">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Create New Support Ticket</h3>
                                <p class="text-gray-600">Fill out the form below to submit your support request</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6 animate-fade-in">
                                <div class="custom-card hover-scale">
                                    <div class="mb-6">
                                        <InputLabel for="title" value="Title" class="text-lg font-medium" />
                                        <div class="mt-2 relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </div>
                                            <TextInput
                                                id="title"
                                                type="text"
                                                class="custom-input pl-10"
                                                v-model="form.title"
                                                required
                                                autofocus
                                                placeholder="Enter a descriptive title for your ticket"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>

                                    <div class="mb-6">
                                        <InputLabel for="description" value="Description" class="text-lg font-medium" />
                                        <div class="mt-2 relative">
                                            <div class="absolute left-3 top-3 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                </svg>
                                            </div>
                                            <textarea
                                                id="description"
                                                class="custom-input pl-12"
                                                v-model="form.description"
                                                rows="6"
                                                required
                                                placeholder="Please provide detailed information about your issue"
                                                style="padding-left: 2.5rem"
                                            ></textarea>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <div>
                                        <InputLabel for="priority" value="Priority" class="text-lg font-medium" />
                                        <div class="mt-2 relative">
                                            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <select
                                                id="priority"
                                                class="custom-select pl-12"
                                                v-model="form.priority"
                                                required
                                                style="padding-left: 2.5rem"
                                            >
                                                <option value="" disabled>Select a priority level</option>
                                                <option v-for="(label, value) in priorities" :key="value" :value="value">
                                                    {{ label }}
                                                </option>
                                            </select>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.priority" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-end">
                                    <PrimaryButton class="custom-button" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Creating...' : 'Create Ticket' }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-button {
    @apply px-4 py-2 text-white text-sm font-medium rounded-md shadow-sm transition-all duration-200 transform hover:scale-105 active:scale-95;
}

.custom-card {
    @apply bg-white p-6 rounded-lg shadow-sm border border-gray-100 transition-all duration-200;
}

.hover-scale {
    @apply hover:shadow-md transform transition-all duration-200;
}

.custom-input {
    @apply block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 transition-colors duration-200;
}

.custom-select {
    @apply block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 transition-colors duration-200;
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

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

/* Loading animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style> 