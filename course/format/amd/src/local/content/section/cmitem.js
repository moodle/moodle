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
 * Course course module item component.
 *
 * This component is used to control specific course modules interactions like drag and drop.
 *
 * @module     core_courseformat/local/content/section/cmitem
 * @class      core_courseformat/local/content/section/cmitem
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DndCmItem from 'core_courseformat/local/courseeditor/dndcmitem';

export default class extends DndCmItem {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'content_section_cmitem';
        // Default query selectors.
        this.selectors = {
            BULKSELECT: `[data-for='cmBulkSelect']`,
            BULKCHECKBOX: `[data-bulkcheckbox]`,
            CARD: `[data-region='activity-card']`,
            DRAGICON: `.editing_move`,
            INPLACEEDITABLE: `[data-inplaceeditablelink]`,
        };
        // Most classes will be loaded later by DndCmItem.
        this.classes = {
            LOCKED: 'editinprogress',
            HIDE: 'd-none',
            SELECTED: 'selected',
        };
        // We need our id to watch specific events.
        this.id = this.element.dataset.id;
    }

    /**
     * Initial state ready method.
     * @param {Object} state the state data
     */
    stateReady(state) {
        this.configDragDrop(this.id);
        this.getElement(this.selectors.DRAGICON)?.classList.add(this.classes.DRAGICON);
        this._refreshBulk({state});
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `cm[${this.id}]:deleted`, handler: this.unregister},
            {watch: `cm[${this.id}]:updated`, handler: this._refreshCm},
            {watch: `bulk:updated`, handler: this._refreshBulk},
        ];
    }

    /**
     * Return the custom activity card drag shadow image.
     *
     * The element returned will be used when the user drags the card.
     *
     * @returns {HTMLElement}
     */
    setDragImage() {
        return this.getElement(this.selectors.CARD);
    }

    /**
     * Update a course index cm using the state information.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    _refreshCm({element}) {
        // Update classes.
        this.element.classList.toggle(this.classes.DRAGGING, element.dragging ?? false);
        this.element.classList.toggle(this.classes.LOCKED, element.locked ?? false);
        this.locked = element.locked;
    }

    /**
     * Update the bulk editing interface.
     *
     * @param {object} param
     * @param {Object} param.state the state data
     */
    _refreshBulk({state}) {
        const bulk = state.bulk;
        // For now, dragging elements in bulk is not possible.
        this.setDraggable(!bulk.enabled);
        // Convert the card into an active element in bulk mode.
        if (bulk.enabled) {
            this.element.dataset.action = 'toggleSelectionCm';
            this.element.dataset.preventDefault = 1;
        } else {
            this.element.removeAttribute('data-action');
            this.element.removeAttribute('data-preventDefault');
        }

        this.getElement(this.selectors.BULKSELECT)?.classList.toggle(this.classes.HIDE, !bulk.enabled);

        const disabled = !this._isCmBulkEnabled(bulk);
        const selected = this._isSelected(bulk);
        this._refreshActivityCard(bulk, selected);
        this._setCheckboxValue(selected, disabled);
    }

    /**
     * Update the activity card depending on the bulk selection.
     *
     * @param {Object} bulk the current bulk state data
     * @param {Boolean} selected if the activity is selected.
     */
    _refreshActivityCard(bulk, selected) {
        this.getElement(this.selectors.INPLACEEDITABLE)?.classList.toggle(this.classes.HIDE, bulk.enabled);
        this.getElement(this.selectors.CARD)?.classList.toggle(this.classes.SELECTED, selected);
        this.element.classList.toggle(this.classes.SELECTED, selected);
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
    _isCmBulkEnabled(bulk) {
        if (!bulk.enabled) {
            return false;
        }
        return (bulk.selectedType === '' || bulk.selectedType === 'cm');
    }

    /**
     * Check if the cm id is part of the current bulk selection.
     * @param {Object} bulk the current state bulk attribute
     * @returns {Boolean}
     */
    _isSelected(bulk) {
        if (bulk.selectedType !== 'cm') {
            return false;
        }
        return bulk.selection.includes(this.id);
    }
}
