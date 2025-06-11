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
 * Allow the user to search for grades within the singleview report.
 *
 * @module    gradereport_singleview/grade
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import GradeItemSearch from 'core_grades/comboboxsearch/grade';

// Define our standard lookups.
const selectors = {
    component: '.grade-search',
    courseid: '[data-region="courseid"]',
};
const component = document.querySelector(selectors.component);

export default class GradeItems extends GradeItemSearch {

    courseID = component.querySelector(selectors.courseid).dataset.courseid;

    /**
     * Construct the class.
     *
     * @param {string} baseUrl The base URL for the page.
     */
    constructor(baseUrl) {
        super();
        this.baseUrl = baseUrl;
    }

    static init(baseUrl) {
        return new GradeItems(baseUrl);
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} gradeID The ID of the grade item selected.
     * @returns {string|*}
     */
    selectOneLink(gradeID) {
        const url = new URL(this.baseUrl);
        url.searchParams.set('gradesearchvalue', this.getSearchTerm());
        url.searchParams.set('item', 'grade');
        url.searchParams.set('itemid', gradeID);
        return url.toString();
    }
}
