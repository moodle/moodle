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
 * Course section header component.
 *
 * This component is used to control specific course section interactions like drag and drop.
 *
 * @module     core_courseformat/local/content/section/header
 * @class      core_courseformat/local/content/section/header
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DndSectionItem from 'core_courseformat/local/courseeditor/dndsectionitem';

export default class extends DndSectionItem {

    /**
     * Constructor hook.
     *
     * @param {Object} descriptor
     */
    create(descriptor) {
        // Optional component name for debugging.
        this.name = 'content_section_header';
        // Default query selectors.
        this.selectors = {
            ACTIONSMENU: `.section_action_menu`,
            BULKSELECT: `[data-for='sectionBulkSelect']`,
            BULKCHECKBOX: `[data-bulkcheckbox]`,
            CHEVRON: `[data-for='sectiontoggler']`,
        };
        this.classes = {
            HIDE: 'd-none',
            SELECTED: 'selected',
        };
        // Get main info from the descriptor.
        this.id = descriptor.id;
        this.section = descriptor.section;
        this.course = descriptor.course;
        this.fullregion = descriptor.fullregion;
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the initial state
     */
    stateReady(state) {
        this.configDragDrop(this.id, state, this.fullregion);
        this._refreshBulk({state});
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `bulk:updated`, handler: this._refreshBulk},
            {watch: `section[${this.id}].title:updated`, handler: this._refreshSectionTitle},
        ];
    }

    /**
     * Update the section when the section name changes.
     *
     * The section header have several HTML that uses the section name
     * for accessibility and behat tests. This method updates them all.
     *
     * @param {object} param
     * @param {Object} param.element the section info
     */
    _refreshSectionTitle(param) {
        const element = param.element;
        this.getElement(this.selectors.CHEVRON)?.setAttribute("aria-label", element.title);
        this._refreshSectionBulkSelector(param);
    }

    /**
     * Update the bulk checkbox when the section name changes.
     *
     * @param {object} param
     * @param {Object} param.element the section info
     */
    async _refreshSectionBulkSelector({element}) {
        const checkbox = this.getElement(this.selectors.BULKCHECKBOX);
        if (!checkbox) {
            return;
        }
        const newLabel = await this.reactive.getFormatString('selectsection', element.title);
        checkbox.title = newLabel;
        const label = this.getElement(`label[for='${checkbox.id}']`);
        if (label) {
            label.innerText = newLabel;
        }
    }

    /**
     * Update a bulk options.
     *
     * @param {object} param
     * @param {Object} param.state the state data
     */
    _refreshBulk({state}) {
        const bulk = state.bulk;
        if (!this._isSectionBulkEditable()) {
            return;
        }
        // For now, dragging elements in bulk is not possible.
        this.setDraggable(!bulk.enabled);
        this.getElement(this.selectors.BULKSELECT)?.classList.toggle(this.classes.HIDE, !bulk.enabled);

        const disabled = !this._isSectionBulkEnabled(bulk);
        const selected = this._isSelected(bulk);
        this.element.classList.toggle(this.classes.SELECTED, selected);
        this._setCheckboxValue(selected, disabled);
    }

    /**
     * Modify the checkbox element.
     * @param {Boolean} checked the new checked value
     * @param {Boolean} disabled the new disabled value
     */
    _setCheckboxValue(checked, disabled) {
        const checkbox = this.getElement(this.selectors.BULKCHECKBOX);
        if (!checkbox) {
            return;
        }
        checkbox.checked = checked;
        checkbox.disabled = disabled;
        // Is selectable is used to easily scan the page for bulk checkboxes.
        if (disabled) {
            checkbox.removeAttribute('data-is-selectable');
        } else {
            checkbox.dataset.isSelectable = 1;
        }
    }

    /**
     * Check if cm bulk selection is available.
     * @param {Object} bulk the current state bulk attribute
     * @returns {Boolean}
     */
    _isSectionBulkEnabled(bulk) {
        if (!bulk.enabled) {
            return false;
        }
        return (bulk.selectedType === '' || bulk.selectedType === 'section');
    }

    /**
     * Check if the section is bulk editable.
     * @return {Boolean}
     */
    _isSectionBulkEditable() {
        const section = this.reactive.get('section', this.id);
        return section?.bulkeditable ?? false;
    }

    /**
     * Check if the cm id is part of the current bulk selection.
     * @param {Object} bulk the current state bulk attribute
     * @returns {Boolean}
     */
    _isSelected(bulk) {
        if (bulk.selectedType !== 'section') {
            return false;
        }
        return bulk.selection.includes(this.id);
    }
}
