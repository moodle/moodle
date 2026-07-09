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
 * Controls for the course index drawer, such as expand-all/collapse-all sections.
 *
 * @module     theme_boost/courseindexdrawercontrols
 * @copyright  2023 Stefan Topfstedt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';

export default class Component extends BaseComponent {

    create() {
        this.name = 'courseindexdrawercontrols';
        this.selectors = {
            TOGGLE: `[data-action="toggleallcourseindexsections"]`,
        };
        this.classes = {
            COLLAPSED: `collapsed`,
        };
    }

    /**
     * @param {HTMLElement|string} target the DOM main element or its ID
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
     * @return {Array}
     */
    getWatchers() {
        return [
            {watch: `section.indexcollapsed:updated`, handler: this._refreshToggle},
            {watch: `section:created`, handler: this._refreshToggle},
            {watch: `section:deleted`, handler: this._refreshToggle},
        ];
    }

    /**
     * @param {Object} state
     */
    stateReady(state) {
        const btn = this.getElement(this.selectors.TOGGLE);
        if (btn) {
            const courseindex = document.getElementById('courseindex');
            if (courseindex) {
                btn.setAttribute('aria-controls', courseindex.id);
            }
            this.addEventListener(btn, 'click', this._toggleAllSections);
        }
        this._refreshToggle({state});
    }

    /**
     * On-click handler. Collapses all when currently expanded, otherwise expands.
     *
     * @param {Event} event
     * @private
     */
    _toggleAllSections(event) {
        event.preventDefault();
        const btn = this.getElement(this.selectors.TOGGLE);
        const isAllCollapsed = btn.classList.contains(this.classes.COLLAPSED);
        this.reactive.dispatch('allSectionsIndexCollapsed', !isAllCollapsed);
    }

    /**
     * Syncs the toggle to the current section state: adds the "collapsed" class
     * (and updates aria-expanded) only when every section is collapsed.
     *
     * @param {Object} param
     * @param {Object} param.state
     * @private
     */
    _refreshToggle({state}) {
        const btn = this.getElement(this.selectors.TOGGLE);
        if (!btn) {
            return;
        }
        const sections = [...state.section.values()].filter((section) => !section.component);
        const allCollapsed = sections.length > 0 && sections.every(s => s.indexcollapsed);
        btn.classList.toggle(this.classes.COLLAPSED, allCollapsed);
        btn.setAttribute('aria-expanded', String(!allCollapsed));
    }
}
