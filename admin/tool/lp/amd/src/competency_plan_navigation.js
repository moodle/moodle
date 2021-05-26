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
 * Event click on selecting competency in the competency autocomplete.
 *
 * @module     tool_lp/competency_plan_navigation
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * CompetencyPlanNavigation
     *
     * @class
     * @param {String} competencySelector The selector of the competency element.
     * @param {String} baseUrl The base url for the page (no params).
     * @param {Number} userId The user id
     * @param {Number} competencyId The competency id
     * @param {Number} planId The plan id
     */
    var CompetencyPlanNavigation = function(competencySelector, baseUrl, userId, competencyId, planId) {
        this._baseUrl = baseUrl;
        this._userId = userId + '';
        this._competencyId = competencyId + '';
        this._planId = planId;
        this._ignoreFirstCompetency = true;

        $(competencySelector).on('change', this._competencyChanged.bind(this));
    };

    /**
     * The competency was changed in the select list.
     *
     * @method _competencyChanged
     * @param {Event} e
     */
    CompetencyPlanNavigation.prototype._competencyChanged = function(e) {
        if (this._ignoreFirstCompetency) {
            this._ignoreFirstCompetency = false;
            return;
        }
        var newCompetencyId = $(e.target).val();
        var queryStr = '?userid=' + this._userId + '&planid=' + this._planId + '&competencyid=' + newCompetencyId;
        document.location = this._baseUrl + queryStr;
    };

    /** @property {Number} The id of the competency. */
    CompetencyPlanNavigation.prototype._competencyId = null;
    /** @property {Number} The id of the user. */
    CompetencyPlanNavigation.prototype._userId = null;
    /** @property {Number} The id of the plan. */
    CompetencyPlanNavigation.prototype._planId = null;
    /** @property {String} Plugin base url. */
    CompetencyPlanNavigation.prototype._baseUrl = null;
    /** @property {Boolean} Ignore the first change event for competencies. */
    CompetencyPlanNavigation.prototype._ignoreFirstCompetency = null;

    return CompetencyPlanNavigation;
});
