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
        async = require('async');

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

    // Project configuration.
    grunt.initConfig({
        shifter: {
            options: {
                recursive: true,
                paths: files ? files : [cwd]
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

    // Register JS tasks.
    grunt.registerTask('shifter', 'Run Shifter against the current directory', tasks.shifter);


    // Register the default task.
    grunt.registerTask('default', ['shifter']);
};
