import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                "surface-container-low": "#f6f3f5",
                "secondary-fixed": "#89f5e7",
                "on-tertiary": "#ffffff",
                "surface-container-high": "#eae7e9",
                "outline-variant": "#c6c6cd",
                "tertiary": "#000000",
                "tertiary-fixed-dim": "#dec29a",
                "outline": "#76777d",
                "on-tertiary-fixed": "#271901",
                "surface-tint": "#565e74",
                "on-primary-container": "#7c839b",
                "surface-bright": "#fcf8fa",
                "on-surface": "#1b1b1d",
                "primary": "#000000",
                "on-secondary": "#ffffff",
                "official-ink": "#0F172A",
                "error": "#ba1a1a",
                "community-indigo": "#4F46E5",
                "surface": "#fcf8fa",
                "on-surface-variant": "#45464d",
                "surface-dim": "#dcd9db",
                "inverse-surface": "#303032",
                "primary-fixed-dim": "#bec6e0",
                "primary-container": "#131b2e",
                "surface-container": "#f0edef",
                "inverse-on-surface": "#f3f0f2",
                "secondary-container": "#86f2e4",
                "on-primary": "#ffffff",
                "community-teal": "#0D9488",
                "on-tertiary-fixed-variant": "#574425",
                "on-background": "#1b1b1d",
                "tertiary-fixed": "#fcdeb5",
                "on-secondary-container": "#006f66",
                "secondary-fixed-dim": "#6bd8cb",
                "tertiary-container": "#271901",
                "reaction-red": "#EF4444",
                "inverse-primary": "#bec6e0",
                "error-container": "#ffdad6",
                "on-primary-fixed": "#131b2e",
                "paper-white": "#F8FAFC",
                "on-tertiary-container": "#98805d",
                "on-error": "#ffffff",
                "surface-container-lowest": "#ffffff",
                "secondary": "#006a61",
                "on-primary-fixed-variant": "#3f465c",
                "on-secondary-fixed-variant": "#005049",
                "surface-variant": "#e4e2e4",
                "surface-border": "#E2E8F0",
                "on-error-container": "#93000a",
                "on-secondary-fixed": "#00201d",
                "background": "#fcf8fa",
                "primary-fixed": "#dae2fd",
                "surface-container-highest": "#e4e2e4"
            },
            borderRadius: {
                "DEFAULT": "0.125rem",
                "lg": "0.25rem",
                "xl": "0.5rem",
                "full": "0.75rem"
            },
            spacing: {
                "base": "4px",
                "container-max": "1200px",
                "margin-desktop": "64px",
                "margin-mobile": "16px",
                "gutter": "24px",
                "article-width": "720px"
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                "headline-lg": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "display-xl": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "article-body": ["Literata", ...defaultTheme.fontFamily.serif],
                "label-caps": ["Inter", ...defaultTheme.fontFamily.sans],
                "ui-small": ["Inter", ...defaultTheme.fontFamily.sans],
                "display-xl-mobile": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "ui-medium": ["Inter", ...defaultTheme.fontFamily.sans],
                "headline-lg-mobile": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans]
            },
            fontSize: {
                "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "700"}],
                "display-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                "article-body": ["18px", {"lineHeight": "32px", "fontWeight": "400"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                "ui-small": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "display-xl-mobile": ["36px", {"lineHeight": "42px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                "ui-medium": ["16px", {"lineHeight": "24px", "fontWeight": "500"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}]
            }
        },
    },

    plugins: [forms],
};
