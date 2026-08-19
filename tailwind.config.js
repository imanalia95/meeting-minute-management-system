import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                sage: {
                    50:  '#f4f6f3',
                    100: '#e6ebe1',
                    200: '#cfdac5',
                    300: '#b0c29e',
                    400: '#8fa876',
                    500: '#71915a',
                    600: '#5a7546',
                    700: '#485c38',
                    800: '#3b4a2f',
                    900: '#323f29',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
