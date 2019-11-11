// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/* jshint node: true, browser: false */
/* eslint-env node */

/**
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Grunt configuration
 */

/* eslint-env node */
module.exports = function(grunt) {
    var path = require('path'),
        tasks = {},
        cwd = process.env.PWD || process.cwd(),
        async = require('async'),
        DOMParser = require('xmldom').DOMParser,
        xpath = require('xpath'),
        semver = require('semver'),
        watchman = require('fb-watchman'),
        watchmanClient = new watchman.Client(),
        gruntFilePath = process.cwd();

    // Verify the node version is new enough.
    var expected = semver.validRange(grunt.file.readJSON('package.json').engines.node);
    var actual = semver.valid(process.version);
    if (!semver.satisfies(actual, expected)) {
        grunt.fail.fatal('Node version not satisfied. Require ' + expected + ', version installed: ' + actual);
    }

    // Windows users can't run grunt in a subdirectory, so allow them to set
    // the root by passing --root=path/to/dir.
    if (grunt.option('root')) {
        var root = grunt.option('root');
        if (grunt.file.exists(__dirname, root)) {
            cwd = path.join(__dirname, root);
            grunt.log.ok('Setting root to ' + cwd);
        } else {
            grunt.fail.fatal('Setting root to ' + root + ' failed - path does not exist');
        }
    }

    var files = null;
    if (grunt.option('files')) {
        // Accept a comma separated list of files to process.
        files = grunt.option('files').split(',');
    }

    var inAMD = path.basename(cwd) == 'amd';

    // Globbing pattern for matching all AMD JS source files.
    var amdSrc = [];
    if (inAMD) {
        amdSrc.push(cwd + "/src/*.js");
        amdSrc.push(cwd + "/src/**/*.js");
    } else {
        amdSrc.push("**/amd/src/*.js");
        amdSrc.push("**/amd/src/**/*.js");
    }

    /**
     * Function to generate the destination for the uglify task
     * (e.g. build/file.min.js). This function will be passed to
     * the rename property of files array when building dynamically:
     * http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
     *
     * @param {String} destPath the current destination
     * @param {String} srcPath the  matched src path
     * @return {String} The rewritten destination path.
     */
    var babelRename = function(destPath, srcPath) {
        destPath = srcPath.replace('src', 'build');
        destPath = destPath.replace('.js', '.min.js');
        return destPath;
    };

    /**
     * Find thirdpartylibs.xml and generate an array of paths contained within
     * them (used to generate ignore files and so on).
     *
     * @return {array} The list of thirdparty paths.
     */
    var getThirdPartyPathsFromXML = function() {
        var thirdpartyfiles = grunt.file.expand('*/**/thirdpartylibs.xml');
        var libs = ['node_modules/', 'vendor/'];

        thirdpartyfiles.forEach(function(file) {
          var dirname = path.dirname(file);

          var doc = new DOMParser().parseFromString(grunt.file.read(file));
          var nodes = xpath.select("/libraries/library/location/text()", doc);

          nodes.forEach(function(node) {
            var lib = path.join(dirname, node.toString());
            if (grunt.file.isDir(lib)) {
                // Ensure trailing slash on dirs.
                lib = lib.replace(/\/?$/, '/');
            }

            // Look for duplicate paths before adding to array.
            if (libs.indexOf(lib) === -1) {
                libs.push(lib);
            }
          });
        });
        return libs;
    };

    // Project configuration.
    grunt.initConfig({
        eslint: {
            // Even though warnings dont stop the build we don't display warnings by default because
            // at this moment we've got too many core warnings.
            options: {quiet: !grunt.option('show-lint-warnings')},
            amd: {src: files ? files : amdSrc},
            // Check YUI module source files.
            yui: {src: files ? files : ['**/yui/src/**/*.js', '!*/**/yui/src/*/meta/*.js']}
        },
        babel: {
            options: {
                sourceMaps: true,
                comments: false,
                plugins: [
                    'transform-es2015-modules-amd-lazy',
                    'system-import-transformer',
                    // This plugin modifies the Babel transpiling for "export default"
                    // so that if it's used then only the exported value is returned
                    // by the generated AMD module.
                    //
                    // It also adds the Moodle plugin name to the AMD module definition
                    // so that it can be imported as expected in other modules.
                    path.resolve('babel-plugin-add-module-to-define.js'),
                    '@babel/plugin-syntax-dynamic-import',
                    '@babel/plugin-syntax-import-meta',
                    ['@babel/plugin-proposal-class-properties', {'loose': false}],
                    '@babel/plugin-proposal-json-strings'
                ],
                presets: [
                    ['minify', {
                        // This minification plugin needs to be disabled because it breaks the
                        // source map generation and causes invalid source maps to be output.
                        simplify: false,
                        builtIns: false
                    }],
                    ['@babel/preset-env', {
                        targets: {
                            browsers: [
                                ">0.25%",
                                "last 2 versions",
                                "not ie <= 10",
                                "not op_mini all",
                                "not Opera > 0",
                                "not dead"
                            ]
                        },
                        modules: false,
                        useBuiltIns: false
                    }]
                ]
            },
            dist: {
                files: [{
                    expand: true,
                    src: files ? files : amdSrc,
                    rename: babelRename
                }]
            }
        },
        sass: {
            dist: {
                files: {
                    "theme/boost/style/moodle.css": "theme/boost/scss/preset/default.scss",
                    "theme/classic/style/moodle.css": "theme/classic/scss/classicgrunt.scss"
                }
            },
            options: {
                includePaths: ["theme/boost/scss/", "theme/classic/scss/"]
            }
        },
        watch: {
            options: {
                nospawn: true // We need not to spawn so config can be changed dynamically.
            },
            amd: {
                files: ['**/amd/src/**/*.js'],
                tasks: ['amd']
            },
            boost: {
                files: ['**/theme/boost/scss/**/*.scss'],
                tasks: ['scss']
            },
            rawcss: {
                files: ['**/*.css', '**/theme/**/!(moodle.css|editor.css)'],
                tasks: ['rawcss']
            },
            yui: {
                files: ['**/yui/src/**/*.js'],
                tasks: ['yui']
            },
            gherkinlint: {
                files: ['**/tests/behat/*.feature'],
                tasks: ['gherkinlint']
            }
        },
        shifter: {
            options: {
                recursive: true,
                paths: files ? files : [cwd]
            }
        },
        gherkinlint: {
            options: {
                files: files ? files : ['**/tests/behat/*.feature'],
            }
        },
        stylelint: {
            scss: {
                options: {syntax: 'scss'},
                src: files ? files : ['*/**/*.scss']
            },
            css: {
                src: files ? files : ['*/**/*.css'],
                options: {
                    configOverrides: {
                        rules: {
                            // These rules have to be disabled in .stylelintrc for scss compat.
                            "at-rule-no-unknown": true,
                        }
                    }
                }
            }
        }
    });

    /**
     * Generate ignore files (utilising thirdpartylibs.xml data)
     */
    tasks.ignorefiles = function() {
      // An array of paths to third party directories.
      var thirdPartyPaths = getThirdPartyPathsFromXML();
      // Generate .eslintignore.
      var eslintIgnores = ['# Generated by "grunt ignorefiles"', '*/**/yui/src/*/meta/', '*/**/build/'].concat(thirdPartyPaths);
      grunt.file.write('.eslintignore', eslintIgnores.join('\n'));
      // Generate .stylelintignore.
      var stylelintIgnores = [
          '# Generated by "grunt ignorefiles"',
          '**/yui/build/*',
          'theme/boost/style/moodle.css',
          'theme/classic/style/moodle.css',
      ].concat(thirdPartyPaths);
      grunt.file.write('.stylelintignore', stylelintIgnores.join('\n'));
    };

    /**
     * Shifter task. Is configured with a path to a specific file or a directory,
     * in the case of a specific file it will work out the right module to be built.
     *
     * Note that this task runs the invidiaul shifter jobs async (becase it spawns
     * so be careful to to call done().
     */
    tasks.shifter = function() {
        var done = this.async(),
            options = grunt.config('shifter.options');

        // Run the shifter processes one at a time to avoid confusing output.
        async.eachSeries(options.paths, function(src, filedone) {
            var args = [];
            args.push(path.normalize(__dirname + '/node_modules/shifter/bin/shifter'));

            // Always ignore the node_modules directory.
            args.push('--excludes', 'node_modules');

            // Determine the most appropriate options to run with based upon the current location.
            if (grunt.file.isMatch('**/yui/**/*.js', src)) {
                // When passed a JS file, build our containing module (this happen with
                // watch).
                grunt.log.debug('Shifter passed a specific JS file');
                src = path.dirname(path.dirname(src));
                options.recursive = false;
            } else if (grunt.file.isMatch('**/yui/src', src)) {
                // When in a src directory --walk all modules.
                grunt.log.debug('In a src directory');
                args.push('--walk');
                options.recursive = false;
            } else if (grunt.file.isMatch('**/yui/src/*', src)) {
                // When in module, only build our module.
                grunt.log.debug('In a module directory');
                options.recursive = false;
            } else if (grunt.file.isMatch('**/yui/src/*/js', src)) {
                // When in module src, only build our module.
                grunt.log.debug('In a source directory');
                src = path.dirname(src);
                options.recursive = false;
            }

            if (grunt.option('watch')) {
                grunt.fail.fatal('The --watch option has been removed, please use `grunt watch` instead');
            }

            // Add the stderr option if appropriate
            if (grunt.option('verbose')) {
                args.push('--lint-stderr');
            }

            if (grunt.option('no-color')) {
                args.push('--color=false');
            }

            var execShifter = function() {

                grunt.log.ok("Running shifter on " + src);
                grunt.util.spawn({
                    cmd: "node",
                    args: args,
                    opts: {cwd: src, stdio: 'inherit', env: process.env}
                }, function(error, result, code) {
                    if (code) {
                        grunt.fail.fatal('Shifter failed with code: ' + code);
                    } else {
                        grunt.log.ok('Shifter build complete.');
                        filedone();
                    }
                });
            };

            // Actually run shifter.
            if (!options.recursive) {
                execShifter();
            } else {
                // Check that there are yui modules otherwise shifter ends with exit code 1.
                if (grunt.file.expand({cwd: src}, '**/yui/src/**/*.js').length > 0) {
                    args.push('--recursive');
                    execShifter();
                } else {
                    grunt.log.ok('No YUI modules to build.');
                    filedone();
                }
            }
        }, done);
    };

    tasks.gherkinlint = function() {
        var done = this.async(),
            options = grunt.config('gherkinlint.options');

        var args = grunt.file.expand(options.files);
        args.unshift(path.normalize(__dirname + '/node_modules/.bin/gherkin-lint'));
        grunt.util.spawn({
            cmd: 'node',
            args: args,
            opts: {stdio: 'inherit', env: process.env}
        }, function(error, result, code) {
            // Propagate the exit code.
            done(code === 0);
        });
    };

    tasks.startup = function() {
        // Are we in a YUI directory?
        if (path.basename(path.resolve(cwd, '../../')) == 'yui') {
            grunt.task.run('yui');
        // Are we in an AMD directory?
        } else if (inAMD) {
            grunt.task.run('amd');
        } else {
            // Run them all!.
            grunt.task.run('css');
            grunt.task.run('js');
            grunt.task.run('gherkinlint');
        }
    };

    /**
     * This is a wrapper task to handle the grunt watch command. It attempts to use
     * Watchman to monitor for file changes, if it's installed, because it's much faster.
     *
     * If Watchman isn't installed then it falls back to the grunt-contrib-watch file
     * watcher for backwards compatibility.
     */
    tasks.watch = function() {
        var watchTaskDone = this.async();
        var watchInitialised = false;
        var watchTaskQueue = {};
        var processingQueue = false;

        // Grab the tasks and files that have been queued up and execute them.
        var processWatchTaskQueue = function() {
            if (!Object.keys(watchTaskQueue).length || processingQueue) {
                // If there is nothing in the queue or we're already processing then wait.
                return;
            }

            processingQueue = true;

            // Grab all tasks currently in the queue.
            var queueToProcess = watchTaskQueue;
            // Reset the queue.
            watchTaskQueue = {};

            async.forEachSeries(
                Object.keys(queueToProcess),
                function(task, next) {
                    var files = queueToProcess[task];
                    var filesOption = '--files=' + files.join(',');
                    grunt.log.ok('Running task ' + task + ' for files ' + filesOption);

                    // Spawn the task in a child process so that it doesn't kill this one
                    // if it failed.
                    grunt.util.spawn(
                        {
                            // Spawn with the grunt bin.
                            grunt: true,
                            // Run from current working dir and inherit stdio from process.
                            opts: {
                                cwd: cwd,
                                stdio: 'inherit'
                            },
                            args: [task, filesOption]
                        },
                        function(err, res, code) {
                            if (code !== 0) {
                                // The grunt task failed.
                                grunt.log.error(err);
                            }

                            // Move on to the next task.
                            next();
                        }
                    );
                },
                function() {
                    // No longer processing.
                    processingQueue = false;
                    // Once all of the tasks are done then recurse just in case more tasks
                    // were queued while we were processing.
                    processWatchTaskQueue();
                }
            );
        };

        var watchConfig = grunt.config.get(['watch']);
        watchConfig = Object.keys(watchConfig).reduce(function(carry, key) {
            if (key == 'options') {
                return carry;
            }

            var value = watchConfig[key];
            var fileGlobs = value.files;
            var taskNames = value.tasks;

            taskNames.forEach(function(taskName) {
                carry[taskName] = fileGlobs;
            });

            return carry;
        }, {});

        watchmanClient.on('error', function(error) {
            // We have to add an error handler here and parse the error string because the
            // example way from the docs to check if Watchman is installed doesn't actually work!!
            // See: https://github.com/facebook/watchman/issues/509
            if (error.message.match('Watchman was not found')) {
                // If watchman isn't installed then we should fallback to the other watch task.
                grunt.log.ok('It is recommended that you install Watchman for better performance using the "watch" command.');

                // Fallback to the old grunt-contrib-watch task.
                grunt.renameTask('watch-grunt', 'watch');
                grunt.task.run(['watch']);
                // This task is finished.
                watchTaskDone(0);
            } else {
                grunt.log.error(error);
                // Fatal error.
                watchTaskDone(1);
            }
        });

        watchmanClient.on('subscription', function(resp) {
            if (resp.subscription !== 'grunt-watch') {
                return;
            }

            resp.files.forEach(function(file) {
                grunt.log.ok('File changed: ' + file.name);

                var fullPath = cwd + '/' + file.name;
                Object.keys(watchConfig).forEach(function(task) {
                    var fileGlobs = watchConfig[task];
                    var match = fileGlobs.every(function(fileGlob) {
                        return grunt.file.isMatch(fileGlob, fullPath);
                    });
                    if (match) {
                        // If we are watching a subdirectory then the file.name will be relative
                        // to that directory. However the grunt tasks  expect the file paths to be
                        // relative to the Gruntfile.js location so let's normalise them before
                        // adding them to the queue.
                        var relativePath = fullPath.replace(gruntFilePath + '/', '');
                        if (task in watchTaskQueue) {
                            if (!watchTaskQueue[task].includes(relativePath)) {
                                watchTaskQueue[task] = watchTaskQueue[task].concat(relativePath);
                            }
                        } else {
                            watchTaskQueue[task] = [relativePath];
                        }
                    }
                });
            });

            processWatchTaskQueue();
        });

        process.on('SIGINT', function() {
            // Let the user know that they may need to manually stop the Watchman daemon if they
            // no longer want it running.
            if (watchInitialised) {
                grunt.log.ok('The Watchman daemon may still be running and may need to be stopped manually.');
            }

            process.exit();
        });

        // Initiate the watch on the current directory.
        watchmanClient.command(['watch-project', cwd], function(watchError, watchResponse) {
            if (watchError) {
                grunt.log.error('Error initiating watch:', watchError);
                watchTaskDone(1);
                return;
            }

            if ('warning' in watchResponse) {
                grunt.log.error('warning: ', watchResponse.warning);
            }

            var watch = watchResponse.watch;
            var relativePath = watchResponse.relative_path;
            watchInitialised = true;

            watchmanClient.command(['clock', watch], function(clockError, clockResponse) {
                if (clockError) {
                    grunt.log.error('Failed to query clock:', clockError);
                    watchTaskDone(1);
                    return;
                }

                // Use the matching patterns specified in the watch config.
                var matches = Object.keys(watchConfig).map(function(task) {
                    var fileGlobs = watchConfig[task];
                    var fileGlobMatches = fileGlobs.map(function(fileGlob) {
                        return ['match', fileGlob, 'wholename'];
                    });

                    return ['allof'].concat(fileGlobMatches);
                });

                var sub = {
                    expression: ["anyof"].concat(matches),
                    // Which fields we're interested in.
                    fields: ["name", "size", "type"],
                    // Add our time constraint.
                    since: clockResponse.clock
                };

                if (relativePath) {
                    /* eslint-disable camelcase */
                    sub.relative_root = relativePath;
                }

                watchmanClient.command(['subscribe', watch, 'grunt-watch', sub], function(subscribeError) {
                    if (subscribeError) {
                        // Probably an error in the subscription criteria.
                        grunt.log.error('failed to subscribe: ', subscribeError);
                        watchTaskDone(1);
                        return;
                    }

                    grunt.log.ok('Listening for changes to files in ' + cwd);
                });
            });
        });
    };

    // On watch, we dynamically modify config to build only affected files. This
    // method is slightly complicated to deal with multiple changed files at once (copied
    // from the grunt-contrib-watch readme).
    var changedFiles = Object.create(null);
    var onChange = grunt.util._.debounce(function() {
        var files = Object.keys(changedFiles);
        grunt.config('eslint.amd.src', files);
        grunt.config('eslint.yui.src', files);
        grunt.config('shifter.options.paths', files);
        grunt.config('gherkinlint.options.files', files);
        grunt.config('babel.dist.files', [{expand: true, src: files, rename: babelRename}]);
        changedFiles = Object.create(null);
    }, 200);

    grunt.event.on('watch', function(action, filepath) {
        changedFiles[filepath] = action;
        onChange();
    });

    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-stylelint');
    grunt.loadNpmTasks('grunt-babel');

    // Rename the grunt-contrib-watch "watch" task because we're going to wrap it.
    grunt.renameTask('watch', 'watch-grunt');

    // Register JS tasks.
    grunt.registerTask('shifter', 'Run Shifter against the current directory', tasks.shifter);
    grunt.registerTask('gherkinlint', 'Run gherkinlint against the current directory', tasks.gherkinlint);
    grunt.registerTask('ignorefiles', 'Generate ignore files for linters', tasks.ignorefiles);
    grunt.registerTask('watch', 'Run tasks on file changes', tasks.watch);
    grunt.registerTask('yui', ['eslint:yui', 'shifter']);
    grunt.registerTask('amd', ['eslint:amd', 'babel']);
    grunt.registerTask('js', ['amd', 'yui']);

    // Register CSS taks.
    grunt.registerTask('css', ['stylelint:scss', 'sass', 'stylelint:css']);
    grunt.registerTask('scss', ['stylelint:scss', 'sass']);
    grunt.registerTask('rawcss', ['stylelint:css']);

    // Register the startup task.
    grunt.registerTask('startup', 'Run the correct tasks for the current directory', tasks.startup);

    // Register the default task.
    grunt.registerTask('default', ['startup']);
};
