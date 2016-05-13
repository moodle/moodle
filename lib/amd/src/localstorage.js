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
 * @package    core
 * @class      localstorage
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['core/config'], function(config) {

    // Private functions and variables.
    /** @var {boolean} supported - Is localstorage supported in this browser? */
    var supported = false;
    /** @var {string} prefix - Prefix to use on all cache keys */
    var prefix = '';
    /** @var {jsrevPrefix} jsrevPrefix - Key to store the current jsrev version for the cache */
    var jsrevPrefix = '';
    /** @var {Object} localStorage - Browsers localStorage object */
    var localStorage = null;

    /**
     * Check if the browser supports local storage.
     *
     * @method detectSupport
     * @return {boolean} True if the browser supports local storage.
     */
    var detectSupport = function() {
        if (config.jsrev == -1) {
            // Disable cache if debugging.
            return false;
        }
        if (typeof(window.localStorage) === "undefined") {
            return false;
        }
        var testKey = 'test';
        try {
            localStorage = window.localStorage;
            if (localStorage === null) {
                return false;
            }
            // MDL-51461 - Some browsers misreport availability of local storage
            // so check it is actually usable.
            localStorage.setItem(testKey, '1');
            localStorage.removeItem(testKey);
            return true;
        } catch (ex) {
            return false;
        }
    };

    /**
     * Add a unique prefix to all keys so multiple moodle sites do not share caches.
     *
     * @method prefixKey
     * @param {string} key The cache key to prefix.
     * @return {string} The new key
     */
    var prefixKey = function(key) {
        return prefix + key;
    };

    /**
     * Check the current jsrev version and clear the cache if it has been bumped.
     *
     * @method validateCache
     */
    var validateCache = function() {
        var cacheVersion = localStorage.getItem(jsrevPrefix);
        if (cacheVersion === null) {
            localStorage.setItem(jsrevPrefix, config.jsrev);
            return;
        }
        var moodleVersion = config.jsrev;

        if (moodleVersion != cacheVersion) {
            localStorage.clear();
            localStorage.setItem(jsrevPrefix, config.jsrev);
        }
    };

    /**
     * Hash a string, used to make shorter key prefixes.
     *
     * @method hashString
     * @param string source The string to hash
     * @return int The int hash
     */
    var hashString = function(source) {
        // From http://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript-jquery.
        /* jshint bitwise: false */
        var hash = 0, i, chr, len;
        if (source.length === 0) {
            return hash;
        }
        for (i = 0, len = source.length; i < len; i++) {
            chr   = source.charCodeAt(i);
            hash  = ((hash << 5) - hash) + chr;
            hash |= 0; // Convert to 32bit integer
        }
        return hash;
    };

    /**
     * Init this module.
     *
     * This computes the hash prefixes from jsrev and friends.
     */
    var init = function() {
        supported = detectSupport();
        var hashSource = config.wwwroot + '/' + config.jsrev;

        var hash = hashString(hashSource);
        prefix = hash + '/';
        hashSource = config.wwwroot + '/';
        hash = hashString(hashSource);
        jsrevPrefix = hash + '/jsrev';
    };

    // Run the module init.
    init();

    return /** @alias module:core/localstorage */ {
        /**
         * Get a value from local storage. Remember - all values must be strings.
         *
         * @method get
         * @param {string} key The cache key to check.
         * @return {boolean|string} False if the value is not in the cache, or some other error - a string otherwise.
         */
        get: function(key) {
            if (!supported) {
                return false;
            }
            validateCache();
            key = prefixKey(key);

            return localStorage.getItem(key);
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
            if (!supported) {
                return false;
            }
            validateCache();
            key = prefixKey(key);
            // This can throw exceptions when the storage limit is reached.
            try {
                localStorage.setItem(key, value);
            } catch (e) {
                return false;
            }
            return true;
        }

    };
});
