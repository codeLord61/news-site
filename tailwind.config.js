module.exports = {
  content: [
    "./public/**/*.html",
    "./public/**/*.php",
    "./app/views/**/*.php", 
    "./public/assets/js/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        bangla: ['"Noto Sans Bengali"', '"SolaimanLipi"', "sans-serif"],
      },
    },
  },
  plugins: [
    require("@tailwindcss/typography"),
  ],
};
