import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import {resolve} from 'path';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
    ],
    server: {
        port: 5173,
        fs: {
            // Allow serving files from the node_modules directory
            allow: ['node_modules'] ['src']
        }
    },
    resolve: {
        alias: {
            // Resolve '@' to the src directory
            '@': resolve(__dirname, 'src')
        }
    }
});
