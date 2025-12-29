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
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#2563EB', // blue-600
                    hover: '#1D4ED8',   // blue-700
                },
                secondary: '#64748B',   // slate-500
                success: '#10B981',     // emerald-500
                danger: '#EF4444',      // red-500
                warning: '#F59E0B',     // amber-500
                info: '#3B82F6',        // blue-500
                dark: '#0F172A',        // slate-900
                light: '#F8FAFC',       // slate-50
            },
            borderRadius: {
                'xl': '12px',
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            }
        },
    },

    plugins: [forms],
};
