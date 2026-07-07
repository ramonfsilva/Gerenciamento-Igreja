import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                heading: ['Cormorant Garamond', 'Georgia', 'serif'],
            },
            colors: {
                primary: {
                    DEFAULT: 'var(--color-primary)',
                    hover: 'var(--color-primary-hover)',
                    light: 'var(--color-primary-light)',
                },
                'on-primary': 'var(--color-on-primary)',
                secondary: {
                    DEFAULT: 'var(--color-secondary)',
                    hover: 'var(--color-secondary-hover)',
                },
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    hover: 'var(--color-accent-hover)',
                    light: 'var(--color-accent-light)',
                },
                background: 'var(--color-background)',
                surface: {
                    DEFAULT: 'var(--color-surface)',
                    hover: 'var(--color-surface-hover)',
                },
                muted: {
                    DEFAULT: 'var(--color-muted)',
                    foreground: 'var(--color-muted-foreground)',
                },
                border: 'var(--color-border)',
                input: 'var(--color-input)',
                ring: 'var(--color-ring)',
                'text-primary': 'var(--color-text-primary)',
                'text-secondary': 'var(--color-text-secondary)',
                'text-muted': 'var(--color-text-muted)',
                success: {
                    DEFAULT: 'var(--color-success)',
                    light: 'var(--color-success-light)',
                },
                danger: {
                    DEFAULT: 'var(--color-danger)',
                    light: 'var(--color-danger-light)',
                },
                warning: {
                    DEFAULT: 'var(--color-warning)',
                    light: 'var(--color-warning-light)',
                },
                nav: {
                    bg: 'var(--color-nav-bg)',
                },
                sidebar: {
                    bg: 'var(--color-sidebar-bg)',
                    border: 'var(--color-sidebar-border)',
                },
            },
            borderRadius: {
                sm: 'var(--radius-sm)',
                DEFAULT: 'var(--radius)',
                lg: 'var(--radius-lg)',
            },
            boxShadow: {
                card: 'var(--shadow-card)',
                'card-hover': 'var(--shadow-card-hover)',
                dropdown: 'var(--shadow-dropdown)',
                modal: 'var(--shadow-modal)',
            },
            transitionDuration: {
                fast: 'var(--duration-fast)',
                normal: 'var(--duration-normal)',
                slow: 'var(--duration-slow)',
            },
            transitionTimingFunction: {
                'ease-out-cubic': 'var(--ease-out)',
            },
        },
    },

    plugins: [forms],
};
