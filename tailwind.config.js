/** @type {import('tailwindcss').Config} */
module.exports = {
    // Tailwind will scan these files to find all class names used.
    // Update these paths when you move to app/Views/ in MVC phase.
    content: [
        "./public/**/*.html",
        "./public/**/*.php",
        "./app/Views/**/*.php",   // for when MVC views are added
        "./public/assets/js/**/*.js",
    ],
    theme: {
        extend: {
            // Custom font for Bangla text (used in about.html)
            fontFamily: {
                bangla: ['"Noto Sans Bengali"', '"SolaimanLipi"', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'), // enables the .prose class used in article.html
    ],
};
