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
        brand: {
          gold:   '#E7C15A',
          emerald:'#0B6B53',
          ocean:  '#0F3D3E',
          cream:  '#FFF4D6',
          slate:  '#374151',
        },
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      boxShadow: {
        soft: '0 4px 20px rgba(0,0,0,0.08)',
      }
    },
  },
  plugins: [forms],
}
