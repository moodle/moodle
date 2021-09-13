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
 * @module     core_courseformat/local/content
 * @class      core_courseformat/local/content
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import inplaceeditable from 'core/inplace_editable';
// Course actions is needed for actions that are not migrated to components.
import courseActions from 'core_course/actions';

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'course_format';
        // Default query selectors.
        this.selectors = {
            SECTION: `[data-for='section']`,
            SECTION_ITEM: `[data-for='section_item']`,
            SECTION_TITLE: `[data-for='section_title']`,
            SECTION_CMLIST: `[data-for='cmlist']`,
            COURSE_SECTIONLIST: `[data-for='course_sectionlist']`,
            CM: `[data-for='cmitem']`,
        };
        // Array to save dettached elements during element resorting.
        this.dettachedCms = {};
        this.dettachedSections = {};
    }

    /**
     * Static method to create a component instance form the mustahce template.
     *
     * @param {string} target the DOM main element or its ID
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
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        // Check if the course format is compatible with reactive components.
        if (!this.reactive.supportComponents) {
            return [];
        }
        return [
            // State changes that require to reload some course modules.
            {watch: `cm.visible:updated`, handler: this._reloadCm},
            // Update section number and title.
            {watch: `section.number:updated`, handler: this._refreshSectionNumber},
            // Sections and cm sorting.
            {watch: `transaction:start`, handler: this._startProcessing},
            {watch: `course.sectionlist:updated`, handler: this._refreshCourseSectionlist},
            {watch: `section.cmlist:updated`, handler: this._refreshSectionCmlist},
        ];
    }

    /**
     * Reload a course module.
     *
     * Most course module HTML is still strongly backend dependant.
     * Some changes require to get a new version af the module.
     *
     * @param {Object} update the state update data
     */
    _reloadCm({element}) {
        const cmitem = this.getElement(this.selectors.CM, element.id);
        if (cmitem) {
            courseActions.refreshModule(cmitem, element.id);
        }
    }

    /**
     * Setup the component to start a transaction.
     *
     * Some of the course actions replaces the current DOM element with a new one before updating the
     * course state. This means the component cannot preload any index properly until the transaction starts.
     *
     */
    _startProcessing() {
        // During a section or cm sorting, some elements could be dettached from the DOM and we
        // need to store somewhare in case they are needed later.
        this.dettachedCms = {};
        this.dettachedSections = {};
    }

    /**
     * Update a course section when the section number changes.
     *
     * The courseActions module used for most course section tools still depends on css classes and
     * section numbers (not id). To prevent inconsistencies when a section is moved, we need to refresh
     * the
     *
     * Course formats can override the section title rendering so the frontend depends heavily on backend
     * rendering. Luckily in edit mode we can trigger a title update using the inplace_editable module.
     *
     * @param {Object} details the update details.
     */
    _refreshSectionNumber({element}) {
        // Find the element.
        const target = this.getElement(this.selectors.SECTION, element.id);
        if (!target) {
            throw new Error(`Unkown section with ID ${element.id}`);
        }
        // Update section numbers in all data, css and YUI attributes.
        target.id = `section-${element.number}`;
        // YUI uses section number as section id in data-sectionid, in principle if a format use components
        // don't need this sectionid attribute anymore, but we keep the compatibility in case some plugin
        // use it for legacy purposes.
        target.dataset.sectionid = element.number;
        // The data-number is the attribute used by components to store the section number.
        target.dataset.number = element.number;

        // Update title and title inplace editable, if any.
        const inplace = inplaceeditable.getInplaceEditable(target.querySelector(this.selectors.SECTION_TITLE));
        if (inplace) {
            // The course content HTML can be modified at any moment, so the function need to do some checkings
            // to make sure the inplace editable still represents the same itemid.
            const currentvalue = inplace.getValue();
            const currentitemid = inplace.getItemId();
            // Unnamed sections must be recalculated.
            if (inplace.getValue() === '') {
                // The value to send can be an empty value if it is a default name.
                if (currentitemid == element.id && (currentvalue != element.rawtitle || element.rawtitle == '')) {
                    inplace.setValue(element.rawtitle);
                }
            }
        }
    }

    /**
     * Refresh a section cm list.
     *
     * @param {Object} details the update details.
     */
    _refreshSectionCmlist({element}) {
        const cmlist = element.cmlist ?? [];
        const section = this.getElement(this.selectors.SECTION, element.id);
        const listparent = section?.querySelector(this.selectors.SECTION_CMLIST);
        if (listparent) {
            this._fixOrder(listparent, cmlist, this.selectors.CM, this.dettachedCms);
        }
    }

    /**
     * Refresh the section list.
     *
     * @param {Object} details the update details.
     */
    _refreshCourseSectionlist({element}) {
        const sectionlist = element.sectionlist ?? [];
        const listparent = this.getElement(this.selectors.COURSE_SECTIONLIST);
        if (listparent) {
            this._fixOrder(listparent, sectionlist, this.selectors.SECTION, this.dettachedSections);
        }
    }

    /**
     * Fix/reorder the section or cms order.
     *
     * @param {Element} container the HTML element to reorder.
     * @param {Array} neworder an array with the ids order
     * @param {string} selector the element selector
     * @param {Object} dettachedelements a list of dettached elements
     */
    _fixOrder(container, neworder, selector, dettachedelements) {

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
            const item = this.getElement(selector, itemid) ?? dettachedelements[itemid];
            // Get the current elemnt at that position.
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
            const lastchild = container.lastChild;
            dettachedelements[lastchild?.dataset?.id ?? 0] = lastchild;
            container.removeChild(lastchild);
        }
    }
}
