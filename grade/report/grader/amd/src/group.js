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
 * Allow the user to search for groups within the grader report.
 *
 * @module    gradereport_grader/group
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.5 - please use core_course/actionbar/group instead.
 * @todo       Final deprecation in Moodle 6.0. See MDL-82116.
 */
import GroupSearch from 'core_group/comboboxsearch/group';
import Url from 'core/url';

export default class Group extends GroupSearch {

    courseID;

    constructor() {
        window.console.warn(
            'The gradereport_grader/group module has been deprecated since Moodle 4.5.' +
            ' Please use core_course/actionbar/group instead.',
        );

        super();

        // Define our standard lookups.
        this.selectors = {...this.selectors,
            courseid: '[data-region="courseid"]',
        };
        const component = document.querySelector(this.componentSelector());
        this.courseID = component.querySelector(this.selectors.courseid).dataset.courseid;
    }

    static init() {
        return new Group();
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} groupID The ID of the group selected.
     * @returns {string|*}
     */
    selectOneLink(groupID) {
        return Url.relativeUrl('/grade/report/grader/index.php', {
            id: this.courseID,
            groupsearchvalue: this.getSearchTerm(),
            group: groupID,
        }, false);
    }
}
