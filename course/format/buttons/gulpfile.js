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

gulp.task("style", function() {
  gulp.src("scss/style.scss")
    .pipe(plumber())
    .pipe(sass())
    .pipe(postcss([
      autoprefixer({browsers: ["last 2 versions"]}),
      mqpacker({sort: true})
    ]))
    // .pipe(rename("styles.css"))
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


gulp.watch("amd/src/*.js", ["minjs"]);
gulp.watch("sass/**/*.{scss,sass}", ["style"]);
