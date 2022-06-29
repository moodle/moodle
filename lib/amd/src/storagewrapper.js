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
 * Wrap an instance of the browser's local or session storage to handle
 * cache expiry, key namespacing and other helpful things.
 *
 * @module     core/storagewrapper
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/config'], function(config) {

    /**
     * Constructor.
     *
     * @param {object} storage window.localStorage or window.sessionStorage
     */
    var Wrapper = function(storage) {
        this.storage = storage;
        this.supported = this.detectSupport();
        this.hashSource = config.wwwroot + '/' + config.jsrev;
        this.hash = this.hashString(this.hashSource);
        this.prefix = this.hash + '/';
        this.jsrevPrefix = this.hashString(config.wwwroot) + '/jsrev';
        this.validateCache();
    };

    /**
     * Check if the browser supports the type of storage.
     *
     * @method detectSupport
     * @return {boolean} True if the browser supports storage.
     */
    Wrapper.prototype.detectSupport = function() {
        if (config.jsrev == -1) {
            // Disable cache if debugging.
            return false;
        }
        if (typeof (this.storage) === "undefined") {
            return false;
        }
        var testKey = 'test';
        try {
            if (this.storage === null) {
                return false;
            }
            // MDL-51461 - Some browsers misreport availability of the storage
            // so check it is actually usable.
            this.storage.setItem(testKey, '1');
            this.storage.removeItem(testKey);
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
    Wrapper.prototype.prefixKey = function(key) {
        return this.prefix + key;
    };

    /**
     * Check the current jsrev version and clear the cache if it has been bumped.
     *
     * @method validateCache
     */
    Wrapper.prototype.validateCache = function() {
        var cacheVersion = this.storage.getItem(this.jsrevPrefix);
        if (cacheVersion === null) {
            this.storage.setItem(this.jsrevPrefix, config.jsrev);
            return;
        }

        var moodleVersion = config.jsrev;
        if (moodleVersion != cacheVersion) {
            this.storage.clear();
            this.storage.setItem(this.jsrevPrefix, config.jsrev);
        }
    };

    /**
     * Hash a string, used to make shorter key prefixes.
     *
     * @method hashString
     * @param {String} source The string to hash
     * @return {Number}
     */
    Wrapper.prototype.hashString = function(source) {
        // From http://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript-jquery.
        /* jshint bitwise: false */
        /* eslint no-bitwise: "off" */
        var hash = 0;
        var i, chr, len;
        if (source.length === 0) {
            return hash;
        }
        for (i = 0, len = source.length; i < len; i++) {
            chr = source.charCodeAt(i);
            hash = ((hash << 5) - hash) + chr;
            hash |= 0; // Convert to 32bit integer
        }
        return hash;
    };

    /**
     * Get a value from local storage. Remember - all values must be strings.
     *
     * @method get
     * @param {string} key The cache key to check.
     * @return {boolean|string} False if the value is not in the cache, or some other error - a string otherwise.
     */
    Wrapper.prototype.get = function(key) {
        if (!this.supported) {
            return false;
        }
        key = this.prefixKey(key);

        return this.storage.getItem(key);
    };

    /**
     * Set a value to local storage. Remember - all values must be strings.
     *
     * @method set
     * @param {string} key The cache key to set.
     * @param {string} value The value to set.
     * @return {boolean} False if the value can't be saved in the cache, or some other error - true otherwise.
     */
    Wrapper.prototype.set = function(key, value) {
        if (!this.supported) {
            return false;
        }
        key = this.prefixKey(key);
        // This can throw exceptions when the storage limit is reached.
        try {
            this.storage.setItem(key, value);
        } catch (e) {
            return false;
        }
        return true;
    };

    return Wrapper;
});
