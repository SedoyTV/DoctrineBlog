const mix = require('laravel-mix');

mix.js('frontend/src/main.js', 'public/js')
    .vue();
