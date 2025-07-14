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
 * Simple API for set/get to localstorage, with cacherev expiration.
 *
 * @module     core/localstorage
 * @class      localstorage
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['core/config', 'core/storagewrapper'], function(config, StorageWrapper) {

    // Private functions and variables.
    /** @var {Object} StorageWrapper - Wraps browsers localStorage object */
    var storage = new StorageWrapper(window.localStorage);

    return /** @alias module:core/localstorage */ {
        /**
         * Get a value from local storage. Remember - all values must be strings.
         *
         * @method get
         * @param {string} key The cache key to check.
         * @return {boolean|string} False if the value is not in the cache, or some other error - a string otherwise.
         */
        get: function(key) {
            return storage.get(key);
        },

        /**
         * Set a value to local storage. Remember - all values must be strings.
         *
         * @method set
         * @param {string} key The cache key to set.
         * @param {string} value The value to set.
         * @return {boolean} False if the value can't be saved in the cache, or some other error - true otherwise.
         */
        set: function(key, value) {
            return storage.set(key, value);
        },

        /**
         * Clean local storage
         *
         * @method clean
         */
        clean: function() {
            return storage.clean();
        }

    };
});
