const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/tag_creator', 'public/js')
    .copy('resources/js/gallery.js', 'public/js')
    .js('resources/js/schedule.js', 'public/js')
    .js('resources/js/dropzone-config.js', 'public/js')
    .js('resources/js/app.js','public/js')
    .copy('node_modules/easy-autocomplete/dist/jquery.easy-autocomplete.min.js','public/js')
    .copy('node_modules/easy-autocomplete/dist/easy-autocomplete.min.css','public/css')
    .sass('resources/sass/app.scss', 'public/css');
