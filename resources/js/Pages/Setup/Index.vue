<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { useSetupStore } from '@/Stores/setupStore';

// Props passed from the controller
const props = defineProps({
    envInfo: Object,
    dbConfig: Object,
    mailConfig: Object
});

// Use the setup store
const setupStore = useSetupStore();

// Environment check for dev mode
const isDevelopment = ref(import.meta.env.DEV);

// Define the setup steps
const steps = [
    { id: 1, title: 'Requirements Check', icon: 'check-circle', completed: false, current: true },
    { id: 2, title: 'Database Configuration', icon: 'database', completed: false, current: false },
    { id: 3, title: 'Database Migrations', icon: 'table', completed: false, current: false },
    { id: 4, title: 'Mail Configuration', icon: 'envelope', completed: false, current: false },
    { id: 5, title: 'Finalize Setup', icon: 'flag-checkered', completed: false, current: false },
];

// Reference store values
const currentStep = computed(() => setupStore.currentStep);
const status = computed(() => setupStore.status);
const dbForm = computed({
    get: () => setupStore.dbForm,
    set: (value) => setupStore.updateDbForm(value)
});
const mailForm = computed({
    get: () => setupStore.mailForm,
    set: (value) => setupStore.updateMailForm(value)
});
const migrationOutput = computed({
    get: () => setupStore.migrationOutput,
    set: (value) => setupStore.setMigrationOutput(value)
});

// Helper refs
const loading = ref(false);
const error = ref(null);

// Update steps based on current step
const updateStepsState = () => {
    // Reset all steps
    steps.forEach(step => {
        step.completed = false;
        step.current = false;
    });
    
    // Mark completed steps
    for (let i = 0; i < currentStep.value - 1; i++) {
        if (steps[i]) {
            steps[i].completed = true;
        }
    }
    
    // Mark current step
    if (steps[currentStep.value - 1]) {
        steps[currentStep.value - 1].current = true;
    }
};

// Watch for changes to save to localStorage
watch(currentStep, (newValue) => {
    setupStore.setCurrentStep(newValue);
    updateStepsState();
}, { immediate: true });

watch(dbForm, (newValue) => {
    setupStore.updateDbForm(newValue);
}, { deep: true });

watch(mailForm, (newValue) => {
    setupStore.updateMailForm(newValue);
}, { deep: true });

watch(migrationOutput, (newValue) => {
    setupStore.setMigrationOutput(newValue);
});

// Initialize steps state on mount
onMounted(() => {
    updateStepsState();
    
    // Update forms with initial values from props if they're not already set
    if (!dbForm.value.database) {
  
        setupStore.updateDbForm({
            connection: props.dbConfig.connection,
            host: props.dbConfig.host,
            port: props.dbConfig.port,
            database: props.dbConfig.database,
            username: props.dbConfig.username,
            password: props.dbConfig.password
        });
    }
    
    if (!mailForm.value.username) {
        setupStore.updateMailForm({
            host: props.mailConfig.host,
            port: props.mailConfig.port,
            username: props.mailConfig.username,
            password: props.mailConfig.password,
            encryption: props.mailConfig.encryption,
            from_address: props.mailConfig.from_address,
            from_name: props.mailConfig.from_name
        });
    }
});

// Check if all requirements are met
const requirementsAllMet = computed(() => {
    const requirements = props.envInfo.requirements;
    const writablePaths = props.envInfo.writablePaths;
    
    // Check all requirements and writable paths
    return Object.values(requirements).every(req => req === true) && 
           Object.values(writablePaths).every(path => path === true);
});

// Go to next step
const nextStep = () => {
    setupStore.setCurrentStep(currentStep.value + 1);
};

// Go to previous step
const prevStep = () => {
    setupStore.setCurrentStep(currentStep.value - 1);
};

// Test database connection
const testDatabaseConnection = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/setup/test-database', dbForm.value);
        if (response.data.success) {
            setupStore.updateStatus('database', true);
            nextStep();
        } else {
            error.value = response.data.message;
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while testing the database connection.';
    } finally {
        loading.value = false;
    }
};

// Save database configuration
const saveDatabase = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/setup/save-database', dbForm.value);
        if (response.data.success) {
            setupStore.updateStatus('database', true);
            nextStep();
        } else {
            error.value = response.data.message;
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while saving the database configuration.';
    } finally {
        loading.value = false;
    }
};

// Run migrations
const runMigrations = async () => {
    loading.value = true;
    error.value = null;
    setupStore.setMigrationOutput('');
    
    try {
        const response = await axios.post('/setup/run-migrations', { reset: true });
        setupStore.setMigrationOutput(response.data.output);
        
        if (response.data.success) {
            setupStore.updateStatus('migration', true);
            nextStep();
        } else {
            error.value = 'Migration failed. Please check the output log.';
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while running migrations.';
        setupStore.setMigrationOutput(e.response?.data?.output || '');
    } finally {
        loading.value = false;
    }
};

// Test mail connection
const testMailConnection = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/setup/test-mail', mailForm.value);
        if (response.data.success) {
            setupStore.updateStatus('mail', true);
            nextStep();
        } else {
            error.value = response.data.message;
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while testing the mail connection.';
    } finally {
        loading.value = false;
    }
};

// Save mail configuration
const saveMail = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/setup/save-mail', mailForm.value);
        if (response.data.success) {
            setupStore.updateStatus('mail', true);
            nextStep();
        } else {
            error.value = response.data.message;
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while saving the mail configuration.';
    } finally {
        loading.value = false;
    }
};

// Finalize setup
const finalizeSetup = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/setup/finalize');
        if (response.data.success) {
            setupStore.updateStatus('finalize', true);
            setupStore.markSetupCompleted();
            
            // Redirect to welcome page
            setTimeout(() => {
                router.visit('/');
            }, 1500);
        } else {
            error.value = response.data.message;
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while finalizing the setup.';
    } finally {
        loading.value = false;
    }
};

// Get icon classes for a step
const getStepIconClasses = (step) => {
    let classes = 'fas fa-' + step.icon + ' text-xl';
    
    if (step.completed) {
        classes += ' text-green-500';
    } else if (step.current) {
        classes += ' text-blue-500 animate-pulse';
    } else {
        classes += ' text-gray-400';
    }
    
    return classes;
};

// Function to reset setup (for testing/debugging)
const resetSetup = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        // Reset the database first
        const response = await axios.post('/setup/run-migrations', { reset: true });
        setupStore.setMigrationOutput(response.data.output || '');
        
        // Reset the store state
        setupStore.resetState();
        
        // Reset forms to initial values
        setupStore.updateDbForm({
            connection: props.dbConfig.connection,
            host: props.dbConfig.host,
            port: props.dbConfig.port,
            database: props.dbConfig.database,
            username: props.dbConfig.username,
            password: props.dbConfig.password
        });
        
        setupStore.updateMailForm({
            host: props.mailConfig.host,
            port: props.mailConfig.port,
            username: props.mailConfig.username,
            password: props.mailConfig.password,
            encryption: props.mailConfig.encryption,
            from_address: props.mailConfig.from_address,
            from_name: props.mailConfig.from_name
        });
        
        // Show success message
        error.value = null;
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred while resetting the setup.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Setup Support Ticket System" />

    <div class="setup-page">
        <div class="setup-container">
            <!-- Header -->
            <div class="setup-header">
                <div class="setup-header-bg">
                    <div class="setup-header-circle-1"></div>
                    <div class="setup-header-circle-2"></div>
                    <div class="setup-header-circle-3"></div>
                </div>
                <h1 class="text-3xl font-bold flex items-center text-white">
                    <i class="fas fa-ticket-alt mr-3 mt-1"></i>
                    Support Ticket System Setup
                </h1>
                <p class="mt-2 text-indigo-100 opacity-90 max-w-xl">
                    Welcome to the installation wizard. Follow the steps below to set up your support ticket system.
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-steps">
                    <div v-for="(step, index) in steps" :key="step.id" class="flex flex-col items-center">
                        <div class="relative">
                            <div :class="[
                                'step-indicator',
                                step.completed ? 'step-indicator-completed' : 
                                step.current ? 'step-indicator-current' : 
                                'step-indicator-pending'
                            ]">
                                <i :class="getStepIconClasses(step)"></i>
                            </div>
                            
                            <!-- Connection line between steps -->
                            <div v-if="index < steps.length - 1" 
                                 :class="['step-line', { 'step-line-completed': step.completed }]">
                            </div>
                        </div>
                        <span :class="[
                            'step-title',
                            {
                                'step-title-completed': step.completed,
                                'step-title-current': step.current && !step.completed,
                                'step-title-pending': !step.current && !step.completed
                            }
                        ]">{{ step.title }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="p-6">
                <!-- Status Messages -->
                <div v-if="error" class="error-message">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ error }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Loading Indicator -->
                <div v-if="loading" class="loading-indicator">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Processing...</span>
                    </div>
                </div>
                
                <!-- Migration Output -->
                <div v-if="migrationOutput" class="migration-output">
                    <pre>{{ migrationOutput }}</pre>
                </div>

                <!-- Step Content -->
                <div v-if="currentStep === 1" class="animate-fadeIn">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">System Requirements</h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center pb-2 mb-2 border-b border-gray-200">
                            <span class="font-medium">PHP Version</span>
                            <span :class="props.envInfo.requirements.php ? 'text-green-600' : 'text-red-600'">
                                {{ props.envInfo.phpVersion }}
                                <i :class="props.envInfo.requirements.php ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                            </span>
                        </div>
                        
                        <div v-for="(value, requirement) in props.envInfo.requirements" :key="requirement" 
                             class="flex justify-between items-center pb-2 mb-2 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0">
                            <span class="font-medium capitalize">{{ requirement }}</span>
                            <span :class="value ? 'text-green-600' : 'text-red-600'">
                                <i :class="value ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                            </span>
                        </div>
                    </div>
                    
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Directory Permissions</h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div v-for="(value, path) in props.envInfo.writablePaths" :key="path" 
                             class="flex justify-between items-center pb-2 mb-2 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0">
                            <span class="font-medium">{{ path }}</span>
                            <span :class="value ? 'text-green-600' : 'text-red-600'">
                                <i :class="value ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 2" class="animate-fadeIn">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Database Configuration</h2>
                    
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Database Connection</label>
                            <select v-model="dbForm.connection" class="form-select w-full rounded-md border-gray-300 shadow-sm">
                                <option value="mysql">MySQL</option>
                                <option value="pgsql">PostgreSQL</option>
                                <option value="sqlite">SQLite</option>
                                <option value="sqlsrv">SQL Server</option>
                            </select>
                        </div>
                        
                        <div v-if="dbForm.connection !== 'sqlite'" class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Host</label>
                            <input type="text" v-model="dbForm.host" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="localhost">
                        </div>
                        
                        <div v-if="dbForm.connection !== 'sqlite'" class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                            <input type="text" v-model="dbForm.port" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="3306">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                            <input type="text" v-model="dbForm.database" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="laravel">
                        </div>
                        
                        <div v-if="dbForm.connection !== 'sqlite'" class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" v-model="dbForm.username" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="root">
                        </div>
                        
                        <div v-if="dbForm.connection !== 'sqlite'" class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" v-model="dbForm.password" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="Password">
                        </div>
                    </div>
                    
                    <button @click="testDatabaseConnection" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 mr-2">
                        <i class="fas fa-database mr-2"></i> Test Connection
                    </button>
                </div>

                <div v-if="currentStep === 3" class="animate-fadeIn">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Database Migrations</h2>
                    <div class="info-box">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Click the button below to run migrations and seed the database. This will create all necessary tables for your application.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 4" class="animate-fadeIn">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Mail Configuration</h2>
                    
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Configure mail settings for your application. We recommend using Mailtrap for testing.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                            <input type="text" v-model="mailForm.host" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="smtp.mailtrap.io">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                            <input type="text" v-model="mailForm.port" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="2525">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mail Username</label>
                            <input type="text" v-model="mailForm.username" class="form-input w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mail Password</label>
                            <input type="password" v-model="mailForm.password" class="form-input w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                            <select v-model="mailForm.encryption" class="form-select w-full rounded-md border-gray-300 shadow-sm">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="null">None</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                            <input type="email" v-model="mailForm.from_address" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="no-reply@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                            <input type="text" v-model="mailForm.from_name" class="form-input w-full rounded-md border-gray-300 shadow-sm" placeholder="Support Ticket System">
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 5" class="animate-fadeIn">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Finalize Setup</h2>
                    <div class="success-box">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    Great job! You've configured all necessary settings for your Support Ticket System.
                                    Click the button below to finalize the setup and start using your application.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="summary-box">
                        <h3 class="font-medium text-gray-700 mb-2">Configuration Summary</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Database:</span> {{ dbForm.connection }} ({{ dbForm.database }})</p>
                            <p><span class="font-medium">Mail Provider:</span> {{ mailForm.host }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="setup-footer">
                <button v-if="currentStep > 1" @click="prevStep" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <div v-else></div>
                
                <div class="flex items-center">
                    <!-- Hidden developer reset button -->
                    <button v-if="isDevelopment" 
                            @click="resetSetup" 
                            type="button"
                            class="text-xs text-gray-400 hover:text-red-500 mr-4">
                        Reset
                    </button>
                    
                    <!-- Action buttons for each step -->
                    <button v-if="currentStep === 1" 
                            @click="nextStep" 
                            :disabled="!requirementsAllMet" 
                            :class="[
                                'px-4 py-2 rounded-md',
                                requirementsAllMet ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-blue-300 text-white cursor-not-allowed'
                            ]">
                        Next <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    
                    <button v-else-if="currentStep === 2" 
                            @click="saveDatabase" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-arrow-right mr-2"></i> Continue
                    </button>
                    
                    <button v-else-if="currentStep === 3" 
                            @click="runMigrations" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Run Migrations <i class="fas fa-database ml-2"></i>
                    </button>
                    
                    <button v-else-if="currentStep === 4" 
                            @click="saveMail" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-arrow-right mr-2"></i> Continue
                    </button>
                    
                    <button v-else-if="currentStep === 5" 
                            @click="finalizeSetup" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Complete Setup <i class="fas fa-check ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Footer credit -->
        <div class="footer-credit">
            Support Ticket System &copy; {{ new Date().getFullYear() }} • Powered by Laravel & Vue
        </div>
        
    </div>
</template>
