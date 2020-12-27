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
 * Module to navigation between users in a course.
 *
 * @package    report_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * UserCourseNavigation
     *
     * @param {String} userSelector The selector of the user element.
     * @param {String} moduleSelector The selector of the module element.
     * @param {String} baseUrl The base url for the page (no params).
     * @param {Number} userId The course id
     * @param {Number} courseId The user id
     * @param {Number} moduleId The activity module (filter)
     */
    var UserCourseNavigation = function(userSelector, moduleSelector, baseUrl, userId, courseId, moduleId) {
        this._baseUrl = baseUrl;
        this._userId = userId + '';
        this._courseId = courseId;
        this._moduleId = moduleId;

        $(userSelector).on('change', this._userChanged.bind(this));
        $(moduleSelector).on('change', this._moduleChanged.bind(this));
    };

    /**
     * The user was changed in the select list.
     *
     * @method _userChanged
     * @param {Event} e the event
     */
    UserCourseNavigation.prototype._userChanged = function(e) {
        // Note: This change causes a page reload and is intentionally not paired with a js_complete call.
        M.util.js_pending('report_competency/user_course_navigation:_userChanged');
        var newUserId = $(e.target).val();
        var queryStr = '?user=' + newUserId + '&id=' + this._courseId + '&mod=' + this._moduleId;
        document.location = this._baseUrl + queryStr;
    };

    /**
     * The module was changed in the select list.
     *
     * @method _moduleChanged
     * @param {Event} e the event
     */
    UserCourseNavigation.prototype._moduleChanged = function(e) {
        // Note: This change causes a page reload and is intentionally not paired with a js_complete call.
        M.util.js_pending('report_competency/user_course_navigation:_moduleChanged');
        var newModuleId = $(e.target).val();
        var queryStr = '?mod=' + newModuleId + '&id=' + this._courseId + '&user=' + this._userId;
        document.location = this._baseUrl + queryStr;
    };

    /** @type {Number} The id of the user. */
    UserCourseNavigation.prototype._userId = null;
    /** @type {Number} The id of the module. */
    UserCourseNavigation.prototype._moduleId = null;
    /** @type {Number} The id of the course. */
    UserCourseNavigation.prototype._courseId = null;
    /** @type {String} Plugin base url. */
    UserCourseNavigation.prototype._baseUrl = null;

    return /** @alias module:report_competency/user_course_navigation */ UserCourseNavigation;

});
