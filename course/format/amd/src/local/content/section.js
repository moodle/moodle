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
 * Course section format component.
 *
 * @module     core_courseformat/local/content/section
 * @class      core_courseformat/local/content/section
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Header from 'core_courseformat/local/content/section/header';
import DndSection from 'core_courseformat/local/courseeditor/dndsection';
import Templates from 'core/templates';

export default class extends DndSection {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'content_section';
        // Default query selectors.
        this.selectors = {
            SECTION_ITEM: `[data-for='section_title']`,
            CM: `[data-for="cmitem"]`,
            SECTIONINFO: `[data-for="sectioninfo"]`,
            SECTIONBADGES: `[data-region="sectionbadges"]`,
            SHOWSECTION: `[data-action="sectionShow"]`,
            HIDESECTION: `[data-action="sectionHide"]`,
            ACTIONTEXT: `.menu-action-text`,
            ICON: `.icon`,
        };
        // Most classes will be loaded later by DndCmItem.
        this.classes = {
            LOCKED: 'editinprogress',
            HASDESCRIPTION: 'description',
            HIDE: 'd-none',
            HIDDEN: 'hidden',
            CURRENT: 'current',
        };

        // We need our id to watch specific events.
        this.id = this.element.dataset.id;
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the initial state
     */
    stateReady(state) {
        this.configState(state);
        // Drag and drop is only available for components compatible course formats.
        if (this.reactive.isEditing && this.reactive.supportComponents) {
            // Section zero and other formats sections may not have a title to drag.
            const sectionItem = this.getElement(this.selectors.SECTION_ITEM);
            if (sectionItem) {
                // Init the inner dragable element.
                const headerComponent = new Header({
                    ...this,
                    element: sectionItem,
                    fullregion: this.element,
                });
                this.configDragDrop(headerComponent);
            }
        }
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `section[${this.id}]:updated`, handler: this._refreshSection},
        ];
    }

    /**
     * Validate if the drop data can be dropped over the component.
     *
     * @param {Object} dropdata the exported drop data.
     * @returns {boolean}
     */
    validateDropData(dropdata) {
        // If the format uses one section per page sections dropping in the content is ignored.
       if (dropdata?.type === 'section' && this.reactive.sectionReturn != 0) {
            return false;
        }
        return super.validateDropData(dropdata);
    }

    /**
     * Get the last CM element of that section.
     *
     * @returns {element|null}
     */
    getLastCm() {
        const cms = this.getElements(this.selectors.CM);
        // DndUpload may add extra elements so :last-child selector cannot be used.
        if (!cms || cms.length === 0) {
            return null;
        }
        return cms[cms.length - 1];
    }

    /**
     * Update a content section using the state information.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSection({element}) {
        // Update classes.
        this.element.classList.toggle(this.classes.DRAGGING, element.dragging ?? false);
        this.element.classList.toggle(this.classes.LOCKED, element.locked ?? false);
        this.element.classList.toggle(this.classes.HIDDEN, !element.visible ?? false);
        this.element.classList.toggle(this.classes.CURRENT, element.current ?? false);
        this.locked = element.locked;
        // The description box classes depends on the section state.
        const sectioninfo = this.getElement(this.selectors.SECTIONINFO);
        if (sectioninfo) {
            sectioninfo.classList.toggle(this.classes.HASDESCRIPTION, element.hasrestrictions);
        }
        // Update section badges and menus.
        this._updateBadges(element);
        this._updateActionsMenu(element);
    }

    /**
     * Update a section badges using the state information.
     *
     * @param {object} section the section state.
     */
    _updateBadges(section) {
        const current = this.getElement(`${this.selectors.SECTIONBADGES} [data-type='iscurrent']`);
        current?.classList.toggle(this.classes.HIDE, !section.current);

        const hiddenFromStudents = this.getElement(`${this.selectors.SECTIONBADGES} [data-type='hiddenfromstudents']`);
        hiddenFromStudents?.classList.toggle(this.classes.HIDE, section.visible);
    }

    /**
     * Update a section action menus.
     *
     * @param {object} section the section state.
     */
    async _updateActionsMenu(section) {
        let selector;
        let newAction;
        if (section.visible) {
            selector = this.selectors.SHOWSECTION;
            newAction = 'sectionHide';
        } else {
            selector = this.selectors.HIDESECTION;
            newAction = 'sectionShow';
        }
        // Find the affected action.
        const affectedAction = this.getElement(selector);
        if (!affectedAction) {
            return;
        }
        // Change action.
        affectedAction.dataset.action = newAction;
        // Change text.
        const actionText = affectedAction.querySelector(this.selectors.ACTIONTEXT);
        if (affectedAction.dataset?.swapname && actionText) {
            const oldText = actionText?.innerText;
            actionText.innerText = affectedAction.dataset.swapname;
            affectedAction.dataset.swapname = oldText;
        }
        // Change icon.
        const icon = affectedAction.querySelector(this.selectors.ICON);
        if (affectedAction.dataset?.swapicon && icon) {
            const newIcon = affectedAction.dataset.swapicon;
            if (newIcon) {
                const pixHtml = await Templates.renderPix(newIcon, 'core');
                Templates.replaceNode(icon, pixHtml, '');
            }
        }
    }
}
