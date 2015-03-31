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
 * URL utility functions.
 *
 * @module     core/url
 * @package    core
 * @class      url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['core/config'], function(config) {


    return /** @alias module:core/url */ {
        // Public variables and functions.
        /**
         * Generate a style tag referencing this sheet and add it to the head of the page.
         *
         * @method fileUrl
         * @param {string} sheet The style sheet name. Must exist in the theme, or one of it's parents.
         * @return {string}
         */
        fileUrl: function(relativeScript, slashArg) {

            var url = config.wwwroot + relativeScript;

            // Force a /
            if (slashArg.charAt(0) != '/') {
                slashArg = '/' + slashArg;
            }
            if (config.slasharguments) {
                url += slashArg;
            } else {
                url += '?file=' + encodeURIComponent(slashArg);
            }
            return url;
        },

        /**
         * Take a path relative to the moodle basedir and do some fixing (see class moodle_url in php).
         *
         * @method relativeUrl
         * @param {string} relativePath The path relative to the moodle basedir.
         * @return {string}
         */
        relativeUrl: function(relativePath) {

            if (relativePath.indexOf('http:') === 0 || relativePath.indexOf('https:') === 0 || relativePath.indexOf('://')) {
                throw new Error('relativeUrl function does not accept absolute urls');
            }

            // Fix non-relative paths;
            if (relativePath.charAt(0) != '/') {
                relativePath = '/' + relativePath;
            }

            // Fix admin urls.
            if (config.admin !== 'admin') {
                relativePath = relativePath.replace(/^\/admin\//, '/' + config.admin + '/');
            }
            return config.wwwroot + relativePath;
        },

        /**
         * Wrapper for image_url function.
         *
         * @method imageUrl
         * @param {string} imagename The image name (e.g. t/edit).
         * @param {string} component The component (e.g. mod_feedback).
         * @return {string}
         */
        imageUrl: function(imagename, component) {
            return M.util.image_url(imagename, component);
        }
    };
});
