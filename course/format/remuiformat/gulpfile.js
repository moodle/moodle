// Gulp.
var gulp = require('gulp');

// Sass/CSS stuff.
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var prefix = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var shell  = require('gulp-shell');
const sourcemaps = require('gulp-sourcemaps');
// JS stuff.
const minify = require('gulp-minify');
const del = require('del');

var jssrc = './amd/src/*.js';

// Compile all your Sass.
gulp.task('sass', function (done){
    gulp.src(['./scss/styles.scss'])
        .pipe(sass({
            outputStyle: 'expanded'
        }))
        .pipe(prefix(
            "last 1 version", "> 1%", "ie 8", "ie 7"
            ))
        .pipe(gulp.dest('.'));
    // Task code here.
    done();
});

gulp.task('compress', function(done) {
    gulp.src(jssrc)
    .pipe(sourcemaps.init())
    .pipe(minify({
        ext:{
           min:'.min.js'
        },
        noSource: true,
        ignoreFiles: []
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./amd/build'));
    // Task code here.
    done();
});

gulp.task('purge', shell.task('php ' + __dirname + '/../../../admin/cli/purge_caches.php'));

gulp.task('watch', function(done) {
    gulp.watch('./amd/src/*.js', gulp.series('clean', 'compress', 'purge'));
    gulp.watch('./scss/*.scss', gulp.series('sass', 'purge'));
    done();
});

gulp.task('clean', function() {
    return del(['./amd/build/*']);
});

gulp.task('default', gulp.series('clean', 'compress', 'sass', 'purge', 'watch'));
