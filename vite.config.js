import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import setupPlugin from './vite-setup-plugin';

export default defineConfig({
    plugins: [
        setupPlugin(),
        laravel({
            input: 'resources/js/app.js',
            refresh: process.env.VITE_DISABLE_HMR !== 'true',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        hmr: {
            // Disable HMR during setup process
            enabled: process.env.VITE_DISABLE_HMR !== 'true',
        },
        watch: {
            // Completely ignore .env file changes to prevent reloads
            ignored: ['**/.env', '**/.env.*', '**/storage/**']
        }
    },
});
