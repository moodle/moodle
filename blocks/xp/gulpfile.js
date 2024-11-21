/* eslint-disable */

const gulp = require('gulp');
const replace = require('gulp-replace');
const exec = require('child_process').exec;
const postcss = require('gulp-postcss');
const webpack = require('webpack');

const webpackDevConfig = require('./webpack.dev.js');
const webpackProdConfig = require('./webpack.prod.js');

const cssPaths = ['./css/styles.css'];

const jsAmdPaths = ['./amd/src/*.js'];

const jsUiPaths = ['./ui/src/**/*.ts', './ui/src/**/*.tsx', './ui/src/**/*.js'];

const cssWatchPaths = [
  './renderer.php',
  './templates/**/*.mustache',
  './classes/form/**/*.php',
  './classes/local/shortcode/handler.php',
  './classes/local/controller/**/*.php',
  './classes/rule_*.php',
  './css/safelist.txt',
].concat(jsUiPaths);

/** CSS. */

var cssBuild = gulp.series(tailwindBuild);

function tailwindBuild(cb) {
  // Build Tailwind. This behaves differently depending on NODE_ENV.
  return gulp.src(cssPaths).pipe(postcss()).pipe(gulp.dest('.'));
}

/** Moodle. */

var moodleGruntAmdIsRunning = false;

function moodleGruntAmd(cb) {
  if (moodleGruntAmdIsRunning) {
    cb();
    return;
  };

  moodleGruntAmdIsRunning = true;
  exec('grunt amd', function (err, stdout, stderr) {
    moodleGruntAmdIsRunning = false;
    if (err) {
      console.log(stdout);
      console.log(stderr);
    }
    cb(err);
  });
}

function removeAmdDefinedName(cb) {
  // We must do this to support older Moodle versions that did not expect
  // the name to be included as part of the build file. That can be seen
  // in Moodle 3.3, and maybe older versions, where the code will attempt
  // to add the module even if it is already present. As of Moodle 4.0,
  // the module name will be injected if not present, so we can safely remove it.
  return gulp
    .src('./amd/build/*.js')
    .pipe(replace(/^\s*define\s*\(\s*['"][a-z0-9_/-]+['"]\s*,/m, 'define('))
    .pipe(gulp.dest('./amd/build'));
}

const moodleAmd = gulp.series(moodleGruntAmd, removeAmdDefinedName);

/** Webpack. */

function webpackBuildFromConfig(config) {
  return new Promise((resolve, reject) => {
    webpack(config, (err, stats) => {
      if (err) {
        return reject(err);
      } else if (stats.hasErrors()) {
        return reject(new Error(stats.compilation.errors.map((e) => e.message).join('\n')));
      }
      resolve();
    });
  });
}

function webpackBuildDev(cb) {
  return webpackBuildFromConfig(webpackDevConfig)
}

function webpackBuildProd(cb) {
  return webpackBuildFromConfig(webpackProdConfig)
}

function webpackFixVendorDependencies(cb) {
  // Change the define definition of the ui-* modules to dependent on the vendor module.
  return gulp
    .src(['./amd/src/ui-*.js', '!./amd/src/ui-commons-lazy.js'])
    .pipe(replace(/^\s*define\s*\(/m, 'define(["block_xp/ui-commons-lazy"],'))
    .pipe(gulp.dest('./amd/src'));
}

const postWebpackBuild = gulp.series(webpackFixVendorDependencies);
const webpackDev = gulp.series(webpackBuildDev, postWebpackBuild);
const webpackProd = gulp.series(webpackBuildProd, postWebpackBuild);

/** Watch. */

function watchAmd(cb) {
  return gulp.watch(jsAmdPaths, { delay: 500 }, gulp.series(moodleAmd));
}

function watchCss(cb) {
  return gulp.watch([].concat(cssPaths, cssWatchPaths), cssBuild);
}

const watchJs = gulp.parallel(watchUi, watchAmd);

function watchUi(cb) {
  return gulp.watch(jsUiPaths, gulp.series(webpackDev, moodleAmd));
}

exports['dist'] = gulp.series(cssBuild, webpackProd, moodleAmd);
exports['dist:dev'] = gulp.series(cssBuild, webpackDev, moodleAmd);
exports['watch'] = gulp.parallel(watchJs, watchCss);
exports['watch:css'] = watchCss;
exports['watch:js'] = watchJs;
exports['webpack:dev'] = webpackDev;
exports['webpack:prod'] = webpackProd;
