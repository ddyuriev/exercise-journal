import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const path = require('path')

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias :{
            '~bootstrap' : path.resolve(__dirname, 'node_modules/bootstrap'),
            '~select2' : path.resolve(__dirname, 'node_modules/select2'),
            '~select2-bootstrap-5-theme' : path.resolve(__dirname, 'node_modules/select2-bootstrap-5-theme'),
            '~bootstrap-icons' : path.resolve(__dirname, 'node_modules/bootstrap-icons'),
        }
    }
});
