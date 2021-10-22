// webpack.mix.js

let mix = require('laravel-mix');
const tailwindcss = require('tailwindcss'); /* Add this line at the top */

mix.js('src/App.js', 'includes/Frontend').vue();
mix.sass('src/App.scss', 'includes/Frontend').options({
    postCss: [ tailwindcss('./tailwind.config.js') ],
});