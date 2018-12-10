/**
 * Main Gulpfile
 */

// Main gulp extensions
var gulp = require('gulp');
var chug = require('gulp-chug');
var argv = require('yargs').argv;

// Main paths (e.g. from app/Resources/SyliusAdminBundle/Gulpfile.js)
config = [
    '--rootPath',
    argv.rootPath || '../../../',
    '--nodeModulesPath',
    argv.nodeModulesPath || '../../../node_modules/'
];

// AdminBundle gulpfile
gulp.task('admin', function() {
    gulp.src('app/Resources/SyliusAdminBundle/Gulpfile.js', { read: false })
        .pipe(chug({ args: config }));
});

// ShopBundle gulpfile
gulp.task('shop', function() {
    gulp.src('app/Resources/SyliusShopBundle/Gulpfile.js', { read: false })
        .pipe(chug({ args: config }));
});
gulp.task('shop-dist', function () {
    gulp.src('app/Resources/SyliusShopBundle/Gulpfile.js', { read: false })
        .pipe(chug({ args: config, tasks: 'dist' }));
});
gulp.task('shop-watch-js', function () {
    gulp.src('app/Resources/SyliusShopBundle/Gulpfile.js', { read: false })
        .pipe(chug({ args: config, tasks: 'watch-js' }));
});

gulp.task('default', ['admin', 'shop']);
