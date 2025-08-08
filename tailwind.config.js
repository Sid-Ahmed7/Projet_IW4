

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  safelist: [
    // Force inclusion of all color classes used in devis
    'bg-green',
    'hover:bg-green',
    'bg-red',
    'hover:bg-red',
    'bg-blue-600',
    'hover:bg-blue-700',
    'bg-purple-600',
    'hover:bg-purple-700',
    'bg-orange-600',
    'hover:bg-orange-700',
    'bg-gray-600',
    'hover:bg-gray-700',
    'bg-yellow-500',
    'hover:bg-yellow-600',
    'bg-indigo-600',
    'hover:bg-indigo-700',
    'transition-colors',
    'transition-all',
    'duration-200',
    'transform',
    'hover:scale-105',
    'shadow-lg',
    'shadow-sm'
  ],
  theme: {
    extend: {

      colors: {
        darkBlue: "#1E293A",
        lightBlue: "#5d7a97",
        lightGreen: "#00FFC2",
        viletPer: "#de15da",
        darkGreen: "#005DCD",
        pink: "#FF2896",
        orange: "#FF9B65",
        fullPink: "#F2EDED",
        purpleM: "#9D18A9",
        red: "#B50505",
        green: "#05B541",
        "text-color": "var(--text-color)"
      },
      borderRadius: {

        '2xl': '2rem',
        '50xl': '50rem',
      },
      width: {
        '26': '26%',
        '300': '300px',
        '343' : '343px',

      },
      height:{
        '86' :'86px',
        '278':'278px',
      },
      margin: {
        '15': '15px',
      },

      letterSpacing: {

        "small": "0.1rem",
      }
    },
  },
  plugins: [],
}
