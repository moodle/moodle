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

import ContentComponent from 'core_courseformat/local/content';
import {getCourseEditor} from 'core_courseformat/courseeditor';

const selectors = {
    block: '.block_site_main_menu',
};

/**
 * Main section component for the site main menu block.
 *
 * @module     block_site_main_menu/mainsection
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class extends ContentComponent {
    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {string} query the DOM main element query selector.
     * @return {ContentComponent}
     */
    static init(query) {
        let element = document.querySelector(query);
        const courseId = element.getAttribute('data-courseid');
        // We need to include the full block because some controls are located in the block footer.
        element = element.closest(selectors.block);
        return new ContentComponent({
            element,
            reactive: getCourseEditor(courseId),
        });
    }
}
