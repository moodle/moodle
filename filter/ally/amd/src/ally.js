/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Ally AX library.
 *
 * @package
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import Log from 'core/log';

class Ally {
    #config = null;
    #token = null;
    #baseUrl = null;

    static #initialized = false;

    /**
     * Initialize the AMD module with the necessary data
     * @param  {String} jwt    The JWT token
     * @param  {Object} config The Ally configuration containing the Ally client id and admin URL
     */
    init = function(jwt, config) {
        if (Ally.#initialized) {
            // Already initialized - return.
            return;
        }
        Ally.#initialized = true;
        if (!config.adminurl) {
            // Do not localise - just a debug message.
            Log.info('The Ally admin tool is not configured with a Launch URL. Aborting JS load.');
            return;
        }

        /**
         * Get the base URL for a given url.
         *
         * e.g.,  given `https://ally.local/api/v1/20/lti/institution`, this function will return `https://ally.local`.
         *
         * @param  {String} url A full URL
         * @return {String} The base URL of the given `url`.
         */
        const getBaseUrl = (url) => {
            const parser = document.createElement('a');
            parser.href = url;

            let baseUrl = parser.protocol + '//' + parser.hostname;
            if (parser.port) {
                baseUrl += ':' + parser.port;
            }

            return baseUrl;
        };

        this.#token = jwt;
        this.#config = config;
        this.#baseUrl = getBaseUrl(config.adminurl);

        // Load up the Ally script.
        // Note - this is not to be cached as it is just a loader script.
        // The target script below loads up the latest version of the amd module which does get cached.
        $.getScript(this.#baseUrl + '/integration/moodlerooms/ally.js')
            .fail(function() {
                Log.error('Failed to load Ally JS');
            });
    };

    /**
     * Get the JWT token that can be used to authenticate the current user
     * @return {String} The JWT token
     */
    token = function() {
        return this.#token;
    };

    /**
     * Get the Ally configuration containing the Ally client id and base URL
     * @return {Object} The Ally configuration
     */
    config = function() {
        return this.#config;
    };

    /**
     * Get the Ally base URL
     * @return {String} The Ally base URL
     */
    getAllyBaseUrl = function() {
        return this.#baseUrl;
    };
}

export default new Ally();
