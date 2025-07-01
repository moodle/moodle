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
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'mod_assign/grading_events'], function($, GradingEvents) {

    /**
     * GradingActions class.
     *
     * @class mod_assign/grading_actions
     * @param {String} selector The selector for the page region containing the actions panel.
     */
    var GradingActions = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);

        this.registerEventListeners();
    };

    /** @property {String} Selector for the page region containing the user navigation. */
    GradingActions.prototype._regionSelector = null;

    /** @property {Integer} Remember the last user id to prevent unnessecary reloads. */
    GradingActions.prototype._lastUserId = 0;

    /** @property {JQuery} JQuery node for the page region containing the user navigation. */
    GradingActions.prototype._region = null;

    /** @property {Integer} Lower percent limit (mouseX / pagewidth), collapses review panel if exceeded during resizing. */
    GradingActions.prototype.LOWER_RESIZING_LIMIT = 5;

    /** @property {Integer} Upper percent limit (mouseX / pagewidth), collapses grade panel if exceeded  during resizing. */
    GradingActions.prototype.UPPER_RESIZING_LIMIT = 95;

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
     * Get the review slider element.
     *
     * @method getResizePanelsElement
     * @return {HTMLElement} The resizer element or null if not found.
     */
    GradingActions.prototype.getResizePanelsElement = function() {
        return document.querySelector('[data-region="resizer"]');
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
        this.setPanelSplit(0);
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
        this.setPanelSplit(100);
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
        this.getResizePanelsElement().classList.remove('hide');
        this.setPanelSplit(70);
    };


    /**
     * This function enabled the tracking of mouse movement for resizing.
     *
     * @method onResizeStart
     */
    GradingActions.prototype.onResizeStart = function() {
        // Bind and store the handlers so we can remove them later.
        this.handleMouseMove = this.onResizing.bind(this);
        this.handleMouseUp = this.onResizeEnd.bind(this);

        // Add a class to disable pointer events on iframes during resizing.
        // This is to prevent the mouse events from being captured by iframes,
        // preventing the mouse up event from triggering.
        document.querySelectorAll('iframe').forEach(function(iframe) {
            iframe.classList.add('disable-pointer');
        });

        document.addEventListener('mousemove', this.handleMouseMove);
        document.addEventListener('mouseup', this.handleMouseUp);
    };

    /**
     * When user releases the mouse button, finishing resizing, we stop tracking the mouse movement.
     *
     * @method onResizeEnd
     */
    GradingActions.prototype.onResizeEnd = function() {
        document.querySelectorAll('iframe').forEach(function(iframe) {
            iframe.classList.remove('disable-pointer');
        });
        document.removeEventListener('mousemove', this.handleMouseMove);
        document.removeEventListener('mouseup', this.handleMouseUp);
    };

    /**
     * Set the CSS variable for panel split.
     *
     * @method setPanelSplit
     * @param {Number} percentage The percentage to set.
     */
    GradingActions.prototype.setPanelSplit = function(percentage) {
        if (percentage <= 0 || percentage >= 100) {
            this.getResizePanelsElement().classList.add('hide');
        }

        document.documentElement.style.setProperty('--mod-assign-panel-split', percentage + '%');
    };

    /**
     * Get the CSS variable for panel split.
     *
     * @method getPanelSplit
     * @return {Number} The current panel split percentage.
     */
    GradingActions.prototype.getPanelSplit = function() {
        const split = getComputedStyle(document.documentElement).getPropertyValue('--mod-assign-panel-split');
        return parseFloat(split);
    };

    /**
     * Handle the resize action.
     *
     * @method onResizing
     * @param {Event} e The mousemove event.
     */
    GradingActions.prototype.onResizing = function(e) {
        const x = e.clientX;
        const pagewidth = document.documentElement.clientWidth;
        let percentage = (x / pagewidth) * 100;

        // Flip percentage in RTL.
        const isRTL = document.documentElement.dir === 'rtl';
        if (isRTL) {
            percentage = 100 - percentage;
        }

        // When the user resizes panels to a certain exist, we collapse them.
        if (percentage < this.LOWER_RESIZING_LIMIT || percentage > this.UPPER_RESIZING_LIMIT) {
            if (percentage > this.UPPER_RESIZING_LIMIT) {
                this.collapseGradePanel();
            } else if (percentage < this.LOWER_RESIZING_LIMIT) {
                this.collapseReviewPanel();
            }
            this.onResizeEnd();
            return;
        }

        this.setPanelSplit(percentage);
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

            var resizePanelsSlider = this.getResizePanelsElement();
            resizePanelsSlider.addEventListener('mousedown', function(e) {
                this.onResizeStart();
                e.preventDefault();
            }.bind(this));

            resizePanelsSlider.addEventListener('keydown', function(e) {
                if (![37, 39, 38, 40].includes(e.keyCode)) {
                    // Ignore keys other than arrow keys.
                    return;
                }


                const isRTL = document.documentElement.dir === 'rtl';
                const currentSplit = this.getPanelSplit();

                // Flip percentage in RTL.
                var increment = isRTL ? -5 : 5;
                var newValue = currentSplit;

                // Left or down arrow key.
                if (e.keyCode === 37 || e.keyCode === 40) {
                    newValue = currentSplit - increment;
                }

                // Right or up arrow key.
                if (e.keyCode === 39 || e.keyCode === 38) {
                    newValue = currentSplit + increment;
                }

                // Add extra space to prevent the panel immediately collapsing
                // when the user tries to resize it using the mouse.
                const extraSpace = 3;

                newValue = Math.max(newValue, this.LOWER_RESIZING_LIMIT + extraSpace);
                newValue = Math.min(newValue, this.UPPER_RESIZING_LIMIT - extraSpace);

                this.setPanelSplit(newValue);
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
