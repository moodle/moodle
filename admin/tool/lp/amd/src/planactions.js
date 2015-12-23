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
        'tool_lp/menubar'],
        function($, templates, ajax, notification, str, Menubar) {

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
     * Callback to render the region template.
     *
     * @param {Object} context The context for the template.
     */
    PlanActions.prototype._renderView = function(context) {
        var self = this;
        templates.render(self._template, context)
            .done(function(newhtml, newjs) {
                $(self._region).replaceWith(newhtml);
                templates.runTemplateJS(newjs);
            }.bind(self))
            .fail(notification.exception);
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
        return $.when.apply($.when, ajax.call(calls))
            .then(function() {
                self._renderView.call(self, arguments[arguments.length - 1]);
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
                methodname: 'tool_lp_delete_plan',
                args: { id: planData.id }
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
            methodname: 'tool_lp_read_plan',
            args: { id: planData.id }
        }]);

        requests[0].done(function(plan) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'deleteplan', component: 'tool_lp', param: plan.name },
                { key: 'delete', component: 'moodle' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete plan X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    function() {
                        self._doDelete(planData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Delete plan handler.
     *
     * @param  {Event} e The event.
     */
    PlanActions.prototype._deletePlanHandler = function(e) {
        e.preventDefault();
        var data = this._findPlanData($(e.target));
        this.deletePlan(data);
    };

    /**
     * Reopen plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doReopenPlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'tool_lp_reopen_plan',
                args: { planid: planData.id}
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
                methodname: 'tool_lp_read_plan',
                args: { id: planData.id }
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'reopenplanconfirm', component: 'tool_lp', param: plan.name },
                { key: 'reopenplan', component: 'tool_lp' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Reopen plan X?
                    strings[2], // reopen.
                    strings[3], // Cancel.
                    function() {
                        self._doReopenPlan(planData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Reopen plan handler.
     *
     * @param  {Event} e The event.
     */
    PlanActions.prototype._reopenPlanHandler = function(e) {
        e.preventDefault();
        var data = this._findPlanData($(e.target));
        this.reopenPlan(data);
    };

    /**
     * Complete plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doCompletePlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'tool_lp_complete_plan',
                args: { planid: planData.id}
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
                methodname: 'tool_lp_read_plan',
                args: { id: planData.id }
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'completeplanconfirm', component: 'tool_lp', param: plan.name },
                { key: 'completeplan', component: 'tool_lp' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Complete plan X?
                    strings[2], // Complete.
                    strings[3], // Cancel.
                    function() {
                        self._doCompletePlan(planData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Complete plan handler.
     *
     * @param  {Event} e The event.
     */
    PlanActions.prototype._completePlanHandler = function(e) {
        e.preventDefault();
        var data = this._findPlanData($(e.target));
        this.completePlan(data);
    };
    
    /**
     * Unlink plan and reload the region.
     *
     * @param  {Object} planData Plan data from plan node.
     */
    PlanActions.prototype._doUnlinkPlan = function(planData) {
        var self = this,
            calls = [{
                methodname: 'tool_lp_unlink_plan_from_template',
                args: { planid: planData.id}
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
                methodname: 'tool_lp_read_plan',
                args: { id: planData.id }
            }]);

        requests[0].done(function(plan) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'unlinkplantemplateconfirm', component: 'tool_lp', param: plan.name },
                { key: 'unlinkplantemplate', component: 'tool_lp' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Unlink plan X?
                    strings[2], // Unlink.
                    strings[3], // Cancel.
                    function() {
                        self._doUnlinkPlan(planData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);
    };
    
    /**
     * Unlink plan handler.
     *
     * @param  {Event} e The event.
     */
    PlanActions.prototype._unlinkPlanHandler = function(e) {
        e.preventDefault();
        var data = this._findPlanData($(e.target));
        this.unlinkPlan(data);
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
        var self = this;
        Menubar.enhance(selector, {
            '[data-action="plan-delete"]': self._deletePlanHandler.bind(self),
            '[data-action="plan-complete"]': self._completePlanHandler.bind(self),
            '[data-action="plan-reopen"]': self._reopenPlanHandler.bind(self),
            '[data-action="plan-unlink"]': self._unlinkPlanHandler.bind(self),
        });
    };

    /**
     * Register the events in the region.
     *
     * At this stage this cannot be used with enhanceMenubar or multiple handlers
     * will be added to the same node.
     */
    PlanActions.prototype.registerEvents = function() {
        var wrapper = $(this._region),
            self = this;

        wrapper.find('[data-action="plan-delete"]').click(self._deletePlanHandler.bind(self));
        wrapper.find('[data-action="plan-complete"]').click(self._completePlanHandler.bind(self));
        wrapper.find('[data-action="plan-reopen"]').click(self._reopenPlanHandler.bind(self));
        wrapper.find('[data-action="plan-unlink"]').click(self._unlinkPlanHandler.bind(self));
    };

    return PlanActions;
});
