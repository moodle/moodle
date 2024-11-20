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
      COLLAPSEALL: `[data-action="collapseallcourseindexsections"]`,
      EXPANDALL: `[data-action="expandallcourseindexsections"]`,
    };
  }

  /**
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
    // Attach the on-click event handlers to the expand-all and collapse-all buttons, if present.
    const expandAllBtn = this.getElement(this.selectors.EXPANDALL);
    if (expandAllBtn) {
      this.addEventListener(expandAllBtn, 'click', this._expandAllSections);

    }
    const collapseAllBtn = this.getElement(this.selectors.COLLAPSEALL);
    if (collapseAllBtn) {
      this.addEventListener(collapseAllBtn, 'click', this._collapseAllSections);
    }
  }

  /**
   * On-click event handler for the collapse-all button.
   * @private
   */
  _collapseAllSections() {
    this._toggleAllSections(true);
  }

  /**
   * On-click event handler for the expand-all button.
   * @private
   */
  _expandAllSections() {
    this._toggleAllSections(false);
  }

  /**
   * Collapses or expands all sections in the course index.
   * @param {boolean} expandOrCollapse set to TRUE to collapse all, and FALSE to expand all.
   * @private
   */
  _toggleAllSections(expandOrCollapse) {
    this.reactive.dispatch('allSectionsIndexCollapsed', expandOrCollapse);
  }
}
