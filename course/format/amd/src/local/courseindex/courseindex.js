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
import jQuery from 'jquery';
import ContentTree from 'core_courseformat/local/courseeditor/contenttree';

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
            DRAWER: `.drawer`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            SECTIONHIDDEN: 'dimmed',
            CMHIDDEN: 'dimmed',
            SECTIONCURRENT: 'current',
            COLLAPSED: `collapsed`,
            SHOW: `show`,
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
     *
     * @param {Object} state the state data
     */
    stateReady(state) {
        // Activate section togglers.
        this.addEventListener(this.element, 'click', this._sectionTogglers);

        // Get cms and sections elements.
        const sections = this.getElements(this.selectors.SECTION);
        sections.forEach((section) => {
            this.sections[section.dataset.id] = section;
        });
        const cms = this.getElements(this.selectors.CM);
        cms.forEach((cm) => {
            this.cms[cm.dataset.id] = cm;
        });

        // Set the page item if any.
        this._refreshPageItem({element: state.course, state});

        // Configure Aria Tree.
        this.contentTree = new ContentTree(this.element, this.selectors, this.reactive.isEditing);
    }

    getWatchers() {
        return [
            {watch: `section.indexcollapsed:updated`, handler: this._refreshSectionCollapsed},
            {watch: `cm:created`, handler: this._createCm},
            {watch: `cm:deleted`, handler: this._deleteCm},
            {watch: `section:created`, handler: this._createSection},
            {watch: `section:deleted`, handler: this._deleteSection},
            {watch: `course.pageItem:created`, handler: this._refreshPageItem},
            {watch: `course.pageItem:updated`, handler: this._refreshPageItem},
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
    _sectionTogglers(event) {
        const sectionlink = event.target.closest(this.selectors.TOGGLER);
        const isChevron = event.target.closest(this.selectors.COLLAPSE);

        if (sectionlink || isChevron) {

            const section = event.target.closest(this.selectors.SECTION);
            const toggler = section.querySelector(this.selectors.COLLAPSE);
            const isCollapsed = toggler?.classList.contains(this.classes.COLLAPSED) ?? false;

            if (isChevron || isCollapsed) {
                // Update the state.
                const sectionId = section.getAttribute('data-id');
                this.reactive.dispatch(
                    'sectionIndexCollapsed',
                    [sectionId],
                    !isCollapsed
                );
            }
        }
    }

    /**
     * Update section collapsed.
     *
     * @param {object} args
     * @param {object} args.element The leement to be expanded
     */
    _refreshSectionCollapsed({element}) {
        const target = this.getElement(this.selectors.SECTION, element.id);
        if (!target) {
            throw new Error(`Unkown section with ID ${element.id}`);
        }
        // Check if it is already done.
        const toggler = target.querySelector(this.selectors.COLLAPSE);
        const isCollapsed = toggler?.classList.contains(this.classes.COLLAPSED) ?? false;

        if (element.indexcollapsed !== isCollapsed) {
            this._expandSectionNode(element);
        }
    }

    /**
     * Expand a section node.
     *
     * By default the method will use element.indexcollapsed to decide if the
     * section is opened or closed. However, using forceValue it is possible
     * to open or close a section independant from the indexcollapsed attribute.
     *
     * @param {Object} element the course module state element
     * @param {boolean} forceValue optional forced expanded value
     */
    _expandSectionNode(element, forceValue) {
        const target = this.getElement(this.selectors.SECTION, element.id);
        const toggler = target.querySelector(this.selectors.COLLAPSE);
        let collapsibleId = toggler.dataset.target ?? toggler.getAttribute("href");
        if (!collapsibleId) {
            return;
        }
        collapsibleId = collapsibleId.replace('#', '');
        const collapsible = document.getElementById(collapsibleId);
        if (!collapsible) {
            return;
        }

        if (forceValue === undefined) {
            forceValue = (element.indexcollapsed) ? false : true;
        }

        // Course index is based on Bootstrap 4 collapsibles. To collapse them we need jQuery to
        // interact with collapsibles methods. Hopefully, this will change in Bootstrap 5 because
        // it does not require jQuery anymore (when MDL-79179 is integrated).
        const togglerValue = (forceValue) ? 'show' : 'hide';
        jQuery(collapsible).collapse(togglerValue);
    }

    /**
     * Handle a page item update.
     *
     * @param {Object} details the update details
     * @param {Object} details.state the state data.
     * @param {Object} details.element the course state data.
     */
    _refreshPageItem({element, state}) {
        if (!element?.pageItem?.isStatic || element.pageItem.type != 'cm') {
            return;
        }
        // Check if we need to uncollapse the section and scroll to the element.
        const section = state.section.get(element.pageItem.sectionId);
        if (section.indexcollapsed) {
            this._expandSectionNode(section, true);
            setTimeout(
                () => this.cms[element.pageItem.id]?.scrollIntoView({block: "nearest"}),
                250
            );
        }
    }

    /**
     * Create a newcm instance.
     *
     * @param {object} param
     * @param {Object} param.state
     * @param {Object} param.element
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
     * Create a new section instance.
     *
     * @param {Object} details the update details.
     * @param {Object} details.state the state data.
     * @param {Object} details.element the element data.
     */
    async _createSection({state, element}) {
        // Create a fake node while the component is loading.
        const fakeelement = document.createElement('div');
        fakeelement.classList.add('bg-pulse-grey', 'w-100');
        fakeelement.innerHTML = '&nbsp;';
        this.sections[element.id] = fakeelement;
        // Place the fake node on the correct position.
        this._refreshCourseSectionlist({
            state,
            element: state.course,
        });
        // Collect render data.
        const exporter = this.reactive.getExporter();
        const data = exporter.section(state, element);
        // Create the new content.
        const newcomponent = await this.renderComponent(fakeelement, 'core_courseformat/local/courseindex/section', data);
        // Replace the fake node with the real content.
        const newelement = newcomponent.getElement();
        this.sections[element.id] = newelement;
        fakeelement.parentNode.replaceChild(newelement, fakeelement);
    }

    /**
     * Refresh a section cm list.
     *
     * @param {object} param
     * @param {Object} param.element
     */
    _refreshSectionCmlist({element}) {
        const cmlist = element.cmlist ?? [];
        const listparent = this.getElement(this.selectors.SECTION_CMLIST, element.id);
        this._fixOrder(listparent, cmlist, this.cms);
    }

    /**
     * Refresh the section list.
     *
     * @param {object} param
     * @param {Object} param.element
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
     * @param {object} param
     * @param {Object} param.element
     */
    _deleteCm({element}) {
        delete this.cms[element.id];
    }

    /**
     * Remove a section from the list.
     *
     * The actual DOM element removal is delegated to the section component.
     *
     * @param {Object} details the update details.
     * @param {Object} details.element the element data.
     */
    _deleteSection({element}) {
        delete this.sections[element.id];
    }
}
