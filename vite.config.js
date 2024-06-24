import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import dotenv from 'dotenv';

dotenv.config({ path: resolve(__dirname, '../.env') });

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['frontend/src/main.js'],
            refresh: true,
        }),
    ],
    define: {
        'process.env': process.env
    }
});
