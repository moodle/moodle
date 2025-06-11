// This file is part of Moodle - http://moodle.org/ //
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
 * Javascript events for the `process_monitor` module.
 *
 * @module     core/local/process_monitor/events
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.2
 */

/**
 * Events for the `core_editor` subsystem.
 *
 * @constant
 * @property {String} processMonitorStateChange See {@link event:processMonitorStateChange}
 */
export const eventTypes = {
    /**
     * An event triggered when the monitor state has changed.
     *
     * @event processMonitorStateChange
     */
    processMonitorStateChange: 'core_editor/contentRestored',
};

/**
 * Trigger a state changed event.
 *
 * @method dispatchStateChangedEvent
 * @param {Object} detail the full state
 * @param {Object} target the custom event target (document if none provided)
 * @param {Function} target.dispatchEvent the component dispatch event method.
 */
export function dispatchStateChangedEvent(detail, target) {
    if (target === undefined) {
        target = document;
    }
    target.dispatchEvent(new CustomEvent(
        eventTypes.processMonitorStateChange,
        {
            bubbles: true,
            detail: detail,
        }
    ));
}
