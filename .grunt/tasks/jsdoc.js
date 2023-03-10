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
 * @copyright  2021 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = (grunt) => {
    const path = require('path');

    grunt.registerTask('jsdoc', 'Generate JavaScript documentation using jsdoc', function() {
        const done = this.async();
        const configuration = path.resolve('.grunt/jsdoc/jsdoc.conf.js');

        grunt.util.spawn({
            cmd: 'jsdoc',
            args: [
                '--configure',
                configuration,
            ]
        }, function(error, result, code) {
            if (result.stdout) {
                grunt.log.write(result.stdout);
            }

            if (result.stderr) {
                grunt.log.error(result.stderr);
            }
            if (error) {
                grunt.fail.fatal(`JSDoc failed with error code ${code}`);
            } else {
                grunt.log.write('JSDoc completed successfully'.green);
            }
            done();
        });
    });
};
