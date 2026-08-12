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
 * JS module for the course learning outcomes page.
 *
 * @module    core_course/learningoutcomes
 * @copyright 2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Collapse from 'core_courseformat/local/collapse';

/**
 * Initialise collapse/expand behaviour for learning outcomes sections.
 */
export const init = () => {
    Collapse.init(document.querySelector('[data-for="course_sectionlist"]'), {
        toggleAllSelector: '#collapsesections',
        collapseSelector: '[id^="coursecontentcollapseid"]',
    });
};
