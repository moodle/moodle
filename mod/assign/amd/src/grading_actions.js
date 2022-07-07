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
define(['jquery', 'mod_assign/grading_events'], function($, GradingEvents) {

    /**
     * GradingActions class.
     *
     * @class GradingActions
     * @param {String} selector The selector for the page region containing the actions panel.
     */
    var GradingActions = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);

        this.registerEventListeners();
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

    /**
     * Get the review panel element.
     *
     * @method getReviewPanelElement
     * @return {jQuery}
     */
    GradingActions.prototype.getReviewPanelElement = function() {
        return $('[data-region="review-panel"]');
    };

    /**
     * Check if the page has a review panel.
     *
     * @method hasReviewPanelElement
     * @return {bool}
     */
    GradingActions.prototype.hasReviewPanelElement = function() {
        return this.getReviewPanelElement().length > 0;
    };

    /**
     * Get the collapse grade panel button.
     *
     * @method getCollapseGradePanelButton
     * @return {jQuery}
     */
    GradingActions.prototype.getCollapseGradePanelButton = function() {
        return $('[data-region="grade-actions"] .collapse-grade-panel');
    };

    /**
     * Get the collapse review panel button.
     *
     * @method getCollapseReviewPanelButton
     * @return {jQuery}
     */
    GradingActions.prototype.getCollapseReviewPanelButton = function() {
        return $('[data-region="grade-actions"] .collapse-review-panel');
    };

    /**
     * Get the expand all panels button.
     *
     * @method getExpandAllPanelsButton
     * @return {jQuery}
     */
    GradingActions.prototype.getExpandAllPanelsButton = function() {
        return $('[data-region="grade-actions"] .collapse-none');
    };

    /**
     * Remove the active state from all layout buttons.
     *
     * @method resetLayoutButtons
     */
    GradingActions.prototype.resetLayoutButtons = function() {
        this.getCollapseGradePanelButton().removeClass('active');
        this.getCollapseReviewPanelButton().removeClass('active');
        this.getExpandAllPanelsButton().removeClass('active');
    };

    /**
     * Hide the review panel.
     *
     * @method collapseReviewPanel
     */
    GradingActions.prototype.collapseReviewPanel = function() {
        $(document).trigger(GradingEvents.COLLAPSE_REVIEW_PANEL);
        $(document).trigger(GradingEvents.EXPAND_GRADE_PANEL);
        this.resetLayoutButtons();
        this.getCollapseReviewPanelButton().addClass('active');
    };

    /**
     * Show/Hide the grade panel.
     *
     * @method collapseGradePanel
     */
    GradingActions.prototype.collapseGradePanel = function() {
        $(document).trigger(GradingEvents.COLLAPSE_GRADE_PANEL);
        $(document).trigger(GradingEvents.EXPAND_REVIEW_PANEL);
        this.resetLayoutButtons();
        this.getCollapseGradePanelButton().addClass('active');
    };

    /**
     * Return the layout to default.
     *
     * @method expandAllPanels
     */
    GradingActions.prototype.expandAllPanels = function() {
        $(document).trigger(GradingEvents.EXPAND_GRADE_PANEL);
        $(document).trigger(GradingEvents.EXPAND_REVIEW_PANEL);
        this.resetLayoutButtons();
        this.getExpandAllPanelsButton().addClass('active');
    };

    /**
     * Register event listeners for the grade panel.
     *
     * @method registerEventListeners
     */
    GradingActions.prototype.registerEventListeners = function() {
        // Don't need layout controls if there is no review panel.
        if (this.hasReviewPanelElement()) {
            var collapseReviewPanelButton = this.getCollapseReviewPanelButton();
            collapseReviewPanelButton.click(function(e) {
                this.collapseReviewPanel();
                e.preventDefault();
            }.bind(this));

            collapseReviewPanelButton.keydown(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode === 13 || e.keyCode === 32) {
                        this.collapseReviewPanel();
                        e.preventDefault();
                    }
                }
            }.bind(this));

            var collapseGradePanelButton = this.getCollapseGradePanelButton();
            collapseGradePanelButton.click(function(e) {
                this.collapseGradePanel();
                e.preventDefault();
            }.bind(this));

            collapseGradePanelButton.keydown(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode === 13 || e.keyCode === 32) {
                        this.collapseGradePanel();
                        e.preventDefault();
                    }
                }
            }.bind(this));

            var expandAllPanelsButton = this.getExpandAllPanelsButton();
            expandAllPanelsButton.click(function(e) {
                this.expandAllPanels();
                e.preventDefault();
            }.bind(this));

            expandAllPanelsButton.keydown(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode === 13 || e.keyCode === 32) {
                        this.expandAllPanels();
                        e.preventDefault();
                    }
                }
            }.bind(this));
        }

        $(document).on('user-changed', this._showActionsForm.bind(this));

        this._region.find('[name="savechanges"]').on('click', this._trigger.bind(this, 'save-changes'));
        this._region.find('[name="saveandshownext"]').on('click', this._trigger.bind(this, 'save-and-show-next'));
        this._region.find('[name="resetbutton"]').on('click', this._trigger.bind(this, 'reset'));
        this._region.find('form').on('submit', function(e) {
            e.preventDefault();
        });
    };

    return GradingActions;
});
