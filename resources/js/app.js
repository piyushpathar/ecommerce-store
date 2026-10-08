import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
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

// Escape text, then bold each word of the search query inside it (for search suggestions)
window.highlightMatch = function (text, query) {
    const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    const words = String(query ?? '').trim().split(/\s+/).filter(Boolean)
        .map((w) => escapeHtml(w).replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    const safe = escapeHtml(text);
    if (!words.length) return safe;
    return safe.replace(new RegExp(`(${words.join('|')})`, 'gi'), '<mark class="bg-transparent text-brand-600 dark:text-brand-400 font-semibold">$1</mark>');
};

// Start Alpine
Alpine.plugin(collapse);
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
