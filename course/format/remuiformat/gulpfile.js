// Gulp.
const gulp = require('gulp');
const exec = require('gulp-exec');
const clean = require('gulp-clean');

// Sass/CSS stuff.
const sass = require('gulp-sass');
const prefix = require('gulp-autoprefixer');

// JS stuff.
const minify = require('gulp-minify');
const sourcemaps = require('gulp-sourcemaps');

// Check if production mode on.
const PRODUCTION = true;

// Default js source.
const jssrc = './amd/src/*.js';

// Compile Sass.
gulp.task('sass', function() {
    return gulp.src(['./scss/styles.scss'])
        .pipe(sass({
            outputStyle: 'expanded'
        }))
        .pipe(prefix(
            "last 1 version", "> 1%", "ie 8", "ie 7"
        ))
        .pipe(gulp.dest('.'));
});

// Compile JS.
gulp.task('compress', function() {
    var task = gulp.src(jssrc);
    if (PRODUCTION) {
        task = task.pipe(sourcemaps.init())
            .pipe(minify({
                ext: {
                    min: '.min.js'
                },
                noSource: true,
                ignoreFiles: []
            }))
            .pipe(sourcemaps.write('.'));
    }
    return task.pipe(gulp.dest('./amd/build'));
});

// Purge cache.
gulp.task('purge', function(done) {
    exec('php ' + __dirname + '/../../../admin/cli/purge_caches.php');
    done();
});

// Watch for changes.
gulp.task('watch', function(done) {
    gulp.watch('./amd/src/*.js', gulp.series('clean', 'compress', 'purge'));
    gulp.watch('./scss/*.scss', gulp.series('sass', 'purge'));
    done();
});

// Clean build directory.
gulp.task('clean', function() {
    return gulp.src(['./amd/build/*'], { read: false })
        .pipe(clean({ force: true }));
});

// Default task.
gulp.task('default', gulp.series('clean', 'compress', 'sass', 'purge', 'watch'));
