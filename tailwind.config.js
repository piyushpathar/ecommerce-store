import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                ink: '#0b1220',
                deep: '#070d1a',
                canvas: '#f5f7fa',
                line: '#e5e8ee',
                darkSurface: '#0c1117',
                darkCanvas: '#030507',
                darkLine: 'rgba(255, 255, 255, 0.08)',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                'xs': '0 1px 2px rgba(15, 23, 42, 0.05)',
                'soft': '0 1px 2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.05)',
                'lift': '0 2px 4px rgba(15, 23, 42, 0.04), 0 16px 32px -12px rgba(15, 23, 42, 0.16)',
                'pop': '0 0 0 1px rgba(15, 23, 42, 0.05), 0 24px 48px -12px rgba(15, 23, 42, 0.28)',
                'glow': 'inset 0 1px 0 rgba(255, 255, 255, 0.18), 0 1px 2px rgba(5, 150, 105, 0.3), 0 8px 20px -8px rgba(5, 150, 105, 0.6)',
                'glow-lg': '0 0 30px rgba(16, 185, 129, 0.35)',
            },
            animation: {
                'marquee': 'marquee 35s linear infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0%)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
            },
        },
    },
    plugins: [],
};
