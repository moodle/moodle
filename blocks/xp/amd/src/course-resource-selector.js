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
 * Course resource selector.
 *
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'block_xp/resource-selector'], function($, Ajax, ResourceSelector) {
    /**
     * Course resource selector.
     *
     * @param {String|jQuery} container The container of the contents.
     * @param {jQuery} searchTermFieldNode The input field in which the use searches.
     */
    function CourseResourceSelector(container, searchTermFieldNode) {
        ResourceSelector.prototype.constructor.apply(this, [container, this.searchFunction.bind(this), searchTermFieldNode]);
    }

    CourseResourceSelector.prototype = Object.create(ResourceSelector.prototype);
    CourseResourceSelector.prototype.constructor = CourseResourceSelector;

    CourseResourceSelector.prototype.searchFunction = function(term) {
        var calls = [
            {
                methodname: 'block_xp_search_courses',
                args: { query: term }
            }
        ];

        return Ajax.call(calls)[0].then(function(results) {
            return results.map(function(c) {
                return {
                    _iscourse: true,
                    name: c.fullname,
                    subname: c.shortname,
                    course: c
                };
            });
        });
    };

    return CourseResourceSelector;
});
