import { defineStore } from 'pinia';

export const useSetupStore = defineStore('setup', {
    state: () => {
        // Load state from localStorage if available
        const savedState = localStorage.getItem('setupState');
        
        // Default state
        const defaultState = {
            currentStep: 1,
            status: {
                requirements: false,
                database: false,
                migration: false,
                mail: false,
                finalize: false
            },
            dbForm: {
                connection: 'mysql',
                host: '127.0.0.1',
                port: '3306',
                database: '',
                username: '',
                password: '',
            },
            mailForm: {
                host: 'smtp.mailtrap.io',
                port: '2525',
                username: '',
                password: '',
                encryption: 'tls',
                from_address: 'support@example.com',
                from_name: 'Support Ticket System'
            },
            migrationOutput: '',
            setupCompleted: false,
        };
        
        // Return saved state or default state
        return savedState ? JSON.parse(savedState) : defaultState;
    },
    
    actions: {
        setCurrentStep(step) {
            this.currentStep = step;
            this.saveState();
        },
        
        updateStatus(key, value) {
            this.status[key] = value;
            this.saveState();
        },
        
        updateDbForm(form) {
            this.dbForm = { ...this.dbForm, ...form };
            this.saveState();
        },
        
        updateMailForm(form) {
            this.mailForm = { ...this.mailForm, ...form };
            this.saveState();
        },
        
        setMigrationOutput(output) {
            this.migrationOutput = output;
            this.saveState();
        },
        
        markSetupCompleted() {
            this.setupCompleted = true;
            this.saveState();
        },
        
        resetState() {
            localStorage.removeItem('setupState');
            window.location.reload();
        },
        
        saveState() {
            localStorage.setItem('setupState', JSON.stringify({
                currentStep: this.currentStep,
                status: this.status,
                dbForm: this.dbForm,
                mailForm: this.mailForm,
                migrationOutput: this.migrationOutput,
                setupCompleted: this.setupCompleted
            }));
        }
    }
}); 