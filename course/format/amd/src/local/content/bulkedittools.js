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
import {getString} from 'core/str';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {
    selectAllBulk,
    switchBulkSelection,
    checkAllBulkSelected
} from 'core_courseformat/local/content/actions/bulkselection';
import Notification from 'core/notification';

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
            this.addEventListener(selectAll, 'click', this._selectAllClick);
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
        this._updatePageTitle(element.enabled).catch(Notification.exception);

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
        const stringName = (bulk.selection.length > 1) ? 'bulkselection_plural' : 'bulkselection';
        const selectedCount = await getString(stringName, 'core_courseformat', bulk.selection.length);
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
        selectall.disabled = (bulk.selectedType === '');
        // The changechecker module can prevent the checkbox form changing it's value.
        // To avoid that we leave the sniffer to act before changing the value.
        const pending = new Pending(`courseformat/bulktools:refreshSelectAll`);
        setTimeout(
            () => {
                selectall.checked = checkAllBulkSelected(this.reactive);
                pending.resolve();
            },
            100
        );
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
            action.tabIndex = (enabled) ? 0 : -1;

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
     * Handle special select all cases.
     * @param {Event} event
     */
    _selectAllClick(event) {
        event.preventDefault();
        if (event.altKey) {
            switchBulkSelection(this.reactive);
            return;
        }
        if (checkAllBulkSelected(this.reactive)) {
            this._handleUnselectAll();
            return;
        }
        selectAllBulk(this.reactive, true);
    }

    /**
     * Process unselect all elements.
     */
    _handleUnselectAll() {
        const pending = new Pending(`courseformat/content:bulktUnselectAll`);
        selectAllBulk(this.reactive, false);
        // Wait for a while and focus on the first checkbox.
        setTimeout(() => {
            document.querySelector(this.selectors.SELECTABLE)?.focus();
            pending.resolve();
        }, 150);
    }

    /**
     * Updates the <title> attribute of the page whenever bulk editing is toggled.
     *
     * This helps users, especially screen reader users, to understand the current state of the course homepage.
     *
     * @param {Boolean} enabled True when bulk editing is turned on. False, otherwise.
     * @returns {Promise<void>}
     * @private
     */
    async _updatePageTitle(enabled) {
        const enableBulk = document.querySelector(this.selectors.BULKBTN);
        let params, bulkEditTitle, editingTitle;
        if (enableBulk.dataset.sectiontitle) {
            // Section editing mode.
            params = {
                course: enableBulk.dataset.coursename,
                sectionname: enableBulk.dataset.sectionname,
                sectiontitle: enableBulk.dataset.sectiontitle,
            };
            bulkEditTitle = await getString('coursesectiontitlebulkediting', 'moodle', params);
            editingTitle = await getString('coursesectiontitleediting', 'moodle', params);
        } else {
            // Whole course editing mode.
            params = {
                course: enableBulk.dataset.coursename
            };
            bulkEditTitle = await getString('coursetitlebulkediting', 'moodle', params);
            editingTitle = await getString('coursetitleediting', 'moodle', params);
        }
        const pageTitle = document.title;
        if (enabled) {
            // Use bulk editing string for the page title.
            // At this point, the current page title should be the normal editing title.
            // So replace the normal editing title with the bulk editing title.
            document.title = pageTitle.replace(editingTitle, bulkEditTitle);
        } else {
            // Use the normal editing string for the page title.
            // At this point, the current page title should be the bulk editing title.
            // So replace the bulk editing title with the normal editing title.
            document.title = pageTitle.replace(bulkEditTitle, editingTitle);
        }
    }
}
