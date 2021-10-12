// webpack.mix.js

let mix = require('laravel-mix');

mix.js('src/App.js', 'includes/Frontend').vue();
mix.sass('src/App.scss', 'includes/Frontend');