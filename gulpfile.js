/**
 * gulpfile.js
 */

/**==================================================================================
 * REQUIRES
 *=================================================================================*/

var gulp = require('gulp');

var gutil = require('gulp-util');
var sort = require('gulp-sort');

var sourcemaps = require('gulp-sourcemaps');

var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var cleanCss = require('gulp-clean-css');

var uglify = require('gulp-uglify');
var stripDebug = require('gulp-strip-debug');

var imageMin = require('gulp-imagemin');

var pot = require('gulp-wp-pot');

/**==================================================================================
 * SASS TASKS
 *=================================================================================*/

gulp.task('sass', function ()
{
    return gulp.src('src/sass/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(autoprefixer({
            browsers: ['last 3 versions']
        }))
        .pipe(cleanCss())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('assets/css'));
});

/**==================================================================================
 * JAVASCRIPT TASKS
 *=================================================================================*/

gulp.task('js', function ()
{
    gulp.src('src/js/**/*.js')
        .pipe(sourcemaps.init())
        //.pipe(stripDebug())
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('assets/js'));
});

/**==================================================================================
 * IMAGE TASKS
 *=================================================================================*/

gulp.task('img', function ()
{
    gulp.src('src/img/**/*')
        .pipe(imageMin())
        .pipe(gulp.dest('assets/img'));
});

/**==================================================================================
 * WP TASKS
 *=================================================================================*/

gulp.task('pot', function ()
{
    var domainSlug = __dirname.replace(/.*\/([^\/]+)$/, '$1');

    return gulp.src(['**/*.php', '!tests/**/*', '!vendor/**/*'])
        .pipe(sort())
        .pipe(pot({
            domain: 'woocommerce-custobar',
            destFile: 'woocommerce-custobar.pot',
            package: 'woocommerce-custobar',
            team: 'Sofokus <support@sofokus.com>'
        }))
        .pipe(gulp.dest('languages'));
});

/**==================================================================================
 * GENERIC TASKS
 *=================================================================================*/

gulp.task('compile', ['sass', 'js', 'img', 'pot']);

gulp.task('production', ['sass', 'js', 'img']);

gulp.task('watch', function ()
{
    gulp.watch('src/sass/**/*.scss', ['sass']);
    gulp.watch('src/js/**/*.js', ['js']);
    gulp.watch('src/img/**/*', ['img']);
    gulp.watch(['**/*.php', '!tests/**/*', '!vendor/**/*'], ['pot']);

    console.log(gutil.colors.green('Gulp is watching for changes, Ctrl-C to exit ...'));
});

gulp.task('default', ['production']);
