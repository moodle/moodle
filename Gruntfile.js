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
        tasks = {};

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
                        cwd: process.env.PWD,
                        rename: function(destBase, destPath) {
                            destPath = destPath.replace('src', 'build');
                            destPath = destPath.replace('.js', '.min.js');
                            destPath = path.resolve(process.env.PWD, destPath);
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

            // Determine the most appropriate options to run with based upon the current location.
            if (path.basename(process.env.PWD) === 'src') {
                // Detect whether we're in a src directory.
                grunt.log.debug('In a src directory');
                args.push('--walk');
                options.walk = true;
            } else if (path.basename(path.dirname(process.env.PWD)) === 'src') {
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

            // Actually run shifter.
            shifter = exec(process.cwd() + '/node_modules/shifter/bin/shifter', args, {
                cwd: process.env.PWD,
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

    tasks.startup = function() {
        // Are we in a YUI directory?
        if (path.basename(path.resolve(process.env.PWD, '../../')) == 'yui') {
            grunt.task.run('shifter');
        // Are we in an AMD directory?
        } else if (path.basename(process.env.PWD) == 'amd') {
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
