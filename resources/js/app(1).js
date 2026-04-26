/**
 * app.js
 *
 * - Boots Axios with CSRF token
 * - Manages dark/light mode toggle with localStorage persistence
 */

import axios from 'axios';

// ── Axios setup ──────────────────────────────────────────────
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── Dark Mode ────────────────────────────────────────────────
const STORAGE_KEY = 'theme';
const html        = document.documentElement;

/**
 * Apply the given theme ('dark' | 'light') to <html>
 * and update every toggle button on the page.
 */
function applyTheme(theme) {
    if (theme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }

    // Update all toggle buttons
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        const icon  = btn.querySelector('.icon');
        const label = btn.querySelector('.label');

        if (theme === 'dark') {
            if (icon)  icon.textContent  = '☀️';
            if (label) label.textContent = 'Light mode';
            btn.setAttribute('aria-label', 'Switch to light mode');
            btn.setAttribute('aria-pressed', 'true');
        } else {
            if (icon)  icon.textContent  = '🌙';
            if (label) label.textContent = 'Dark mode';
            btn.setAttribute('aria-label', 'Switch to dark mode');
            btn.setAttribute('aria-pressed', 'false');
        }
    });
}

/**
 * Toggle between dark and light, persist choice.
 */
function toggleTheme() {
    const current = html.classList.contains('dark') ? 'dark' : 'light';
    const next    = current === 'dark' ? 'light' : 'dark';

    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
}

/**
 * Boot: restore saved preference, or fall back to OS preference.
 */
function bootTheme() {
    const saved = localStorage.getItem(STORAGE_KEY);

    if (saved === 'dark' || saved === 'light') {
        applyTheme(saved);
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }
}

// Run immediately so there's no flash of wrong theme
bootTheme();

// Wire up toggle buttons once DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    bootTheme(); // re-apply after DOM loads to update button labels

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', toggleTheme);
    });
});