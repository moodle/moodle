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

module.exports = function(grunt) {
    var path = require('path'),
        tasks = {},
        cwd = process.env.PWD || process.cwd(),
        async = require('async'),
        DOMParser = require('xmldom').DOMParser,
        xpath = require('xpath'),
        semver = require('semver');

    // Verify the node version is new enough.
    var expected = semver.validRange(grunt.file.readJSON('package.json').engines.node);
    var actual = semver.valid(process.version);
    if (!semver.satisfies(actual, expected)) {
        grunt.fail.fatal('Node version too old. Require ' + expected + ', version installed: ' + actual);
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

    var inAMD = path.basename(cwd) == 'amd';

    // Globbing pattern for matching all AMD JS source files.
    var amdSrc = [inAMD ? cwd + '/src/*.js' : '**/amd/src/*.js'];

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
    var uglifyRename = function(destPath, srcPath) {
        destPath = srcPath.replace('src', 'build');
        destPath = destPath.replace('.js', '.min.js');
        destPath = path.resolve(cwd, destPath);
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
            amd: {
              src: amdSrc,
              // Check AMD with some slightly stricter rules.
              rules: {
                'no-unused-vars': 'error',
                'no-implicit-globals': 'error'
              }
            },
            // Check YUI module source files.
            yui: {
               src: ['**/yui/src/**/*.js', '!*/**/yui/src/*/meta/*.js'],
               options: {
                   // Disable some rules which we can't safely define for YUI rollups.
                   rules: {
                     'no-undef': 'off',
                     'no-unused-vars': 'off',
                     'no-unused-expressions': 'off'
                   }
               }
            }
        },
        uglify: {
            amd: {
                files: [{
                    expand: true,
                    src: amdSrc,
                    rename: uglifyRename
                }],
                options: {report: 'none'}
            }
        },
        less: {
            bootstrapbase: {
                files: {
                    "theme/bootstrapbase/style/moodle.css": "theme/bootstrapbase/less/moodle.less",
                    "theme/bootstrapbase/style/editor.css": "theme/bootstrapbase/less/editor.less",
                },
                options: {
                    compress: false // We must not compress to keep the comments.
                }
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
            bootstrapbase: {
                files: ["theme/bootstrapbase/less/**/*.less"],
                tasks: ["css"]
            },
            yui: {
                files: ['**/yui/src/**/*.js'],
                tasks: ['yui']
            },
        },
        shifter: {
            options: {
                recursive: true,
                paths: [cwd]
            }
        },
        stylelint: {
            less: {
                options: {
                    syntax: 'less',
                    configOverrides: {
                        rules: {
                            // These rules have to be disabled in .stylelintrc for scss compat.
                            "at-rule-no-unknown": true,
                            "no-browser-hacks": [true, {"severity": "warning"}]
                        }
                    }
                },
                src: ['theme/**/*.less']
            },
            scss: {
                options: {syntax: 'scss'},
                src: ['*/**/*.scss']
            },
            css: {
                src: ['*/**/*.css'],
                options: {
                    configOverrides: {
                        rules: {
                            // These rules have to be disabled in .stylelintrc for scss compat.
                            "at-rule-no-unknown": true,
                            "no-browser-hacks": [true, {"severity": "warning"}]
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
          'theme/bootstrapbase/style/',
          'theme/clean/style/custom.css',
          'theme/more/style/custom.css'
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
        }
    };

    // On watch, we dynamically modify config to build only affected files. This
    // method is slightly complicated to deal with multiple changed files at once (copied
    // from the grunt-contrib-watch readme).
    var changedFiles = Object.create(null);
    var onChange = grunt.util._.debounce(function() {
          var files = Object.keys(changedFiles);
          grunt.config('eslint.amd.src', files);
          grunt.config('eslint.yui.src', files);
          grunt.config('uglify.amd.files', [{expand: true, src: files, rename: uglifyRename}]);
          grunt.config('shifter.options.paths', files);
          grunt.config('stylelint.less.src', files);
          changedFiles = Object.create(null);
    }, 200);

    grunt.event.on('watch', function(action, filepath) {
          changedFiles[filepath] = action;
          onChange();
    });

    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-stylelint');

    // Register JS tasks.
    grunt.registerTask('shifter', 'Run Shifter against the current directory', tasks.shifter);
    grunt.registerTask('ignorefiles', 'Generate ignore files for linters', tasks.ignorefiles);
    grunt.registerTask('yui', ['eslint:yui', 'shifter']);
    grunt.registerTask('amd', ['eslint:amd', 'uglify']);
    grunt.registerTask('js', ['amd', 'yui']);

    // Register CSS taks.
    grunt.registerTask('css', ['stylelint:scss', 'stylelint:less', 'less:bootstrapbase', 'stylelint:css']);

    // Register the startup task.
    grunt.registerTask('startup', 'Run the correct tasks for the current directory', tasks.startup);

    // Register the default task.
    grunt.registerTask('default', ['startup']);
};
