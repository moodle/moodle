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
 * Course index main component.
 *
 * @module     core_courseformat/local/courseindex/courseindex
 * @class     core_courseformat/local/courseindex/courseindex
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'courseindex';
        // Default query selectors.
        this.selectors = {
            SECTION: `[data-for='section']`,
            SECTION_CMLIST: `[data-for='cmlist']`,
            CM: `[data-for='cm']`,
            TOGGLER: `[data-action="togglecourseindexsection"]`,
            COLLAPSE: `[data-toggle="collapse"]`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            SECTIONHIDDEN: 'dimmed',
            CMHIDDEN: 'dimmed',
            SECTIONCURRENT: 'current',
            COLLAPSED: `collapsed`,
        };
        // Arrays to keep cms and sections elements.
        this.sections = {};
        this.cms = {};
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {element|string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new Component({
            element: document.getElementById(target),
            reactive: getCurrentCourseEditor(),
            selectors,
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        // Activate section togglers.
        this.addEventListener(this.element, 'click', this._setupSectionTogglers);

        // Get cms and sections elements.
        const sections = this.getElements(this.selectors.SECTION);
        sections.forEach((section) => {
            this.sections[section.dataset.id] = section;
        });
        const cms = this.getElements(this.selectors.CM);
        cms.forEach((cm) => {
            this.cms[cm.dataset.id] = cm;
        });
    }

    getWatchers() {
        return [
            {watch: `cm:created`, handler: this._createCm},
            {watch: `cm:deleted`, handler: this._deleteCm},
            // Sections and cm sorting.
            {watch: `course.sectionlist:updated`, handler: this._refreshCourseSectionlist},
            {watch: `section.cmlist:updated`, handler: this._refreshSectionCmlist},
        ];
    }

    /**
     * Setup sections toggler.
     *
     * Toggler click is delegated to the main course index element because new sections can
     * appear at any moment and this way we prevent accidental double bindings.
     *
     * @param {Event} event the triggered event
     */
    _setupSectionTogglers(event) {
        const sectionlink = event.target.closest(this.selectors.TOGGLER);
        if (sectionlink) {
            const toggler = sectionlink.parentNode.querySelector(this.selectors.COLLAPSE);
            if (toggler?.classList.contains(this.classes.COLLAPSED)) {
                toggler.click();
            }
        }
    }

    /**
     * Create a newcm instance.
     *
     * @param {Object} details the update details.
     */
    async _createCm({state, element}) {
        // Create a fake node while the component is loading.
        const fakeelement = document.createElement('li');
        fakeelement.classList.add('bg-pulse-grey', 'w-100');
        fakeelement.innerHTML = '&nbsp;';
        this.cms[element.id] = fakeelement;
        // Place the fake node on the correct position.
        this._refreshSectionCmlist({
            state,
            element: state.section.get(element.sectionid),
        });
        // Collect render data.
        const exporter = this.reactive.getExporter();
        const data = exporter.cm(state, element);
        // Create the new content.
        const newcomponent = await this.renderComponent(fakeelement, 'core_courseformat/local/courseindex/cm', data);
        // Replace the fake node with the real content.
        const newelement = newcomponent.getElement();
        this.cms[element.id] = newelement;
        fakeelement.parentNode.replaceChild(newelement, fakeelement);
    }

    /**
     * Refresh a section cm list.
     *
     * @param {Object} details the update details.
     */
    _refreshSectionCmlist({element}) {
        const cmlist = element.cmlist ?? [];
        const listparent = this.getElement(this.selectors.SECTION_CMLIST, element.id);
        this._fixOrder(listparent, cmlist, this.cms);
    }

    /**
     * Refresh the section list.
     *
     * @param {Object} details the update details.
     */
    _refreshCourseSectionlist({element}) {
        const sectionlist = element.sectionlist ?? [];
        this._fixOrder(this.element, sectionlist, this.sections);
    }

    /**
     * Fix/reorder the section or cms order.
     *
     * @param {Element} container the HTML element to reorder.
     * @param {Array} neworder an array with the ids order
     * @param {Array} allitems the list of html elements that can be placed in the container
     */
    _fixOrder(container, neworder, allitems) {

        // Empty lists should not be visible.
        if (!neworder.length) {
            container.classList.add('hidden');
            container.innerHTML = '';
            return;
        }

        // Grant the list is visible (in case it was empty).
        container.classList.remove('hidden');

        // Move the elements in order at the beginning of the list.
        neworder.forEach((itemid, index) => {
            const item = allitems[itemid];
            // Get the current element at that position.
            const currentitem = container.children[index];
            if (currentitem === undefined) {
                container.append(item);
                return;
            }
            if (currentitem !== item) {
                container.insertBefore(item, currentitem);
            }
        });
        // Remove the remaining elements.
        while (container.children.length > neworder.length) {
            container.removeChild(container.lastChild);
        }
    }

    /**
     * Remove a cm from the list.
     *
     * The actual DOM element removal is delegated to the cm component.
     *
     * @param {Object} details the update details.
     */
    _deleteCm({element}) {
        delete this.cms[element.id];
    }
}
