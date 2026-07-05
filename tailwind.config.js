import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        university: {
          primary: '#0F5832',
          secondary: '#0D4A2A',
          accent: '#1A7A45',
          light: '#E8F5E9',
        },
      },
    },
  },
  plugins: [forms],
};