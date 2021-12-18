const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.options({
    hmrOptions: {
        host: 'localhost',
        port: '8089'
    },
});

// mix.webpackConfig({
//     devServer: {
//         port: '8089'
//     },
// });

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .webpackConfig(require('./webpack.config'));

// if (mix.inProduction()) {
//     mix.version();
// }

mix.version();

if ( ! mix.inProduction()) {
    mix.webpackConfig({
        devtool: 'inline-source-map',
        devServer: {
            port: '8089'
        },
    })
    // mix.webpackConfig({
    //     devtool: 'inline-source-map'
    // })
}

// mix.disableNotifications();


/*
const { assertSupportedNodeVersion } = require('../src/Engine');

module.exports = async () => {
    assertSupportedNodeVersion();

    const mix = require('../src/Mix').primary;

    require(mix.paths.mix());

    await mix.installDependencies();
    await mix.init();

    return mix.build();
};
*/
