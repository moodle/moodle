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
        cwd = process.env.PWD || process.cwd();

    // Windows users can't run grunt in a subdirectory, so allow them to set
    // the root by passing --root=path/to/dir.
    if (grunt.option('root')) {
        var root = grunt.option('root');
        if (grunt.file.exists(__dirname, root)) {
            cwd = path.join(__dirname, root);
            grunt.log.ok('Setting root to '+cwd);
        } else {
            grunt.fail.fatal('Setting root to '+root+' failed - path does not exist');
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
    var uglify_rename = function (destPath, srcPath) {
        destPath = srcPath.replace('src', 'build');
        destPath = destPath.replace('.js', '.min.js');
        destPath = path.resolve(cwd, destPath);
        return destPath;
    };

    // Project configuration.
    grunt.initConfig({
        jshint: {
            options: {jshintrc: '.jshintrc'},
            amd: { src: amdSrc }
        },
        uglify: {
            amd: {
                files: [{
                    expand: true,
                    src: amdSrc,
                    rename: uglify_rename
                }]
            }
        },
        less: {
            bootstrapbase: {
                files: {
                    "theme/bootstrapbase/style/moodle.css": "theme/bootstrapbase/less/moodle.less",
                    "theme/bootstrapbase/style/editor.css": "theme/bootstrapbase/less/editor.less",
                },
                options: {
                    compress: true
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
                tasks: ["less:bootstrapbase"]
            },
            yui: {
                files: ['**/yui/src/**/*.js'],
                tasks: ['shifter']
            },
        },
        shifter: {
            options: {
                recursive: true,
                paths: [cwd]
            }
        }
    });

    /**
     * Shifter task. Is configured with a path to a specific file or a directory,
     * in the case of a specific file it will work out the right module to be built.
     *
     * Note that this task runs the invidiaul shifter jobs async (becase it spawns
     * so be careful to to call done().
     */
    tasks.shifter = function() {
        var async = require('async'),
            done = this.async(),
            options = grunt.config('shifter.options');

        // Run the shifter processes one at a time to avoid confusing output.
        async.eachSeries(options.paths, function (src, filedone) {
            var args = [];
            args.push( path.normalize(__dirname + '/node_modules/shifter/bin/shifter'));

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
                }, function (error, result, code) {
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
            grunt.task.run('shifter');
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
          grunt.config('jshint.amd.src', files);
          grunt.config('uglify.amd.files', [{ expand: true, src: files, rename: uglify_rename }]);
          grunt.config('shifter.options.paths', files);
          changedFiles = Object.create(null);
    }, 200);

    grunt.event.on('watch', function(action, filepath) {
          changedFiles[filepath] = action;
          onChange();
    });

    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Register JS tasks.
    grunt.registerTask('shifter', 'Run Shifter against the current directory', tasks.shifter);
    grunt.registerTask('amd', ['jshint', 'uglify']);
    grunt.registerTask('js', ['amd', 'shifter']);

    // Register CSS taks.
    grunt.registerTask('css', ['less:bootstrapbase']);

    // Register the startup task.
    grunt.registerTask('startup', 'Run the correct tasks for the current directory', tasks.startup);

    // Register the default task.
    grunt.registerTask('default', ['startup']);
};
