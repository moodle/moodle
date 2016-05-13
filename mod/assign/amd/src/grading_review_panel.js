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
define(['jquery'], function($) {

    /**
     * GradingReviewPanel class.
     *
     * @class GradingReviewPanel
     * @param {String} selector The selector for the page region containing the user navigation.
     */
    var GradingReviewPanel = function() {
        this._region = $('[data-region="review-panel"]');
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

    return GradingReviewPanel;
});
