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
 * Default reactive mutations logger class.
 *
 * This logger is used by default by the StateManager to log mutation feedbacks
 * and actions. By default, feedbacks will be displayed as a toast. However, the
 * reactive instance can provide alternative loggers to provide advanced logging
 * capabilities.
 *
 * @module     core/local/reactive/logger
 * @class      Logger
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Logger entry structure.
 *
 * @typedef {object} LoggerEntry
 * @property {string} feedbackMessage Feedback message.
 */

import {add as addToast} from 'core/toast';

/**
 * Default reactive mutations logger class.
 * @class Logger
 */
export default class Logger {
    /**
     * Constructor.
     */
    constructor() {
        this._debug = false;
    }

    /**
     * Add a log entry.
     * @param {LoggerEntry} entry Log entry.
     */
    add(entry) {
        if (entry.feedbackMessage) {
            addToast(entry.feedbackMessage);
        }
    }
}
