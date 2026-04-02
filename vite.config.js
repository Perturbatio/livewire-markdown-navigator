import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { copyFileSync, mkdirSync } from 'fs';
import { resolve, dirname } from 'path';

function copySourceCss(src, dest) {
    return {
        name: 'copy-source-css',
        closeBundle() {
            mkdirSync(dirname(dest), { recursive: true });
            copyFileSync(src, dest);
        },
    };
}

export default defineConfig({
    plugins: [
        tailwindcss(),
        copySourceCss(
            resolve(__dirname, 'resources/css/components/markdown-navigator.css'),
            resolve(__dirname, 'resources/dist/markdown-navigator.tailwind.css'),
        ),
    ],
    build: {
        outDir: 'resources/dist',
        // cssCodeSplit: false,
        rollupOptions: {
            input: 'resources/css/app.css',
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name?.endsWith('.css')) {
                        return 'markdown-navigator.min.css';
                        // return 'markdown-navigator-[hash].css';
                    }

                    return 'assets/[name]-[hash].[extname]';
                }
            }
        },
    },
});
