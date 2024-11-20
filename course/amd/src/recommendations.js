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
 * A javascript module to handle toggling activity chooser recommendations.
 *
 * @module     core_course/recommendations
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Do an ajax call to toggle the recommendation
 *
 * @param  {object} e The event
 * @return {void}
 */
const toggleRecommendation = (e) => {
    let data = {
        methodname: 'core_course_toggle_activity_recommendation',
        args: {
            area: e.currentTarget.dataset.area,
            id: e.currentTarget.dataset.id
        }
    };
    Ajax.call([data])[0].fail(Notification.exception);
};

/**
 * Initialisation function
 *
 * @return {void}
 */
export const init = () => {
    const checkboxelements = document.querySelectorAll("[data-area]");
    checkboxelements.forEach((checkbox) => {
        checkbox.addEventListener('change', toggleRecommendation);
    });
};
