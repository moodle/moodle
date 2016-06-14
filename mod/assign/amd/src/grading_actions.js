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
 * Javascript controller for the "Actions" panel at the bottom of the page.
 *
 * @module     mod_assign/grading_actions
 * @package    mod_assign
 * @class      GradingActions
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery'], function($) {

    /**
     * GradingActions class.
     *
     * @class GradingActions
     * @param {String} selector The selector for the page region containing the actions panel.
     */
    var GradingActions = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);

        $(document).on('user-changed', this._showActionsForm.bind(this));

        this._region.find('[name="savechanges"]').on('click', this._trigger.bind(this, 'save-changes'));
        this._region.find('[name="resetbutton"]').on('click', this._trigger.bind(this, 'reset'));
        this._region.find('form').on('submit', function(e) { e.preventDefault(); });
    };

    /** @type {String} Selector for the page region containing the user navigation. */
    GradingActions.prototype._regionSelector = null;

    /** @type {Integer} Remember the last user id to prevent unnessecary reloads. */
    GradingActions.prototype._lastUserId = 0;

    /** @type {JQuery} JQuery node for the page region containing the user navigation. */
    GradingActions.prototype._region = null;

    /**
     * Show the actions if there is valid user.
     *
     * @method _showActionsForm
     * @private
     * @param {Event} event
     * @param {Integer} userid
     * @return {Deferred} promise resolved when the animations are complete.
     */
    GradingActions.prototype._showActionsForm = function(event, userid) {
        var form = this._region.find('[data-region=grading-actions-form]');

        if (userid != this._lastUserId && userid > 0) {
            this._lastUserId = userid;
        }
        if (userid > 0) {
            form.removeClass('hide');
        } else {
            form.addClass('hide');
        }

    };

    /**
     * Trigger the named action.
     *
     * @method _trigger
     * @private
     * @param {String} action
     */
    GradingActions.prototype._trigger = function(action) {
        $(document).trigger(action);
    };

    return GradingActions;
});
