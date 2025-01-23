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
 * The collapsable section events.
 *
 * This module wraps the standard bootstrap collapsable events, but for collapsable sections.
 *
 * @module     core/local/collapsable_section/events
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Example of listening to a collapsable section events.</caption>
 * import {eventTypes as collapsableSectionEventTypes} from 'core/local/collapsable_section/events';
 *
 * document.addEventListener(collapsableSectionEventTypes.shown, event => {
 *     window.console.log(event.target); // The HTMLElement relating to the block whose content was updated.
 * });
 */

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Events for `core_block`.
 *
 * @constant
 * @property {String} blockContentUpdated See {@link event:blockContentUpdated}
 */
export const eventTypes = {
    /**
     * An event triggered when the content of a block has changed.
     *
     * @event blockContentUpdated
     * @type {CustomEvent}
     * @property {HTMLElement} target The block element that was updated
     * @property {object} detail
     * @property {number} detail.instanceId The block instance id
     */
    shown: 'core_collapsable_section_shown',
    hidden: 'core_collapsable_section_hidden',
    // All Bootstrap 4 jQuery events are wrapped while MDL-71979 is not integrated.
    hideBsCollapse: 'hide.bs.collapse',
    hiddenBsCollapse: 'hidden.bs.collapse',
    showBsCollapse: 'show.bs.collapse',
    shownBsCollapse: 'shown.bs.collapse',
};

/**
 * Trigger an event to indicate that the content of a block was updated.
 *
 * @method notifyBlockContentUpdated
 * @param {HTMLElement} element The HTMLElement containing the updated block.
 * @returns {CustomEvent}
 * @fires blockContentUpdated
 */
export const notifyCollapsableSectionShown = element => dispatchEvent(
    eventTypes.shown,
    {},
    element
);

/**
 * Trigger an event to indicate that the content of a block was updated.
 *
 * @method notifyBlockContentUpdated
 * @param {HTMLElement} element The HTMLElement containing the updated block.
 * @returns {CustomEvent}
 * @fires blockContentUpdated
 */
export const notifyCollapsableSectionHidden = element => dispatchEvent(
    eventTypes.hidden,
    {},
    element
);
