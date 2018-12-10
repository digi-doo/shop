// Gulp extensions
var gulp = require('gulp');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var env = process.env.GULP_ENV;
// var critical = require('critical');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var minify = require('gulp-minify');
var cleanCSS = require('gulp-clean-css');
var imagemin = require('gulp-imagemin');
var argv = require('yargs').argv;
var realFavicon = require ('gulp-real-favicon');
var runSequence = require('run-sequence');
var fs = require('fs');

// Paths
var rootPath = argv.rootPath;
var nodeModulesPath = argv.nodeModulesPath;
var appShopPath = rootPath + 'app/Resources/SyliusShopBundle/';
var webShopPath = rootPath + 'web/assets/shop/';

// File where the favicon markups are stored
var FAVICON_DATA_FILE = webShopPath + 'img/favicon/faviconData.json';

// Shop sasses
var input = appShopPath + 'private/scss/**/*.scss';
var output = webShopPath + 'css/';
var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'expanded'
};
var autoprefixerOptions = {
  browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};
var paths = {
    shop: {
        appJs: [
            appShopPath + 'private/js/modernizr-custom.js',
            appShopPath + 'private/js/classie.js',
            appShopPath + 'private/js/menu.js',
            appShopPath + 'private/js/app.js',
        ],
        taxonJs: [
            appShopPath + 'private/js/infinite-scroll.js',
        ],
    }
}

gulp.task('serve', ['sass'], function() {
    // browserSync.init({
    //     server: {
    //         baseDir: "./"
    //     }
    // });

    gulp.watch(appShopPath + "private/scss/**/*.scss", ['sass']);
    // gulp.watch(appShopPath + "views/*.html.twig").on('change', browserSync.reload);
});

// gulp.task('critical', function (cb) {
//     critical.generate({
//         inline: true,
//         base: './',
//         src: 'index.html',
//         dest: './index-critical.html',
//         minify: true,
//         width: 1920,
//         height: 1080
//     });
// });

gulp.task('sylius-scripts', function () {
    return gulp.src(appShopPath + 'private/js/sylius/js/*.js')
        .pipe(concat('sylius-scripts.js'))
        .pipe(sourcemaps.init())
        .pipe(minify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(webShopPath + 'js'))
});

gulp.task('sylius-app', function () {
    return gulp.src(appShopPath + 'private/js/sylius/app/*.js')
        .pipe(concat('sylius-app.js'))
        .pipe(sourcemaps.init())
        .pipe(minify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(webShopPath + 'js'))
});


gulp.task('app-scripts', function () {
    return gulp.src(paths.shop.appJs)
        .pipe(concat('app.js'))
        .pipe(sourcemaps.init())
        .pipe(minify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(webShopPath + 'js'))
});

gulp.task('sass', function() {
    return gulp
        .src(input)
        .pipe(sourcemaps.init())
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(sourcemaps.write())
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(gulp.dest(output));
        // .pipe(browserSync.stream());
});


gulp.task('watch', function() {
  return gulp
    // Watch the input folder for change,
    // and run `sass` task when something happens
    .watch(input, ['sass'])
    // When there is a change,
    // log a message in the console
    .on('change', function(event) {
      console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

gulp.task('images', () =>
    gulp.src(appShopPath + 'private/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest(webShopPath + 'img'))
);

gulp.task('prod', function () {
  return gulp
    .src(input)
    .pipe(sass({ outputStyle: 'compressed' }))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(gulp.dest(output));
});


// Generate the icons. This task takes a few seconds to complete.
// You should run it at least once to create the icons. Then,
// you should run it whenever RealFaviconGenerator updates its
// package (see the check-for-favicon-update task below).
gulp.task('generate-favicon', function(done) {
    realFavicon.generateFavicon({
        masterPicture: appShopPath + 'private/favicon/favicon.png',
        dest: webShopPath + 'img/favicon',
        iconsPath: webShopPath + 'img/favicon',
        design: {
            ios: {
                pictureAspect: 'noChange',
                assets: {
                    ios6AndPriorIcons: false,
                    ios7AndLaterIcons: false,
                    precomposedIcons: false,
                    declareOnlyDefaultIcon: true
                }
            },
            desktopBrowser: {},
            windows: {
                pictureAspect: 'noChange',
                backgroundColor: '#da532c',
                onConflict: 'override',
                assets: {
                    windows80Ie10Tile: false,
                    windows10Ie11EdgeTiles: {
                        small: false,
                        medium: true,
                        big: false,
                        rectangle: false
                    }
                }
            },
            androidChrome: {
                pictureAspect: 'noChange',
                themeColor: '#ffffff',
                manifest: {
                    display: 'standalone',
                    orientation: 'notSet',
                    onConflict: 'override',
                    declared: true
                },
                assets: {
                    legacyIcon: false,
                    lowResolutionIcons: false
                }
            }
        },
        settings: {
            scalingAlgorithm: 'Mitchell',
            errorOnImageTooSmall: false
        },
        markupFile: FAVICON_DATA_FILE
    }, function() {
        done();
    });
});

// Inject the favicon markups in your HTML pages. You should run
// this task whenever you modify a page. You can keep this task
// as is or refactor your existing HTML pipeline.
gulp.task('inject-favicon-markups', function() {
    return gulp.src([ '*.html.twig' ])
        .pipe(realFavicon.injectFaviconMarkups(JSON.parse(fs.readFileSync(FAVICON_DATA_FILE)).favicon.html_code))
        .pipe(gulp.dest('./'));
});

// Check for updates on RealFaviconGenerator (think: Apple has just
// released a new Touch icon along with the latest version of iOS).
// Run this task from time to time. Ideally, make it part of your
// continuous integration system.
gulp.task('check-for-favicon-update', function(done) {
    var currentVersion = JSON.parse(fs.readFileSync(FAVICON_DATA_FILE)).version;
    realFavicon.checkForUpdates(currentVersion, function(err) {
        if (err) {
            throw err;
        }
    });
});

gulp.task('taxon-scripts', function () {
    return gulp.src(paths.shop.taxonJs)
        .pipe(concat('taxon.js'))
        .pipe(sourcemaps.init())
        .pipe(minify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(webShopPath + 'js'))
});
gulp.task('watch-taxon-scripts', function() {
    gulp.watch(paths.shop.taxonJs, function() {
        runSequence('taxon-scripts');
    });
});

gulp.task('default', ['serve', 'images']);
gulp.task('dist', [
    'app-scripts', 
    'taxon-scripts', 
    'sylius-scripts', 
    'sylius-app', 
    'prod', 
    'images', 
    'generate-favicon'
]);
gulp.task('watch-js', ['watch-taxon-scripts']);