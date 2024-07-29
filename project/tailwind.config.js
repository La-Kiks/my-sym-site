/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      './templates/**/*.html.twig',
      './assets/scripts/*.js',
      "./node_modules/flowbite/**/*.js",
      "./src/**/*.{html,js}"
  ],
    plugins: [
        require('flowbite/plugin')
    ],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                'gold-tips': {
                    '50': '#fdfce9',
                    '100': '#fcfac5',
                    '200': '#faf48e',
                    '300': '#f7e74d',
                    '400': '#f2d41d',
                    '500': '#e2bc10',
                    '600': '#c3930b',
                    '700': '#9c6a0c',
                    '800': '#815412',
                    '900': '#6e4515',
                    '950': '#402308',
                },
            }
        },
    },
}


