import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

/**
 * Builds the documentation site only. Nothing here ships with the package: sites compile the
 * package's CSS and JavaScript with their own Vite.
 */
export default defineConfig({
    plugins: [tailwindcss()],
    base: './',
    build: {
        outDir: 'build/docs/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            input: ['docs/app.css', 'docs/app.js'],
        },
    },
});
