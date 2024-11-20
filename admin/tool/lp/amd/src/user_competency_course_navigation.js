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
 * Module to enable inline editing of a comptency grade.
 *
 * @module     tool_lp/user_competency_course_navigation
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * UserCompetencyCourseNavigation
     *
     * @class tool_lp/user_competency_course_navigation
     * @param {String} userSelector The selector of the user element.
     * @param {String} competencySelector The selector of the competency element.
     * @param {String} baseUrl The base url for the page (no params).
     * @param {Number} userId The user id
     * @param {Number} competencyId The competency id
     * @param {Number} courseId The course id
     */
    var UserCompetencyCourseNavigation = function(userSelector, competencySelector, baseUrl, userId, competencyId, courseId) {
        this._baseUrl = baseUrl;
        this._userId = userId + '';
        this._competencyId = competencyId + '';
        this._courseId = courseId;

        $(userSelector).on('change', this._userChanged.bind(this));
        $(competencySelector).on('change', this._competencyChanged.bind(this));
    };

    /**
     * The user was changed in the select list.
     *
     * @method _userChanged
     * @param {Event} e
     */
    UserCompetencyCourseNavigation.prototype._userChanged = function(e) {
        var newUserId = $(e.target).val();
        var queryStr = '?userid=' + newUserId + '&courseid=' + this._courseId + '&competencyid=' + this._competencyId;
        document.location = this._baseUrl + queryStr;
    };

    /**
     * The competency was changed in the select list.
     *
     * @method _competencyChanged
     * @param {Event} e
     */
    UserCompetencyCourseNavigation.prototype._competencyChanged = function(e) {
        var newCompetencyId = $(e.target).val();
        var queryStr = '?userid=' + this._userId + '&courseid=' + this._courseId + '&competencyid=' + newCompetencyId;
        document.location = this._baseUrl + queryStr;
    };

    /** @property {Number} The id of the competency. */
    UserCompetencyCourseNavigation.prototype._competencyId = null;
    /** @property {Number} The id of the user. */
    UserCompetencyCourseNavigation.prototype._userId = null;
    /** @property {Number} The id of the course. */
    UserCompetencyCourseNavigation.prototype._courseId = null;
    /** @property {String} Plugin base url. */
    UserCompetencyCourseNavigation.prototype._baseUrl = null;
    /** @property {Boolean} Ignore the first change event for competencies. */
    UserCompetencyCourseNavigation.prototype._ignoreFirstCompetency = null;

    return UserCompetencyCourseNavigation;
});
