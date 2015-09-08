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

/**
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Grunt configuration
 */

module.exports = function(grunt) {
    var path = require('path'),
        fs = require('fs'),
        tasks = {},
        cwd = process.env.PWD || process.cwd();

    // Project configuration.
    grunt.initConfig({
        jshint: {
            options: {jshintrc: '.jshintrc'},
            files: ['**/amd/src/*.js']
        },
        uglify: {
            dynamic_mappings: {
                files: grunt.file.expandMapping(
                    ['**/src/*.js', '!**/node_modules/**'],
                    '',
                    {
                        cwd: cwd,
                        rename: function(destBase, destPath) {
                            destPath = destPath.replace('src', 'build');
                            destPath = destPath.replace('.js', '.min.js');
                            destPath = path.resolve(cwd, destPath);
                            return destPath;
                        }
                    }
                )
            }
        }
    });

    tasks.shifter = function() {
       var  exec = require('child_process').spawn,
            done = this.async(),
            args = [],
            options = {
                recursive: true,
                watch: false,
                walk: false,
                module: false
            },
            shifter;

            args.push( path.normalize(__dirname + '/node_modules/shifter/bin/shifter'));

            // Determine the most appropriate options to run with based upon the current location.
            if (path.basename(cwd) === 'src') {
                // Detect whether we're in a src directory.
                grunt.log.debug('In a src directory');
                args.push('--walk');
                options.walk = true;
            } else if (path.basename(path.dirname(cwd)) === 'src') {
                // Detect whether we're in a module directory.
                grunt.log.debug('In a module directory');
                options.module = true;
            }

            if (grunt.option('watch')) {
                if (!options.walk && !options.module) {
                    grunt.fail.fatal('Unable to watch unless in a src or module directory');
                }

                // It is not advisable to run with recursivity and watch - this
                // leads to building the build directory in a race-like fashion.
                grunt.log.debug('Detected a watch - disabling recursivity');
                options.recursive = false;
                args.push('--watch');
            }

            if (options.recursive) {
                args.push('--recursive');
            }

            // Always ignore the node_modules directory.
            args.push('--excludes', 'node_modules');

            // Add the stderr option if appropriate
            if (grunt.option('verbose')) {
                args.push('--lint-stderr');
            }

            var execShifter = function() {

                shifter = exec("node", args, {
                    cwd: cwd,
                    stdio: 'inherit',
                    env: process.env
                });

                // Tidy up after exec.
                shifter.on('exit', function (code) {
                    if (code) {
                        grunt.fail.fatal('Shifter failed with code: ' + code);
                    } else {
                        grunt.log.ok('Shifter build complete.');
                        done();
                    }
                });
            };

            // Actually run shifter.
            if (!options.recursive) {
                execShifter();
            } else {
                // Check that there are yui modules otherwise shifter ends with exit code 1.
                var found = false;
                var hasYuiModules = function(directory, callback) {
                    fs.readdir(directory, function(err, files) {
                        if (err) {
                            return callback(err, null);
                        }

                        // If we already found a match there is no need to continue scanning.
                        if (found === true) {
                            return;
                        }

                        // We need to track the number of files to know when we return a result.
                        var pending = files.length;

                        // We first check files, so if there is a match we don't need further
                        // async calls and we just return a true.
                        for (var i = 0; i < files.length; i++) {
                            if (files[i] === 'yui') {
                                return callback(null, true);
                            }
                        }

                        // Iterate through subdirs if there were no matches.
                        files.forEach(function (file) {

                            var p = path.join(directory, file);
                            stat = fs.statSync(p);
                            if (!stat.isDirectory()) {
                                pending--;
                            } else {

                                // We defer the pending-1 until we scan the whole dir and subdirs.
                                hasYuiModules(p, function(err, result) {
                                    if (err) {
                                        return callback(err);
                                    }

                                    if (result === true) {
                                        // Once we get a true we notify the caller.
                                        found = true;
                                        return callback(null, true);
                                    }

                                    pending--;
                                    if (pending === 0) {
                                        // Notify the caller that the whole dir has been scaned and there are no matches.
                                        return callback(null, false);
                                    }
                                });
                            }

                            // No subdirs here, otherwise the return would be deferred until all subdirs are scanned.
                            if (pending === 0) {
                                return callback(null, false);
                            }
                        });
                    });
                };

                hasYuiModules(cwd, function(err, result) {
                    if (err) {
                        grunt.fail.fatal(err.message);
                    }

                    if (result === true) {
                        execShifter();
                    } else {
                        grunt.log.ok('No YUI modules to build.');
                        done();
                    }
                });
            }
    };

    tasks.startup = function() {
        // Are we in a YUI directory?
        if (path.basename(path.resolve(cwd, '../../')) == 'yui') {
            grunt.task.run('shifter');
        // Are we in an AMD directory?
        } else if (path.basename(cwd) == 'amd') {
            grunt.task.run('jshint');
            grunt.task.run('uglify');
        } else {
            // Run them all!.
            grunt.task.run('shifter');
            grunt.task.run('jshint');
            grunt.task.run('uglify');
        }
    };


    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    // Register the shifter task.
    grunt.registerTask('shifter', 'Run Shifter against the current directory', tasks.shifter);

    // Register the startup task.
    grunt.registerTask('startup', 'Run the correct tasks for the current directory', tasks.startup);

    // Register the default task.
    grunt.registerTask('default', ['startup']);
};
