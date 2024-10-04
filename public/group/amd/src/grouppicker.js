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
 * @module     core_group/groupPicker
 * @copyright  2022 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class used for interfacing with the group select picker.
 *
 * @class core_group/GroupPicker
 */
export default class GroupPicker {
    /**
     * Creates the group picker class and finds the corresponding DOM element.
     *
     * @param {String} elementId The DOM element id of the <select> input
     * @throws Error if the element was not found.
     */
    constructor(elementId = "groups") {
        const pickerDomElement = document.getElementById(elementId);

        if (!pickerDomElement) {
            throw new Error("Groups picker was not found.");
        }

        this.element = pickerDomElement;
    }

    /**
     * Returns the DOM element this class is linked to.
     *
     * @returns {HTMLElement} The DOM element
     */
    getDomElement() {
        return this.element;
    }

    /**
     * Returns the selected group values.
     *
     * @returns {Number[]} The group IDs that are currently selected.
     */
    getSelectedValues() {
        const selectedOptionElements = Array.from(this.element.querySelectorAll("option:checked"));
        const selectedGroups = selectedOptionElements.map(el => parseInt(el.value));

        return selectedGroups;
    }
}
