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
 * @submodule highlighter
 */

/**
 * Provides row, and column highlighting functionality to the grader report.
 *
 * See {{#crossLink "M.gradereport_grader.ReportTable"}}{{/crossLink}} for details.
 *
 * @namespace M.gradereport_grader
 * @class Highlighter
 */

var COLMARK = 'vmarked',
    UIDMARK = 'hmarked';

function Highlighter() {}

Highlighter.ATTRS= {
};

var ROWFIELDS = '.user.cell, th.userreport, th.userfield',
    COLFIELDS = 'tr[data-itemid] th.item, .heading .cell';

Highlighter.prototype = {
    /**
     * Setup column and row highlighting.
     *
     * @method setupHighlighter
     * @chainable
     */
    setupHighlighter: function() {
        this._eventHandles.push(
            // Clicking on the cell should highlight the row.
            this.graderRegion.delegate('click', this._highlightUser, ROWFIELDS, this),

            // Clicking on the cell should highlight the current column.
            this.graderRegion.delegate('click', this._highlightColumn, COLFIELDS, this)
        );

        return this;
    },

    /**
     * Highlight the current assignment column.
     *
     * @method _highlightColumn
     * @param {EventFacade} e The Event fired. This describes the column to highlight.
     * @protected
     */
    _highlightColumn: function(e) {
        var itemid = e.target.getData('itemid');

        if (typeof itemid === 'undefined') {
            // Unable to determine which user to highlight. Return early.
            return;
        }

        this.graderRegion.all('td.cell[data-itemid="' + itemid + '"]').toggleClass(COLMARK);
    },

    /**
     * Highlight the current user row.
     *
     * @method _highlightUser
     * @param {EventFacade} e The Event fired. This describes the user to highlight.
     * @protected
     */
    _highlightUser: function(e) {
        var clickedRow = e.target.ancestor('[data-uid]', true),
            tableRow,
            uid;

        if (clickedRow) {
            uid = clickedRow.getData('uid');
        }

        if (typeof uid === 'undefined') {
            // Unable to determine which user to highlight. Return early.
            return;
        }

        tableRow = this.graderRegion.one('tr[data-uid="' + uid + '"]');
        if (tableRow) {
            tableRow.toggleClass(UIDMARK);
        }
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [Highlighter]);
