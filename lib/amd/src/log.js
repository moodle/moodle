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
 * This is an empty module, that is required before all other modules.
 * Because every module is returned from a request for any other module, this
 * forces the loading of all modules with a single request.
 *
 * @module     core/log
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/loglevel'], function(log) {
    var originalFactory = log.methodFactory;
    log.methodFactory = function(methodName, logLevel) {
        var rawMethod = originalFactory(methodName, logLevel);

        return function(message, source) {
            if (source) {
                rawMethod(source + ": " + message);
            } else {
                rawMethod(message);
            }
        };
    };

    /**
     * Set default config settings.
     *
     * @param {Object} config including the level to use.
     * @method setConfig
     */
    log.setConfig = function(config) {
        if (typeof config.level !== "undefined") {
            log.setLevel(config.level);
        }
    };

    return log;
});
