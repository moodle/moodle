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
 * Competency picker from user plans.
 *
 * To handle 'save' events use: picker.on('save').
 *
 * This will receive a object with either a single 'competencyId', or an array in 'competencyIds'
 * depending on the value of multiSelect.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'core/str',
        'tool_lp/tree',
        'tool_lp/competencypicker'
        ],
        function($, Notification, Ajax, Templates, Str, Tree, PickerBase) {

    /**
     * Competency picker in plan class.
     *
     * @param {Number|false} singlePlan The ID of the plan when limited to one.
     * @param {String} pageContextIncludes One of 'children', 'parents', 'self'.
     * @param {Boolean} multiSelect Support multi-select in the tree.
     */
    var Picker = function(userId, singlePlan, multiSelect) {
        PickerBase.prototype.constructor.apply(this, [1, false, 'self', multiSelect]);
        this._userId = userId;
        this._plans = [];

        if (singlePlan) {
            this._planId = singlePlan;
            this._singlePlan = true;
        }
    };
    Picker.prototype = Object.create(PickerBase.prototype);

    /** @type {Array} The list of plans fetched. */
    Picker.prototype._plans = null;
    /** @type {Number} The current plan ID. */
    Picker.prototype._planId = null;
    /** @type {Boolean} Whether we can browse plans or not. */
    Picker.prototype._singlePlan = false;
    /** @type {Number} The user the plans belongs to. */
    Picker.prototype._userId = null;

    /**
     * Hook to executed after the view is rendered.
     *
     * @method _afterRender
     */
    Picker.prototype._afterRender = function() {
        var self = this;
        PickerBase.prototype._afterRender.apply(self, arguments);

        // Add listener for framework change.
        if (!self._singlePlan) {
            self._find('[data-action="chooseplan"]').change(function(e) {
                self._planId = $(e.target).val();
                self._loadCompetencies().then(self._refresh.bind(self));
            }.bind(self));
        }
    };

    /**
     * Fetch the competencies.
     *
     * @param {Number} planId The planId.
     * @param {String} searchText Limit the competencies to those matching the text.
     * @method _fetchCompetencies
     * @return {Promise}
     */
    Picker.prototype._fetchCompetencies = function(planId, searchText) {
        var self = this;

        return Ajax.call([
            { methodname: 'core_competency_list_plan_competencies', args: {
                id: planId
            }}
        ])[0].done(function(competencies) {

            // Expand the list of competencies into a fake tree.
            var i, tree = [], comp;
            for (i = 0; i < competencies.length; i++) {
                comp = competencies[i].competency;
                if (comp.shortname.toLowerCase().indexOf(searchText.toLowerCase()) < 0) {
                    continue;
                }
                comp.children = [];
                comp.haschildren = 0;
                tree.push(comp);
            }

            self._competencies = tree;

        }).fail(Notification.exception);
    };

    /**
     * Convenience method to get a plan object.
     *
     * @param {Number} id The plan ID.
     * @return {Object|undefined} The plan.
     * @method _getPlan
     */
    Picker.prototype._getPlan = function(id) {
        var plan;
        $.each(this._plans, function(i, f) {
            if (f.id == id) {
                plan = f;
                return false;
            }
        });
        return plan;
    };

    /**
     * Load the competencies.
     *
     * @method _loadCompetencies
     * @return {Promise}
     */
    Picker.prototype._loadCompetencies = function() {
        return this._fetchCompetencies(this._planId, this._searchText);
    };

    /**
     * Load the plans.
     *
     * @method _loadPlans
     * @return {Promise}
     */
    Picker.prototype._loadPlans = function() {
        var promise,
            self = this;

        // Quit early because we already have the data.
        if (self._plans.length > 0) {
            return $.when();
        }

        if (self._singlePlan) {
            promise = Ajax.call([
                { methodname: 'core_competency_read_plan', args: {
                    id: this._planId
                }}
            ])[0].then(function(plan) {
                return [plan];
            });
        } else {
            promise = Ajax.call([
                { methodname: 'core_competency_list_user_plans', args: {
                    userid: self._userId
                }}
            ])[0];
        }

        return promise.done(function(plans) {
            self._plans = plans;
        }).fail(Notification.exception);
    };

    /**
     * Hook to executed before render.
     *
     * @method _preRender
     * @return {Promise}
     */
    Picker.prototype._preRender = function() {
        var self = this;
        return self._loadPlans().then(function() {
            if (!self._planId && self._plans.length > 0) {
                self._planId = self._plans[0].id;
            }

            // We could not set a framework ID, that probably means there are no frameworks accessible.
            if (!self._planId) {
                self._plans = [];
                return $.when();
            }

            return self._loadCompetencies();
        }.bind(self));
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    Picker.prototype._render = function() {
        var self = this;
        return self._preRender().then(function() {

            if (!self._singlePlan) {
                $.each(self._plans, function(i, plan) {
                    if (plan.id == self._planId) {
                        plan.selected = true;
                    } else {
                        plan.selected = false;
                    }
                });
            }

            var context = {
                competencies: self._competencies,
                plan: self._getPlan(self._planId),
                plans: self._plans,
                search: self._searchText,
                singlePlan: self._singlePlan,
            };

            return Templates.render('tool_lp/competency_picker_user_plans', context);
        }.bind(self));
    };

    return /** @alias module:tool_lp/competencypicker_user_plans */ Picker;

});
