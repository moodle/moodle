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
 * Course resource module selector.
 *
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'block_xp/throttler', 'block_xp/resource-selector'], function($, Ajax, Throttler, ResourceSelector) {
    /**
     * Course module resource selector.
     *
     * @param {String|jQuery} container The container of the contents.
     * @param {jQuery} searchTermFieldNode The input field in which the use searches.
     */
    function CmResourceSelector(container, searchTermFieldNode) {
        ResourceSelector.prototype.constructor.apply(this, [container, this.filterFunction.bind(this), searchTermFieldNode]);
        this.resources = [];
        this.courseId = null;
        this.throttler = new Throttler(100);
        this.setMinChars(0);
    }

    CmResourceSelector.prototype = Object.create(ResourceSelector.prototype);
    CmResourceSelector.prototype.constructor = CmResourceSelector;

    CmResourceSelector.prototype.initForCourse = function(courseId) {
        if (this.courseId == courseId) {
            // The course has not changed, display the contents right away.
            this.displayResults(this.resources);
            return;
        }

        this.displaySearching();
        this.courseId = courseId;
        this.fetchAllForCourse(courseId)
            .then(
                function(resources) {
                    if (this.courseId != courseId) {
                        // We switched course in the meantime, ignore.
                        return;
                    }
                    this.resources = resources;
                    this.displayResults(this.resources);
                }.bind(this)
            )
            .fail(function() {
                this.resources = [];
                this.displayEmptyResults();
            }.bind(this));
    };

    CmResourceSelector.prototype.fetchAllForCourse = function(courseId) {
        var searchargs = {
            courseid: courseId,
            query: '*'
        };

        var calls = [
            {
                methodname: 'block_xp_search_modules',
                args: searchargs
            }
        ];

        return Ajax.call(calls)[0].then(function(results) {
            return results.reduce(function(carry, section) {
                return carry.concat(
                    section.modules.map(function(cm) {
                        return {
                            _iscm: true,
                            subname: section.name,
                            name: cm.name,
                            cm: cm
                        };
                    })
                );
            }, []);
        });
    };

    CmResourceSelector.prototype.filterFunction = function(term) {
        term = (term || '').toLowerCase();
        if (!term) {
            return this.resources;
        }
        return this.resources.filter(function(cm) {
            return cm.name.toLowerCase().indexOf(term) > -1;
        });
    };

    return CmResourceSelector;
});
