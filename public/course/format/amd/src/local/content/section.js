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
import Pending from "core/pending";

export default class extends DndSection {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'content_section';
        // Default query selectors.
        this.selectors = {
            ACTIONMENU: '.section-actions',
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
                const headerComponent = this._newHeader(sectionItem);
                this.configDragDrop(headerComponent);
            }
        }
        this._openSectionIfNecessary();
    }

    /**
     * Create a new Header object.
     *
     * @param {Element} sectionItem the Header's element
     * @return {Header} the new object
     */
    _newHeader(sectionItem) {
        return new Header({
            ...this,
            element: sectionItem,
            fullregion: this.element,
        });
    }

    /**
     * Open the section if the anchored activity is inside.
     */
    async _openSectionIfNecessary() {
        const pageCmInfo = this.reactive.getPageAnchorCmInfo();
        if (!pageCmInfo || pageCmInfo.sectionid !== this.id) {
            return;
        }
        await this.reactive.dispatch('sectionContentCollapsed', [this.id], false);
        const pendingOpen = new Pending(`courseformat/section:openSectionIfNecessary`);
        setTimeout(() => {
            document.querySelector("#" + pageCmInfo.anchor).scrollIntoView();
            this.reactive.dispatch('setPageItem', 'cm', pageCmInfo.id);
            pendingOpen.resolve();
        }, 250);
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
        if (dropdata?.type === 'section' && (this.reactive?.sectionReturn ?? this.reactive?.pageSectionId) !== null) {
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
        const lastCm = cms[cms.length - 1];
        // If it is a delegated section return the last item overall.
        if (this.section.component !== null) {
            return lastCm;
        }
        // If it is a regular section and the last item overall has a parent cm, return the parent instead.
        const parentSection = lastCm.parentNode.closest(this.selectors.CM);
        return parentSection ?? lastCm;
    }

    /**
     * Get a fallback element when there is no CM in the section.
     *
     * @returns {element|null} the las course module element of the section.
     */
    getLastCmFallback() {
        // The sectioninfo is always present, even when the section is empty.
        return this.getElement(this.selectors.SECTIONINFO);
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
        const affectedAction = this._getActionMenu(selector);
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
            affectedAction.dataset.swapicon = affectedAction.dataset.icon;
            affectedAction.dataset.icon = newIcon;
            if (newIcon) {
                const pixHtml = await Templates.renderPix(newIcon, 'core');
                Templates.replaceNode(icon, pixHtml, '');
            }
        }
    }

    /**
     * Get the action menu element from the selector.
     *
     * @param {string} selector The selector to find the action menu.
     * @returns The action menu element.
     */
    _getActionMenu(selector) {
        return document.querySelector(`${this.selectors.ACTIONMENU}[data-sectionid='${this.id}'] ${selector}`);
    }
}
