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
 * Javascript controller for the "Review" panel at the left of the page.
 *
 * @module     mod_assign/grading_review_panel
 * @package    mod_assign
 * @class      GradingReviewPanel
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'mod_assign/grading_events'], function($, GradingEvents) {

    /**
     * GradingReviewPanel class.
     *
     * @class GradingReviewPanel
     * @param {String} selector The selector for the page region containing the user navigation.
     */
    var GradingReviewPanel = function() {
        this._region = $('[data-region="review-panel-content"]');
        this.registerEventListeners();
    };

    /** @type {JQuery} JQuery node for the page region containing the user navigation. */
    GradingReviewPanel.prototype._region = null;

    /**
     * It is first come first served to get ownership of the grading review panel.
     * There can be only one.
     *
     * @public
     * @method getReviewPanel
     * @param {String} pluginname - the first plugin to ask for the panel gets it.
     * @return {DOMNode} or false
     */
    GradingReviewPanel.prototype.getReviewPanel = function(pluginname) {
        var owner = this._region.data('panel-owner');
        if (typeof owner == "undefined") {
            this._region.data('review-panel-plugin', pluginname);
        }
        if (this._region.data('review-panel-plugin') == pluginname) {
            return this._region[0];
        }
        return false;
    };

    /**
     * Get the toggle review panel button.
     *
     * @method getTogglePanelButton
     * @return {jQuery}
     */
    GradingReviewPanel.prototype.getTogglePanelButton = function() {
        return this.getPanelElement().find('[data-region="review-panel-toggle"]');
    };

    /**
     * Get the review panel element.
     *
     * @method getPanelElement
     * @return {jQuery}
     */
    GradingReviewPanel.prototype.getPanelElement = function() {
        return $('[data-region="review-panel"]');
    };

    /**
     * Get the review panel content element.
     *
     * @method getPanelContentElement
     * @return {jQuery}
     */
    GradingReviewPanel.prototype.getPanelContentElement = function() {
        return $('[data-region="review-panel-content"]');
    };

    /**
     * Show/Hide the review panel.
     *
     * @method togglePanel
     */
    GradingReviewPanel.prototype.togglePanel = function() {
        if (this.getPanelElement().hasClass('collapsed')) {
            $(document).trigger(GradingEvents.EXPAND_REVIEW_PANEL);
        } else {
            $(document).trigger(GradingEvents.COLLAPSE_REVIEW_PANEL);
        }
    };

    /**
     * Hide the review panel.
     *
     * @method collapsePanel
     */
    GradingReviewPanel.prototype.collapsePanel = function() {
        this.getPanelElement().addClass('collapsed').removeClass('grade-panel-collapsed');
        this.getPanelContentElement().attr('aria-hidden', true);
    };

    /**
     * Show the review panel.
     *
     * @method expandPanel
     */
    GradingReviewPanel.prototype.expandPanel = function() {
        this.getPanelElement().removeClass('collapsed');
        this.getPanelContentElement().removeAttr('aria-hidden');
    };

    /**
     * Register event listeners for the review panel.
     *
     * @method registerEventListeners
     */
    GradingReviewPanel.prototype.registerEventListeners = function() {
        var toggleReviewPanelButton = this.getTogglePanelButton();
        toggleReviewPanelButton.click(function(e) {
            this.togglePanel();
            e.preventDefault();
        }.bind(this));

        toggleReviewPanelButton.keydown(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode === 13 || e.keyCode === 32) {
                    this.togglePanel();
                    e.preventDefault();
                }
            }
        }.bind(this));

        var docElement = $(document);
        docElement.on(GradingEvents.COLLAPSE_REVIEW_PANEL, function() {
            this.collapsePanel();
        }.bind(this));

        // Need special styling when grade panel is collapsed.
        docElement.on(GradingEvents.COLLAPSE_GRADE_PANEL, function() {
            this.expandPanel();
            this.getPanelElement().addClass('grade-panel-collapsed');
        }.bind(this));

        docElement.on(GradingEvents.EXPAND_REVIEW_PANEL, function() {
            this.expandPanel();
        }.bind(this));

        docElement.on(GradingEvents.EXPAND_GRADE_PANEL, function() {
            this.getPanelElement().removeClass('grade-panel-collapsed');
        }.bind(this));
    };

    return GradingReviewPanel;
});
