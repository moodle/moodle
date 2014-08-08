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
 * @module moodle-gradereport_grader-gradereporttable
 * @submodule tooltip
 */

/**
 * Provides tooltip functionality on the grader report.
 *
 * See {{#crossLink "M.gradereport_grader.ReportTable"}}{{/crossLink}} for details.
 *
 * @namespace M.gradereport_grader
 * @class Tooltip
 */

function Tooltip() {}

Tooltip.ATTRS= {
};

var CONTENT =   '<div class="graderreportoverlay {{overridden}}" role="tooltip" aria-describedby="{{id}}">' +
                    '<div class="fullname">{{username}}</div><div class="itemname">{{itemname}}</div>' +
                    '{{#if feedback}}' +
                        '<div class="feedback">{{feedback}}</div>' +
                    '{{/if}}' +
                '</div>';

Tooltip.prototype = {
    /**
     * A reference to the tooltip. A single tooltip is lazily instantiated
     * and then reused.
     *
     * @property _tooltip
     * @type Overlay
     * @protected
     */
    _tooltip: null,

    /**
     * A reference to the boundingBox of the tooltip. This is used as an
     * optimisation in the hideTooltip test.
     *
     * @property _tooltipBoundingBox
     * @type Node
     * @protected
     */
    _tooltipBoundingBox: null,

    /**
     * The compiled template for the tooltip content.
     * This is setup the first time that {{#crossLink "_getTooltip"}}{{/crossLink}} is called.
     *
     * @property _tooltipTemplate
     * @type Function
     * @default null
     * @protected
     */
    _tooltipTemplate: null,

    /**
     * Setup the tooltip.
     *
     * @method setupTooltips
     * @chainable
     */
    setupTooltips: function() {
        this._eventHandles.push(
            this.graderTable.delegate('hover', this._showTooltip, this._hideTooltip, SELECTORS.GRADECELL, this),
            this.graderTable.delegate('click', this._toggleTooltip, SELECTORS.GRADECELL, this)
        );

        return this;
    },

    /**
     * Prepare and retrieve the tooltip.
     *
     * @method _getTooltip
     * @return Overlay
     * @protected
     */
    _getTooltip: function() {
        if (!this._tooltip) {
            this._tooltip = new Y.Overlay({
                visible: false,
                render: Y.one(SELECTORS.GRADEPARENT)
            });
            this._tooltipBoundingBox = this._tooltip.get('boundingBox');
            this._tooltipTemplate = Y.Handlebars.compile(CONTENT);
            this._tooltipBoundingBox.addClass('grader-information-tooltip');
        }
        return this._tooltip;
    },

    /**
     * Display the tooltip.
     *
     * @method _showTooltip
     * @param {EventFacade} e
     * @protected
     */
    _showTooltip: function(e) {
        var cell = e.currentTarget;

        var tooltip = this._getTooltip();

        tooltip.set('bodyContent', this._tooltipTemplate({
                    cellid: cell.get('id'),
                    username: this.getGradeUserName(cell),
                    itemname: this.getGradeItemName(cell),
                    feedback: this.getGradeFeedback(cell),
                    overridden: cell.hasClass(CSS.OVERRIDDEN) ? CSS.OVERRIDDEN : ''
                }))
                .set('xy', [
                    cell.getX() + (cell.get('offsetWidth') / 2),
                    cell.getY() + (cell.get('offsetHeight') / 2)
                ])
                .show();
        e.currentTarget.addClass(CSS.TOOLTIPACTIVE);
    },

    /**
     * Hide the tooltip.
     *
     * @method _hideTooltip
     * @param {EventFacade} e
     * @protected
     */
    _hideTooltip: function(e) {
        if (e.relatedTarget && this._tooltipBoundingBox && this._tooltipBoundingBox.contains(e.relatedTarget)) {
            // Do not exit if the user is mousing over the tooltip itself.
            return;
        }
        if (this._tooltip) {
            e.currentTarget.removeClass(CSS.TOOLTIPACTIVE);
            this._tooltip.hide();
        }
    },

    /**
     * Toggle the tooltip between visible and hidden.
     *
     * @method _toggleTooltip
     * @param {EventFacade} e
     * @protected
     */
    _toggleTooltip: function(e) {
        if (e.currentTarget.hasClass(CSS.TOOLTIPACTIVE)) {
            this._hideTooltip(e);
        } else {
            this._showTooltip(e);
        }
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [Tooltip]);
