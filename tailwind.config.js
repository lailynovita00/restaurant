import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': '#92824e',
                'primary-dark': '#50301c',
                'primary-light': '#f1ddb9',
                'secondary': '#cbb88c',
                'accent': '#765e39',
                'accent-light': '#a38a6a',
                'dark': '#5e4127',
                'gold': '#887446',
            },
        },
    },
    plugins: [],
};
