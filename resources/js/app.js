import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;

// Theme manager
window.toggleDarkMode = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};

// Initialize theme from preference
if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// Global Toast Notifications
window.showToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
};

// Start Alpine
Alpine.start();

// Initialize Lucide icons on load & whenever DOM changes
window.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
document.addEventListener('alpine:initialized', () => {
    createIcons({ icons });
});
window.refreshIcons = () => {
    setTimeout(() => createIcons({ icons }), 50);
};
