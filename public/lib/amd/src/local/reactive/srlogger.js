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
 * Screen reader-only (visually-hidden) reactive mutations logger class.
 *
 * This logger can be used by the StateManager to log mutation feedbacks and actions.
 * The feedback messages logged by this logger will be rendered in a visually-hidden, ARIA live region.
 *
 * @module     core/local/reactive/srlogger
 * @class      SRLogger
 * @copyright  2023 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Logger from 'core/local/reactive/logger';

/**
 * Logger entry structure.
 *
 * @typedef {object} LoggerEntry
 * @property {string} feedbackMessage Feedback message.
 */

/**
 * Screen reader-only (visually-hidden) reactive mutations logger class.
 *
 * @class SRLogger
 */
export default class SRLogger extends Logger {
    /**
     * The element ID of the ARIA live region where the logger feedback will be rendered.
     *
     * @type {string}
     */
    static liveRegionId = 'sr-logger-feedback-container';

    /**
     * Add a log entry.
     * @param {LoggerEntry} entry Log entry.
     */
    add(entry) {
        if (entry.feedbackMessage) {
            // Fetch or create an ARIA live region that will serve as the container for the logger feedback.
            let loggerFeedback = document.getElementById(SRLogger.liveRegionId);
            if (!loggerFeedback) {
                loggerFeedback = document.createElement('div');
                loggerFeedback.id = SRLogger.liveRegionId;
                loggerFeedback.classList.add('visually-hidden');
                loggerFeedback.setAttribute('aria-live', 'polite');
                document.body.append(loggerFeedback);
            }
            // Set the ARIA live region's contents with the feedback.
            loggerFeedback.innerHTML = entry.feedbackMessage;

            // Clear the feedback message after 4 seconds to avoid the contents from being read out in case the user navigates
            // to this region. This is similar to the default timeout of toast messages before disappearing from view.
            setTimeout(() => {
                loggerFeedback.innerHTML = '';
            }, 4000);
        }
    }
}
