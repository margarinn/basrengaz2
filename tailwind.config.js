/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./resources/js/src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#DC2626',
          50: '#FDECEB',
          100: '#FBD9D8',
          200: '#F7B3B1',
          300: '#F38D8A',
          400: '#EF6763',
          500: '#E53935',
          600: '#C42A26',
          700: '#A3201D',
          800: '#821815',
          900: '#61100E',
        },
        secondary: {
          DEFAULT: '#4CAF50',
          50: '#E8F5E9',
          100: '#C8E6C9',
          200: '#A5D6A7',
          300: '#81C784',
          400: '#66BB6A',
          500: '#4CAF50',
          600: '#43A047',
          700: '#388E3C',
          800: '#2E7D32',
          900: '#1B5E20',
        },
        accent: {
          orange: '#F96F0A',
          yellow: '#FFC107',
        },
        dark: {
          DEFAULT: '#1A1A2E',
          800: '#16213E',
          700: '#0F3460',
        }
      },
      fontFamily: {
        sans: ['Poppins', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'sidebar': '4px 0 24px rgba(0, 0, 0, 0.08)',
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
      }
    },
  },
  plugins: [],
}
