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
 * The collapsable sections controls.
 *
 * @module     core/local/collapsable_section/controls
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Example of controlling a collapsable section.</caption>
 *
 * import CollapsableSection from 'core/local/collapsable_section/controls';
 *
 * const section = CollapsableSection.instanceFromSelector('#MyCollapsableSection');
 *
 * // Use hide, show and toggle methods to control the section.
 * section.hide();
 */

import {
    eventTypes,
    notifyCollapsableSectionHidden,
    notifyCollapsableSectionShown
} from 'core/local/collapsable_section/events';

// The jQuery module is only used for interacting with Boostrap 4. It can we removed when MDL-71979 is integrated.
import jQuery from 'jquery';

let initialized = false;

export default class {
    /**
     * Create a new instance from a query selector.
     *
     * @param {String} selector The selector of the collapsable section.
     * @return {CollapsableSection} The collapsable section controls.
     * @throws {Error} If no elements are found with the selector.
     */
    static instanceFromSelector(selector) {
        const elements = document.querySelector(selector);
        if (!elements) {
            throw new Error('No elements found with the selector: ' + selector);
        }
        return new this(elements);
    }

    /**
     * Initialize the collapsable section controls.
     */
    static init() {
        if (initialized) {
            return;
        }
        initialized = true;

        // We want to add extra events to the standard bootstrap collapsable events.
        // TODO: change all jquery events to custom events once MDL-71979 is integrated.
        jQuery(document).on(eventTypes.hiddenBsCollapse, event => {
            if (!this.isCollapsableComponent(event.target)) {
                return;
            }
            notifyCollapsableSectionHidden(event.target);
        });
        jQuery(document).on(eventTypes.shownBsCollapse, event => {
            if (!this.isCollapsableComponent(event.target)) {
                return;
            }
            notifyCollapsableSectionShown(event.target);
        });
    }

    /**
     * Check if the element is a collapsable section.
     *
     * @private
     * @param {HTMLElement} element The element to check.
     * @return {boolean} True if the element is a collapsable section.
     */
    static isCollapsableComponent(element) {
        return element.hasAttribute('data-mdl-component')
            && element.getAttribute('data-mdl-component') === 'core/local/collapsable_section';
    }

    /**
     * Creates an instance of the controls for a collapsable section.
     *
     * @param {HTMLElement} element - The DOM element that this control will manage.
     */
    constructor(element) {
        this.element = element;
    }

    /**
     * Hides the collapsible section element.
     */
    hide() {
        // TODO: change all jquery once MDL-71979 is integrated.
        jQuery(this.element).collapse('hide');
    }

    /**
     * Shows the collapsible section element.
     */
    show() {
        // TODO: change all jquery once MDL-71979 is integrated.
        jQuery(this.element).collapse('show');
    }

    /**
     * Toggle the collapsible section element.
     */
    toggle() {
        // TODO: change all jquery once MDL-71979 is integrated.
        jQuery(this.element).collapse('toggle');
    }

    /**
     * Check if the collapsable section is visible.
     *
     * @return {boolean} True if the collapsable section is visible.
     */
    isVisible() {
        return this.element.classList.contains('show');
    }
}
