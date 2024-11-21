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
 * Course selector.
 *
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';
import {asyncRender} from 'block_xp/compat';
import CourseResourceSelector from 'block_xp/course-resource-selector';
import $ from 'jquery';

export const openCourseSelector = async(onSelected) => {
    const {html} = await asyncRender('block_xp/modal-course-selector', {});
    const modal = new Modal(html);
    modal.setRemoveOnClose(true);

    const rootJq = modal.getRoot();
    const root = rootJq[0];

    rootJq.on(ModalEvents.shown, () => {
        const container = root.querySelector('.search-result-contents');
        const termField = root.querySelector('.search-term-course');
        const cs = new CourseResourceSelector($(container), $(termField));
        cs.onResourceSelected(function(e, resource) {
            onSelected(resource.course);
            modal.hide();
        });
    });

    modal.show();
};