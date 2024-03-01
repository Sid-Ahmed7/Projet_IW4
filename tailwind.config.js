

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
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
