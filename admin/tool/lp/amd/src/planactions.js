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
 * Plan actions via ajax.
 *
 * @module     tool_lp/planactions
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/templates',
        'core/ajax',
        'core/notification',
        'core/str',
        'tool_lp/menubar',
        'tool_lp/dialogue'],
        function($, templates, ajax, notification, str, Menubar, Dialogue) {

    /**
     * PlanActions class.
     *
     * Note that presently this cannot be instantiated more than once per page.
     *
     * @param {String} type The type of page we're in.
     */
    var PlanActions = function(type) {
        this._type = type;

        if (type === 'plan') {
            // This is the page to view one plan.
            this._region = '[data-region="plan-page"]';
            this._planNode = '[data-region="plan-page"]';
            this._template = 'tool_lp/plan_page';
            this._contextMethod = 'tool_lp_data_for_plan_page';

        } else if (type === 'plans') {
            // This is the page to view a list of plans.
            this._region = '[data-region="plans"]';
            this._planNode = '[data-region="plan-node"]';
            this._template = 'tool_lp/plans_page';
            this._contextMethod = 'tool_lp_data_for_plans_page';

        } else {
            throw new TypeError('Unexpected type.');
        }
    };

    /** @type {String} Ajax method to fetch the page data from. */
    PlanActions.prototype._contextMethod = null;
    /** @type {String} Selector to find the node describing the plan. */
    PlanActions.prototype._planNode = null;
    /** @type {String} Selector mapping to the region to update. Usually similar to wrapper. */
    PlanActions.prototype._region = null;
    /** @type {String} Name of the template used to render the region. */
    PlanActions.prototype._template = null;
    /** @type {String} Type of page/region we're in. */
    PlanActions.prototype._type = null;

    /**
     * Resolve the arguments to refresh the region.
     *
     * @param  {Object} planData Plan data from plan node.
     * @return {Object} List of arguments.
     */
    PlanActions.prototype._getContextArgs = function(planData) {
        var self = this,
            args = {};

        if (self._type === 'plan') {
            args = {
                planid: planData.id
            };

        } else if (self._type === 'plans') {
            args = {
                userid: planData.userid
            };
        }

        return args;
    };

    /**
     * Refresh the plan view.
     *
     * This is useful when you only want to refresh the view.
     *
     * @param  {String} selector The node to search the plan data from.
     */
    PlanActions.prototype.refresh = function(selector) {
        var planData = this._findPlanData($(selector));
        this._callAndRefresh([], planData);
    };

    /**
     * Callback to render the region template.
     *
     * @param {Object} context The context for the template.
     * @return {Promise}
     */
    PlanActions.prototype._renderView = function(context) {
        var self = this;
        return templates.render(self._template, context)
            .then(function(newhtml, newjs) {
                $(self._region).replaceWith(newhtml);
                templates.runTemplateJS(newjs);
                return;
            });
    };

    /**
     * Call multiple ajax methods, and refresh.
     *
     * @param  {Array}  calls    List of Ajax calls.
     * @param  {Object} planData Plan data from plan node.
     * @return {Promise}
     */
    PlanActions.prototype._callAndRefresh = function(calls, planData) {
        var self = this;
        calls.push({
            methodname: self._contextMethod,
            args: self._getContextArgs(planData)
        });

        // Apply all the promises, and refresh when the last one is resolved.
        return $.when.apply($, ajax.call(calls))
            .then(function() {
                return self._renderView(arguments[arguments.length - 1]);
            })
            .fail(notification.exception);
    };

    /**
     * Delete a plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doDelete = function(planData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_delete_plan',
                args: {id: planData.id}
            }];
        self._callAndRefresh(calls, planData);
    };

    /**
     * Delete a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype.deletePlan = function(planData) {
        var self = this,
            requests;

        requests = ajax.call([{
            methodname: 'core_competency_read_plan',
            args: {id: planData.id}
        }]);

        requests[0].done(function(plan) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'deleteplan', component: 'tool_lp', param: plan.name},
                {key: 'delete', component: 'moodle'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete plan X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    function() {
                        self._doDelete(planData);
                    }
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Reopen plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doReopenPlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_reopen_plan',
                args: {planid: planData.id}
            }];
        self._callAndRefresh(calls, planData);
    };

    /**
     * Reopen a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype.reopenPlan = function(planData) {
        var self = this,
            requests = ajax.call([{
                methodname: 'core_competency_read_plan',
                args: {id: planData.id}
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'reopenplanconfirm', component: 'tool_lp', param: plan.name},
                {key: 'reopenplan', component: 'tool_lp'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Reopen plan X?
                    strings[2], // Reopen.
                    strings[3], // Cancel.
                    function() {
                        self._doReopenPlan(planData);
                    }
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Complete plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doCompletePlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_complete_plan',
                args: {planid: planData.id}
            }];
        self._callAndRefresh(calls, planData);
    };

    /**
     * Complete a plan process.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype.completePlan = function(planData) {
        var self = this,
            requests = ajax.call([{
                methodname: 'core_competency_read_plan',
                args: {id: planData.id}
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'completeplanconfirm', component: 'tool_lp', param: plan.name},
                {key: 'completeplan', component: 'tool_lp'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Complete plan X?
                    strings[2], // Complete.
                    strings[3], // Cancel.
                    function() {
                        self._doCompletePlan(planData);
                    }
                );
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Unlink plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doUnlinkPlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_unlink_plan_from_template',
                args: {planid: planData.id}
            }];
        self._callAndRefresh(calls, planData);
    };

    /**
     * Unlink a plan process.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype.unlinkPlan = function(planData) {
        var self = this,
            requests = ajax.call([{
                methodname: 'core_competency_read_plan',
                args: {id: planData.id}
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'unlinkplantemplateconfirm', component: 'tool_lp', param: plan.name},
                {key: 'unlinkplantemplate', component: 'tool_lp'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Unlink plan X?
                    strings[2], // Unlink.
                    strings[3], // Cancel.
                    function() {
                        self._doUnlinkPlan(planData);
                    }
                );
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Request review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doRequestReview
     */
    PlanActions.prototype._doRequestReview = function(planData) {
        var calls = [{
            methodname: 'core_competency_plan_request_review',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Request review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method requestReview
     */
    PlanActions.prototype.requestReview = function(planData) {
        this._doRequestReview(planData);
    };

    /**
     * Cancel review request of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doCancelReviewRequest
     */
    PlanActions.prototype._doCancelReviewRequest = function(planData) {
        var calls = [{
            methodname: 'core_competency_plan_cancel_review_request',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Cancel review request of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method cancelReviewRequest
     */
    PlanActions.prototype.cancelReviewRequest = function(planData) {
        this._doCancelReviewRequest(planData);
    };

    /**
     * Start review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doStartReview
     */
    PlanActions.prototype._doStartReview = function(planData) {
        var calls = [{
            methodname: 'core_competency_plan_start_review',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Start review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method startReview
     */
    PlanActions.prototype.startReview = function(planData) {
        this._doStartReview(planData);
    };

    /**
     * Stop review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doStopReview
     */
    PlanActions.prototype._doStopReview = function(planData) {
        var calls = [{
            methodname: 'core_competency_plan_stop_review',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Stop review of a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method stopReview
     */
    PlanActions.prototype.stopReview = function(planData) {
        this._doStopReview(planData);
    };

    /**
     * Approve a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doApprove
     */
    PlanActions.prototype._doApprove = function(planData) {
        var calls = [{
            methodname: 'core_competency_approve_plan',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Approve a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method approve
     */
    PlanActions.prototype.approve = function(planData) {
        this._doApprove(planData);
    };

    /**
     * Unapprove a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method _doUnapprove
     */
    PlanActions.prototype._doUnapprove = function(planData) {
        var calls = [{
            methodname: 'core_competency_unapprove_plan',
            args: {
                id: planData.id
            }
        }];
        this._callAndRefresh(calls, planData);
    };

    /**
     * Unapprove a plan.
     *
     * @param  {Object} planData Plan data from plan node.
     * @method unapprove
     */
    PlanActions.prototype.unapprove = function(planData) {
        this._doUnapprove(planData);
    };

    /**
     * Display list of linked courses on a modal dialogue.
     *
     * @param  {Event} e The event.
     */
    PlanActions.prototype._showLinkedCoursesHandler = function(e) {
        e.preventDefault();

        var competencyid = $(e.target).data('id');
        var requests = ajax.call([{
            methodname: 'tool_lp_list_courses_using_competency',
            args: {id: competencyid}
        }]);

        requests[0].done(function(courses) {
            var context = {
                courses: courses
            };
            templates.render('tool_lp/linked_courses_summary', context).done(function(html) {
                str.get_string('linkedcourses', 'tool_lp').done(function(linkedcourses) {
                    new Dialogue(
                        linkedcourses, // Title.
                        html // The linked courses.
                    );
                }).fail(notification.exception);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Plan event handler.
     *
     * @param  {String} method The method to call.
     * @param  {Event} e The event.
     * @method _eventHandler
     */
    PlanActions.prototype._eventHandler = function(method, e) {
        e.preventDefault();
        var data = this._findPlanData($(e.target));
        this[method](data);
    };

    /**
     * Find the plan data from the plan node.
     *
     * @param  {Node} node The node to search from.
     * @return {Object} Plan data.
     */
    PlanActions.prototype._findPlanData = function(node) {
        var parent = node.parentsUntil($(this._region).parent(), this._planNode),
            data;

        if (parent.length != 1) {
            throw new Error('The plan node was not located.');
        }

        data = parent.data();
        if (typeof data === 'undefined' || typeof data.id === 'undefined') {
            throw new Error('Plan data could not be found.');
        }

        return data;
    };

    /**
     * Enhance a menu bar.
     *
     * @param  {String} selector Menubar selector.
     */
    PlanActions.prototype.enhanceMenubar = function(selector) {
        Menubar.enhance(selector, {
            '[data-action="plan-delete"]': this._eventHandler.bind(this, 'deletePlan'),
            '[data-action="plan-complete"]': this._eventHandler.bind(this, 'completePlan'),
            '[data-action="plan-reopen"]': this._eventHandler.bind(this, 'reopenPlan'),
            '[data-action="plan-unlink"]': this._eventHandler.bind(this, 'unlinkPlan'),
            '[data-action="plan-request-review"]': this._eventHandler.bind(this, 'requestReview'),
            '[data-action="plan-cancel-review-request"]': this._eventHandler.bind(this, 'cancelReviewRequest'),
            '[data-action="plan-start-review"]': this._eventHandler.bind(this, 'startReview'),
            '[data-action="plan-stop-review"]': this._eventHandler.bind(this, 'stopReview'),
            '[data-action="plan-approve"]': this._eventHandler.bind(this, 'approve'),
            '[data-action="plan-unapprove"]': this._eventHandler.bind(this, 'unapprove'),
        });
    };

    /**
     * Register the events in the region.
     *
     * At this stage this cannot be used with enhanceMenubar or multiple handlers
     * will be added to the same node.
     */
    PlanActions.prototype.registerEvents = function() {
        var wrapper = $(this._region);

        wrapper.find('[data-action="plan-delete"]').click(this._eventHandler.bind(this, 'deletePlan'));
        wrapper.find('[data-action="plan-complete"]').click(this._eventHandler.bind(this, 'completePlan'));
        wrapper.find('[data-action="plan-reopen"]').click(this._eventHandler.bind(this, 'reopenPlan'));
        wrapper.find('[data-action="plan-unlink"]').click(this._eventHandler.bind(this, 'unlinkPlan'));

        wrapper.find('[data-action="plan-request-review"]').click(this._eventHandler.bind(this, 'requestReview'));
        wrapper.find('[data-action="plan-cancel-review-request"]').click(this._eventHandler.bind(this, 'cancelReviewRequest'));
        wrapper.find('[data-action="plan-start-review"]').click(this._eventHandler.bind(this, 'startReview'));
        wrapper.find('[data-action="plan-stop-review"]').click(this._eventHandler.bind(this, 'stopReview'));
        wrapper.find('[data-action="plan-approve"]').click(this._eventHandler.bind(this, 'approve'));
        wrapper.find('[data-action="plan-unapprove"]').click(this._eventHandler.bind(this, 'unapprove'));

        wrapper.find('[data-action="find-courses-link"]').click(this._showLinkedCoursesHandler.bind(this));
    };

    return PlanActions;
});
