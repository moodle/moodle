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
 * @module     report_lpmonitoring/user_competency_popup
 * @author     Jean-Philippe Gaudreau <jp.gaudreau@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/str',
        'core/ajax',
        'core/templates',
        'core/modal_factory',
        'core/modal_events'],
    function($, notification, str, ajax, templates, ModalFactory, ModalEvents) {

        /**
         * UserCompetencyPopup
         *
         * @param {String} regionSelector The regionSelector
         * @param {String} userCompetencySelector The userCompetencySelector
         */
        var UserCompetencyPopup = function(regionSelector, userCompetencySelector) {
            this._regionSelector = regionSelector;
            this._userCompetencySelector = userCompetencySelector;
            this._competencyId = null;
            this._planId = null;
            this._userId = null;

            $(this._regionSelector).on('click', this._userCompetencySelector, this._handleClick.bind(this));
        };

        /**
         * Get the data from the clicked cell and open the popup.
         *
         * @method _handleClick
         * @param {Event} e
         */
        UserCompetencyPopup.prototype._handleClick = function(e) {
            e.preventDefault();
            var self = this;
            var cell = $(e.target).closest(this._userCompetencySelector);
            self._competencyId = $(cell).data('competencyid');
            self._planId = $(cell).data('planid');
            self._userId = $(cell).data('userid');

            var requests = ajax.call([{
                methodname : 'tool_lp_data_for_user_competency_summary_in_plan',
                args: { competencyid: self._competencyId , planid: self._planId },
                fail: notification.exception
            }]);

            // Log the user competency viewed in plan event.
            requests[0].then(function (result) {
                self._contextLoaded.bind(self)(result, cell);
                var eventMethodName = 'core_competency_user_competency_viewed_in_plan';
                // Trigger core_competency_user_competency_plan_viewed event instead if plan is already completed.
                if (result.plan.iscompleted) {
                    eventMethodName = 'core_competency_user_competency_plan_viewed';
                }
                ajax.call([{
                    methodname: eventMethodName,
                    args: {competencyid: self._competencyId, userid: self._userId, planid: self._planId},
                    fail: notification.exception
                }]);
            });
        };

        /**
         * We loaded the context, now render the template.
         *
         * @method _contextLoaded
         * @param {Object} context
         * @param {Object} trigger
         */
        UserCompetencyPopup.prototype._contextLoaded = function(context, trigger) {
            var self = this;
            return str.get_string('usercompetencysummary', 'report_competency').done(function(title) {
                return ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: templates.render('tool_lp/user_competency_summary_in_plan', context),
                    large: true
                }, trigger).done(function(modal) {
                    // Keep a reference to the modal.
                    self.popup = modal;
                    self.popup.getRoot().on(ModalEvents.hidden, function() {
                        self.focusContentItem(trigger);
                        self._refresh();
                    });
                    self.popup.show();
                }.bind(this));
            }).fail(notification.exception);
        };

        /**
         * Focus the given content item or the first focusable element within
         * the content item.
         *
         * @method focusContentItem
         * @param {object} item The content item jQuery element
         */
        UserCompetencyPopup.prototype.focusContentItem = function(item) {
            var focusable = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';
            if (item.is(focusable)) {
                item.focus();
            } else {
                item.find(focusable).first().focus();
            }
        };

        /**
         * @var {Dialogue} popup  The popup window (Dialogue).
         * @private
         */
        UserCompetencyPopup.prototype.popup = null;

        /**
         * Destroy DOM after close.
         *
         * @method close
         */
        UserCompetencyPopup.prototype.close = function() {
            this.popup.destroy();
            this.popup = null;
        };

        /**
         * Refresh the page.
         *
         * @method _refresh
         */
        UserCompetencyPopup.prototype._refresh = function() {};

        return  UserCompetencyPopup;
    });