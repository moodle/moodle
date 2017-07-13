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
 * Module to open user competency plan in popup
 *
 * @package    report_competency
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/str', 'core/ajax', 'core/templates', 'tool_lp/dialogue'],
       function($, notification, str, ajax, templates, Dialogue) {

    /**
     * UserCompetencyPopup
     *
     * @param {String} regionSelector The regionSelector
     * @param {String} userCompetencySelector The userCompetencySelector
     * @param {Number} planId The plan ID
     */
    var UserCompetencyPopup = function(regionSelector, userCompetencySelector, planId) {
        this._regionSelector = regionSelector;
        this._userCompetencySelector = userCompetencySelector;
        this._planId = planId;

        $(this._regionSelector).on('click', this._userCompetencySelector, this._handleClick.bind(this));
    };

    /**
     * Get the data from the closest TR and open the popup.
     *
     * @method _handleClick
     * @param {Event} e
     */
    UserCompetencyPopup.prototype._handleClick = function(e) {
        e.preventDefault();
        var tr = $(e.target).closest('tr');
        var competencyId = $(tr).data('competencyid');
        var userId = $(tr).data('userid');
        var planId = this._planId;

        var requests = ajax.call([{
            methodname: 'tool_lp_data_for_user_competency_summary_in_plan',
            args: {competencyid: competencyId, planid: planId},
            done: this._contextLoaded.bind(this),
            fail: notification.exception
        }]);
        // Log the user competency viewed in plan event.
        requests[0].then(function(result) {
            var eventMethodName = 'core_competency_user_competency_viewed_in_plan';
            // Trigger core_competency_user_competency_plan_viewed event instead if plan is already completed.
            if (result.plan.iscompleted) {
                eventMethodName = 'core_competency_user_competency_plan_viewed';
            }
            return ajax.call([{
                methodname: eventMethodName,
                args: {competencyid: competencyId, userid: userId, planid: planId}
            }])[0];
        }).catch(notification.exception);
    };

    /**
     * We loaded the context, now render the template.
     *
     * @method _contextLoaded
     * @param {Object} context
     */
    UserCompetencyPopup.prototype._contextLoaded = function(context) {
        var self = this;
        templates.render('tool_lp/user_competency_summary_in_plan', context).done(function(html, js) {
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
    UserCompetencyPopup.prototype._refresh = function() {
        var planId = this._planId;

        ajax.call([{
            methodname: 'tool_lp_data_for_plan_page',
            args: {planid: planId},
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
    UserCompetencyPopup.prototype._pageContextLoaded = function(context) {
        var self = this;
        templates.render('tool_lp/plan_page', context).done(function(html, js) {
            templates.replaceNode(self._regionSelector, html, js);
        }).fail(notification.exception);
    };

    /** @type {String} The selector for the region with the user competencies */
    UserCompetencyPopup.prototype._regionSelector = null;
    /** @type {String} The selector for the region with a single user competencies */
    UserCompetencyPopup.prototype._userCompetencySelector = null;
    /** @type {Number} The plan Id */
    UserCompetencyPopup.prototype._planId = null;

    return /** @alias module:tool_lp/user_competency_plan_popup */ UserCompetencyPopup;

});
