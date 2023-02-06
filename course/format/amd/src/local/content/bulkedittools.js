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
 * The bulk editor tools bar.
 *
 * @module     core_courseformat/local/content/bulkedittools
 * @class      core_courseformat/local/content/bulkedittools
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {disableStickyFooter, enableStickyFooter} from 'core/sticky-footer';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import {get_string as getString} from 'core/str';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';

// Load global strings.
prefetchStrings(
    'core_courseformat',
    ['bulkselection']
);

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'bulk_editor_tools';
        // Default query selectors.
        this.selectors = {
            ACTIONS: `[data-for="bulkaction"]`,
            ACTIONTOOL: `[data-for="bulkactions"] li`,
            CANCEL: `[data-for="bulkcancel"]`,
            COUNT: `[data-for='bulkcount']`,
            SELECTABLE: `[data-bulkcheckbox][data-is-selectable]`,
            SELECTALL: `[data-for="selectall"]`,
            BULKBTN: `[data-for="enableBulk"]`,
        };
        // Most classes will be loaded later by DndCmItem.
        this.classes = {
            HIDE: 'd-none',
            DISABLED: 'disabled',
        };
    }

    /**
     * Static method to create a component instance from the mustache template.
     *
     * @param {string} target optional altentative DOM main element CSS selector
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new this({
            element: document.querySelector(target),
            reactive: getCurrentCourseEditor(),
            selectors
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        const cancelBtn = this.getElement(this.selectors.CANCEL);
        if (cancelBtn) {
            this.addEventListener(cancelBtn, 'click', this._cancelBulk);
        }
        const selectAll = this.getElement(this.selectors.SELECTALL);
        if (selectAll) {
            this.addEventListener(selectAll, 'change', this._selectAllClick);
        }
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `bulk.enabled:updated`, handler: this._refreshEnabled},
            {watch: `bulk:updated`, handler: this._refreshTools},
        ];
    }

    /**
     * Hide and show the bulk edit tools.
     *
     * @param {object} param
     * @param {Object} param.element details the update details (state.bulk in this case).
     */
    _refreshEnabled({element}) {
        if (element.enabled) {
            enableStickyFooter();
        } else {
            disableStickyFooter();
        }
    }

    /**
     * Refresh the tools depending on the current selection.
     *
     * @param {object} param the state watcher information
     * @param {Object} param.state the full state data.
     * @param {Object} param.element the affected element (bulk in this case).
     */
    _refreshTools(param) {
        this._refreshSelectCount(param);
        this._refreshSelectAll(param);
        this._refreshActions(param);
    }

    /**
     * Refresh the selection count.
     *
     * @param {object} param
     * @param {Object} param.element the affected element (bulk in this case).
     */
    async _refreshSelectCount({element: bulk}) {
        const selectedCount = await getString('bulkselection', 'core_courseformat', bulk.selection.length);
        const selectedElement = this.getElement(this.selectors.COUNT);
        if (selectedElement) {
            selectedElement.innerHTML = selectedCount;
        }
    }

    /**
     * Refresh the select all element.
     *
     * @param {object} param
     * @param {Object} param.element the affected element (bulk in this case).
     */
    _refreshSelectAll({element: bulk}) {
        const selectall = this.getElement(this.selectors.SELECTALL);
        if (!selectall) {
            return;
        }
        if (bulk.selectedType === '') {
            selectall.checked = false;
            selectall.disabled = true;
            return;
        }

        selectall.disabled = false;
        const maxSelection = document.querySelectorAll(this.selectors.SELECTABLE).length;
        selectall.checked = (bulk.selection.length == maxSelection);
    }

    /**
     * Refresh the visible action buttons depending on the selection type.
     *
     * @param {object} param
     * @param {Object} param.element the affected element (bulk in this case).
     */
    _refreshActions({element: bulk}) {
        // By default, we show the cm options.
        const displayType = (bulk.selectedType == 'section') ? 'section' : 'cm';
        const enabled = (bulk.selectedType !== '');
        this.getElements(this.selectors.ACTIONS).forEach(action => {
            action.classList.toggle(this.classes.DISABLED, !enabled);

            const actionTool = action.closest(this.selectors.ACTIONTOOL);
            const isHidden = (action.dataset.bulk != displayType);
            actionTool?.classList.toggle(this.classes.HIDE, isHidden);
        });
    }

    /**
     * Cancel bulk handler.
     */
    _cancelBulk() {
        const pending = new Pending(`courseformat/content:bulktoggle_off`);
        this.reactive.dispatch('bulkEnable', false);
        // Wait for a while and focus on enable bulk button.
        setTimeout(() => {
            document.querySelector(this.selectors.BULKBTN)?.focus();
            pending.resolve();
        }, 150);
    }

    /**
     * Select all elements click handler.
     * @param {Event} event
     */
    _selectAllClick(event) {
        const target = event.target;
        const bulk = this.reactive.get('bulk');
        if (bulk.selectedType === '') {
            return;
        }
        if (!target.checked) {
            this._handleUnselectAll();
            return;
        }
        this._handleSelectAll(bulk);
    }

    /**
     * Process unselect all elements.
     */
    _handleUnselectAll() {
        const pending = new Pending(`courseformat/content:bulktUnselectAll`);
        // Re-enable bulk will clean the selection and the selection type.
        this.reactive.dispatch('bulkEnable', true);
        // Wait for a while and focus on the first checkbox.
        setTimeout(() => {
            document.querySelector(this.selectors.SELECTABLE)?.focus();
            pending.resolve();
        }, 150);
    }

    /**
     * Process a select all selectable elements.
     * @param {Object} bulk the state bulk data
     * @param {String} bulk.selectedType the current selected type (section/cm)
     */
    _handleSelectAll(bulk) {
        const selectableIds = [];
        const selectables = document.querySelectorAll(this.selectors.SELECTABLE);
        if (selectables.length == 0) {
            return;
        }
        selectables.forEach(selectable => {
            selectableIds.push(selectable.dataset.id);
        });
        const mutation = (bulk.selectedType === 'cm') ? 'cmSelect' : 'sectionSelect';
        this.reactive.dispatch(mutation, selectableIds);
    }
}
