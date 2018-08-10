"use strict";

var gulp = require("gulp");
var sass = require("gulp-sass");
var rename = require("gulp-rename");
var plumber = require("gulp-plumber");
var postcss = require("gulp-postcss");
var autoprefixer = require("autoprefixer");
var mqpacker = require("css-mqpacker");
// var csso = require("gulp-csso");
var sequence = require("gulp-sequence");
var del = require("del");
// for min js
var jsmin = require('gulp-jsmin');

// sharingactivities - style
gulp.task("clean", function() {
  return del("style.css");
});

gulp.task('css', function(){
  var processors = [
    autoprefixer({browsers: ['last 2 version']}),
    mqpacker({sort: true})
  ];
  return gulp.src('styles.css')
    .pipe(postcss(processors))
    .pipe(rename('style.css'))
    .pipe(gulp.dest('.'));
});

gulp.task('sass', function() {
    return gulp.src("scss/*.scss")
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest("."))
        // .pipe(browserSync.stream());
});

gulp.task("style", function() {
  gulp.src("scss/*.scss")
    .pipe(plumber())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([
      autoprefixer({browsers: ["last 2 versions"]}),
      mqpacker({sort: true})
    ]))
    .pipe(rename("styles.css"))
    .pipe(gulp.dest('.'));
    // .pipe(csso())
    // .pipe(rename("style.css"))
    // .pipe(gulp.dest("../blocks/search_custom/css"))
    // .pipe(server.stream());
});

gulp.task("build", function(end) {
  sequence(
    "clean",
    "style",
    "minjs",
    end
  );
});



gulp.task('min', function() {
    gulp.src('amd/src/*.js')
        .pipe(jsmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('amd/build'));
});

// minify js
gulp.task('clean_js', function() {
  return del('amd/build/*.js');
});

gulp.task('minjs', function(end) {
  sequence('clean_js', 'min', end);
});

gulp.task('watch', function() {
  gulp.watch("amd/src/*.js", ["minjs"]);
  gulp.watch("sass/**/*.scss", ["sass"]);
  gulp.watch("sass/*.scss", ["style"]);
});
