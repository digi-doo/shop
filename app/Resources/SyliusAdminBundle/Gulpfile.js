// Gulp extensions
var concat = require('gulp-concat');
var env = process.env.GULP_ENV;
var gulp = require('gulp');
var gulpif = require('gulp-if');
var livereload = require('gulp-livereload');
var merge = require('merge-stream');
var order = require('gulp-order');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var argv = require('yargs').argv;

// Paths
var rootPath = argv.rootPath;
var adminRootPath = rootPath + 'web/assets/admin/';
var syliusVendorUiPath = rootPath + 'vendor/sylius/sylius/src/Sylius/Bundle/UiBundle/';
var syliusVendorAdminPath = rootPath + 'vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/';
var appAdminPath = rootPath + 'app/Resources/SyliusAdminBundle/';
var appUiPath = rootPath + 'app/Resources/SyliusUiBundle/';
var nodeModulesPath = argv.nodeModulesPath;

var paths = {
    admin: {
        js: [
            // nodeModulesPath + 'jquery/dist/jquery.min.js', use CDN in header instead (due to some formTypes)
            nodeModulesPath + 'semantic-ui-css/semantic.min.js',
            appUiPath + 'private/js/sylius/**',
            appAdminPath + 'private/js/sylius/**',
            appAdminPath + 'private/js/*'
        ],
        jsFroala: [
            nodeModulesPath + 'froala-editor/js/froala_editor.min.js',
            nodeModulesPath + 'froala-editor/js/languages/cs.js',
            nodeModulesPath + 'froala-editor/js/plugins/*.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/char_counter.min.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/save.min.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/video.min.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/special_characters.min.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/forms.min.js',
            '!' + nodeModulesPath + 'froala-editor/js/plugins/emoticons.min.js',
        ],
        cssFroala: [
            nodeModulesPath + 'froala-editor/css/**/*.css'
        ],
        sass: [
            syliusVendorUiPath + 'Resources/private/sass/**',
            syliusVendorAdminPath + 'Resources/private/sass/**',
            appAdminPath + 'private/sass/sshop-logic.scss',
            appAdminPath + 'private/sass/main.sass'
        ],
        customAdmin: [
            appAdminPath + 'private/sass/admin-prod.scss'  
        ],
        css: [
            nodeModulesPath + 'semantic-ui-css/semantic.min.css',
            syliusVendorUiPath + 'Resources/private/css/**',
            syliusVendorAdminPath + 'Resources/private/css/**'
        ],
        img: [
            syliusVendorUiPath + 'Resources/private/img/**',
            syliusVendorAdminPath + 'Resources/private/img/**',
            appAdminPath + 'private/img/**'
        ]
    }
};

// Main admin js task (for production use 'GULP_ENV=prod gulp admin')
gulp.task('admin-js', function () {
    return gulp.src(paths.admin.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
    ;
});

// Main admin js froal task (for production use 'GULP_ENV=prod gulp admin')
gulp.task('admin-js-froala', function () {
    return gulp.src(paths.admin.jsFroala)
        .pipe(concat('froala.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
    ;
});

// Main admin css froala task (for production use 'GULP_ENV=prod gulp admin')
gulp.task('admin-css-froala', function () {
    return gulp.src(paths.admin.cssFroala)
        .pipe(concat('froala.css'))
        .pipe(gulp.dest(adminRootPath + 'css/'))
    ;
});

// Main admin styles task (for production use 'GULP_ENV=prod gulp admin')
gulp.task('admin-css', function() {
    gulp.src([nodeModulesPath+'semantic-ui-css/themes/**/*']).pipe(gulp.dest(adminRootPath + 'css/themes/'));

    var cssStream = gulp.src(paths.admin.css)
        .pipe(concat('css-files.css'));

    var sassStream = gulp.src(paths.admin.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'));

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'css/'))
        .pipe(livereload())
    ;
});

gulp.task('custom-admin-css', function () {
    return gulp.src(paths.admin.customAdmin)
        .pipe(sass())
        .pipe(concat('admin-prod.css'))
        .pipe(gulp.dest(adminRootPath + 'css/'));
});

// Copy admin images task
gulp.task('admin-img', function() {
    return gulp.src(paths.admin.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'img/'))
    ;
});

// Admin watch shortcut
gulp.task('admin-watch', function() {
    livereload.listen();

    gulp.watch(paths.admin.js, ['admin-js']);
    gulp.watch(paths.admin.sass, ['admin-css']);
    gulp.watch(paths.admin.css, ['admin-css']);
    gulp.watch(paths.admin.img, ['admin-img']);
});

gulp.task('default', ['admin-js', 'admin-js-froala', 'admin-css-froala', 'admin-css', 'admin-img', 'custom-admin-css']);
gulp.task('watch', ['default', 'admin-watch']);
