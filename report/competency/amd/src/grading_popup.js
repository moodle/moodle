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
 * @package    report_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/str', 'core/ajax', 'core/log', 'core/templates', 'tool_lp/dialogue'],
       function($, notification, str, ajax, log, templates, Dialogue) {

    /**
     * GradingPopup
     *
     * @param {String} regionSelector The regionSelector
     * @param {String} userCompetencySelector The userCompetencySelector
     */
    var GradingPopup = function(regionSelector, userCompetencySelector) {
        this._regionSelector = regionSelector;
        this._userCompetencySelector = userCompetencySelector;

        $(this._regionSelector).on('click', this._userCompetencySelector, this._handleClick.bind(this));
    };

    /**
     * Get the data from the clicked cell and open the popup.
     *
     * @method _handleClick
     * @param {Event} e The event
     */
    GradingPopup.prototype._handleClick = function(e) {
        var cell = $(e.target).closest(this._userCompetencySelector);
        var competencyId = $(cell).data('competencyid');
        var courseId = $(cell).data('courseid');
        var userId = $(cell).data('userid');

        log.debug('Clicked on cell: competencyId=' + competencyId + ', courseId=' + courseId + ', userId=' + userId);

        var requests = ajax.call([{
            methodname: 'tool_lp_data_for_user_competency_summary_in_course',
            args: {userid: userId, competencyid: competencyId, courseid: courseId},
        }, {
            methodname: 'core_competency_user_competency_viewed_in_course',
            args: {userid: userId, competencyid: competencyId, courseid: courseId},
        }]);

        $.when.apply($, requests).then(function(context) {
            this._contextLoaded.bind(this)(context);
            return;
        }.bind(this)).catch(notification.exception);
    };

    /**
     * We loaded the context, now render the template.
     *
     * @method _contextLoaded
     * @param {Object} context
     */
    GradingPopup.prototype._contextLoaded = function(context) {
        var self = this;
        // We have to display user info in popup.
        context.displayuser = true;
        templates.render('tool_lp/user_competency_summary_in_course', context).done(function(html, js) {
            str.get_string('usercompetencysummary', 'report_competency').done(function(title) {
                (new Dialogue(title, html, templates.runTemplateJS.bind(templates, js), self._refresh.bind(self), true));
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Refresh the page.
     *
     * @method _refresh
     */
    GradingPopup.prototype._refresh = function() {
        var region = $(this._regionSelector);
        var courseId = region.data('courseid');
        var userId = region.data('userid');

        ajax.call([{
            methodname: 'report_competency_data_for_report',
            args: {courseid: courseId, userid: userId},
            done: this._pageContextLoaded.bind(this),
            fail: notification.exception
        }]);
    };

    /**
     * We loaded the context, now render the template.
     *
     * @method _pageContextLoaded
     * @param {Object} context
     */
    GradingPopup.prototype._pageContextLoaded = function(context) {
        var self = this;
        templates.render('report_competency/report', context).done(function(html, js) {
            templates.replaceNode(self._regionSelector, html, js);
        }).fail(notification.exception);
    };

    /** @type {String} The selector for the region with the user competencies */
    GradingPopup.prototype._regionSelector = null;
    /** @type {String} The selector for the region with a single user competencies */
    GradingPopup.prototype._userCompetencySelector = null;

    return /** @alias module:report_competency/grading_popup */ GradingPopup;

});
