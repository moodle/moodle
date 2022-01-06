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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2016 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Grunt configuration
 */
module.exports = function(grunt) {
    var cwd = process.cwd();

    grunt.initConfig({
        less: {
            // Compile moodle styles.
            mod_webex: {
                options: {
                    compress: false
                },
                src: 'less/styles.less',
                dest: 'styles.css'
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["less/**/*.less"],
            tasks: ["lessmwx"],
            options: {
                spawn: false,
                livereload: true
            }
        }
    });

    // Load contrib tasks from Moodle.
    process.chdir(__dirname + '/../..');
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks('grunt-contrib-watch');
    process.chdir(cwd);

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("lessmwx", ['less:mod_webex']);

};
