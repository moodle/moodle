YUI.add('moodle-atto_table-button', function (Y, NAME) {

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
 * @package    atto_table
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_table-button
 */

/**
 * Atto text editor table plugin.
 *
 * @namespace M.atto_table
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENT = 'atto_table',
    TEMPLATE = '' +
        '<form class="atto_form">' +
            '<label for="{{elementid}}_atto_table_caption">{{get_string "caption" component}}</label>' +
            '<textarea class="caption" id="{{elementid}}_atto_table_caption" rows="4" class="fullwidth" required></textarea>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_headers" class="sameline">{{get_string "headers" component}}</label>' +
            '<select class="headers" id="{{elementid}}_atto_table_headers">' +
                '<option value="columns">{{get_string "columns" component}}' + '</option>' +
                '<option value="rows">{{get_string "rows" component}}' + '</option>' +
                '<option value="both">{{get_string "both" component}}' + '</option>' +
            '</select>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_rows" class="sameline">{{get_string "numberofrows" component}}</label>' +
            '<input class="rows" type="number" value="3" id="{{elementid}}_atto_table_rows" size="8" min="1" max="50"/>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_columns" class="sameline">{{get_string "numberofcolumns" component}}</label>' +
            '<input class="columns" type="number" value="3" id="{{elementid}}_atto_table_columns" size="8" min="1" max="20"/>' +
            '<br/>' +
            '<div class="mdl-align">' +
                '<br/>' +
                '<button class="submit" type="submit">{{get_string "createtable" component}}</button>' +
            '</div>' +
        '</form>',
    CONTEXTMENUTEMPLATE = '' +
        '<ul>' +
            '<li><a href="#" data-change="addcolumnafter">{{get_string "addcolumnafter" component}}</a></li>' +
            '<li><a href="#" data-change="addrowafter">{{get_string "addrowafter" component}}</a></li>' +
            '<li><a href="#" data-change="moverowup">{{get_string "moverowup" component}}</a></li>' +
            '<li><a href="#" data-change="moverowdown">{{get_string "moverowdown" component}}</a></li>' +
            '<li><a href="#" data-change="movecolumnleft">{{get_string "movecolumnleft" component}}</a></li>' +
            '<li><a href="#" data-change="movecolumnright">{{get_string "movecolumnright" component}}</a></li>' +
            '<li><a href="#" data-change="deleterow">{{get_string "deleterow" component}}</a></li>' +
            '<li><a href="#" data-change="deletecolumn">{{get_string "deletecolumn" component}}</a></li>' +
        '</ul>';

    CSS = {
    };

Y.namespace('M.atto_table').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    /**
     * The contextual menu that we can open.
     *
     * @property _contextMenu
     * @type M.editor_atto.Menu
     * @private
     */
    _contextMenu: null,

    /**
     * The last modified target.
     *
     * @property _lastTarget
     * @type Node
     * @private
     */
    _lastTarget: null,

    initializer: function() {
        this.addButton({
            icon: 'e/table',
            callback: this._displayTableEditor,
            tags: 'table'
        });

        // Disable mozilla table controls.
        if (Y.UA.gecko) {
            document.execCommand("enableInlineTableEditing", false, false);
            document.execCommand("enableObjectResizing", false, false);
        }
    },

    /**
     * Display the table tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current cursor position.
        this._currentSelection = this.get('host').getSelection();

        if (this._currentSelection !== false && (!this._currentSelection.collapsed)) {
            var dialogue = this.getDialogue({
                headerContent: M.util.get_string('createtable', COMPONENT),
                focusAfterHide: true
            });

            // Set the dialogue content, and then show the dialogue.
            dialogue.set('bodyContent', this._getDialogueContent())
                    .show();
        }
    },

    /**
     * Display the appropriate table editor.
     *
     * If the current selection includes a table, then we show the
     * contextual menu, otherwise show the table creation dialogue.
     *
     * @method _displayTableEditor
     * @param {EventFacade} e
     * @private
     */
    _displayTableEditor: function(e) {
        var selection = this.get('host').getSelectionParentNode(),
            cell;

        if (!selection) {
            // We don't have a current selection at all, so show the standard dialogue.
            return this._displayDialogue(e);
        }

        // Check all of the table cells found in the selection.
        Y.one(selection).ancestors('th, td', true).each(function(node) {
            if (this.editor.contains(node)) {
                cell = node;
            }
        }, this);

        if (cell) {
            // Add the cell to the EventFacade to save duplication in when showing the menu.
            e.tableCell = cell;
            return this._showTableMenu(e);
        }

        return this._displayDialogue(e);
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @private
     * @return {Node} The content to place in the dialogue.
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATE);
        this._content = Y.Node.create(template({
                CSS: CSS,
                elementid: this.get('host').get('elementid'),
                component: COMPONENT
            }));

        // Handle table setting.
        this._content.one('.submit').on('click', this._setTable, this);

        return this._content;
    },

    /**
     * Handle creation of a new table.
     *
     * @method _setTable
     * @param {EventFacade} e
     * @private
     */
    _setTable: function(e) {
        var caption,
            rows,
            cols,
            headers,
            tablehtml,
            i, j;

        e.preventDefault();

        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        caption = e.currentTarget.ancestor('.atto_form').one('.caption');
        rows = e.currentTarget.ancestor('.atto_form').one('.rows');
        cols = e.currentTarget.ancestor('.atto_form').one('.columns');
        headers = e.currentTarget.ancestor('.atto_form').one('.headers');

        // Set the selection.
        this.get('host').setSelection(this._currentSelection);

        // Note there are some spaces inserted in the cells and before and after, so that users have somewhere to click.
        var nl = "\n";
        tablehtml = '<br/>' + nl + '<table>' + nl;
        tablehtml += '<caption>' + Y.Escape.html(caption.get('value')) + '</caption>' + nl;

        i = 0;
        if (headers.get('value') === 'columns' || headers.get('value') === 'both') {
            i = 1;
            tablehtml += '<thead>' + nl + '<tr>' + nl;
            for (j = 0; j < parseInt(cols.get('value'), 10); j++) {
                tablehtml += '<th scope="col"></th>' + nl;
            }
            tablehtml += '</tr>' + nl + '</thead>' + nl;
        }
        tablehtml += '<tbody>' + nl;
        for (; i < parseInt(rows.get('value'), 10); i++) {
            tablehtml += '<tr>' + nl;
            for (j = 0; j < parseInt(cols.get('value'), 10); j++) {
                if (j === 0 && (headers.get('value') === 'rows' || headers.get('value') === 'both')) {
                    tablehtml += '<th scope="row"></th>' + nl;
                } else {
                    tablehtml += '<td></td>' + nl;
                }
            }
            tablehtml += '</tr>' + nl;
        }
        tablehtml += '</tbody>' + nl;
        tablehtml += '</table>' + nl + '<br/>';

        this.get('host').insertContentAtFocusPoint(tablehtml);

        // Mark the content as updated.
        this.markUpdated();
    },

    /**
     * Display the table menu.
     *
     * @method _showTableMenu
     * @param {EventFacade} e
     * @private
     */
    _showTableMenu: function(e) {
        e.preventDefault();

        var boundingBox;

        if (!this._contextMenu) {
            var template = Y.Handlebars.compile(CONTEXTMENUTEMPLATE),
                content = Y.Node.create(template({
                    elementid: this.get('host').get('elementid'),
                    component: COMPONENT
                }));

            this._contextMenu = new Y.M.editor_atto.Menu({
                headerText: M.util.get_string('edittable', 'atto_table'),
                bodyContent: content
            });

            // Add event handlers for table control menus.
            boundingBox = this._contextMenu.get('boundingBox');
            boundingBox.delegate('click', this._handleTableChange, 'a', this);
            boundingBox.delegate('key', this._handleTableChange, 'down:enter,space', 'a', this);
        }
        boundingBox = this._contextMenu.get('boundingBox');

        // We store the cell of the last click (the control node is transient).
        this._lastTarget = e.tableCell.ancestor('.editor_atto_content td, .editor_atto_content th', true);

        // Show the context menu, and align to the current position.
        this._contextMenu.show();
        this._contextMenu.align(e.tableCell, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);

        // If there are any anchors in the bounding box, focus on the first.
        if (boundingBox.one('a')) {
            boundingBox.one('a').focus();
        }
    },

    /**
     * Handle a selection from the table control menu.
     *
     * @method _handleTableChange
     * @param {EventFacade} e
     * @private
     */
    _handleTableChange: function(e) {
        e.preventDefault();

        // Hide the context menu.
        this._contextMenu.hide();

        // Make our changes.
        switch (e.target.getData('change')) {
            case 'addcolumnafter':
                this._addColumnAfter();
                break;
            case 'addrowafter':
                this._addRowAfter();
                break;
            case 'deleterow':
                this._deleteRow();
                break;
            case 'deletecolumn':
                this._deleteColumn();
                break;
            case 'moverowdown':
                this._moveRowDown();
                break;
            case 'moverowup':
                this._moveRowUp();
                break;
            case 'movecolumnleft':
                this._moveColumnLeft();
                break;
            case 'movecolumnright':
                this._moveColumnRight();
                break;
        }
    },

    /**
     * Determine the index of a row in a table column.
     *
     * @method _getRowIndex
     * @param {Node} cell
     * @private
     */
    _getRowIndex: function(cell) {
        var tablenode = cell.ancestor('table'),
            rownode = cell.ancestor('tr');

        if (!tablenode || !rownode) {
            return;
        }

        var rows = tablenode.all('tr');

        return rows.indexOf(rownode);
    },

    /**
     * Determine the index of a column in a table row.
     *
     * @method _getColumnIndex
     * @param {Node} cellnode
     * @private
     */
    _getColumnIndex: function(cellnode) {
        var rownode = cellnode.ancestor('tr');

        if (!rownode) {
            return;
        }

        var cells = rownode.all('td, th');

        return cells.indexOf(cellnode);
    },

    /**
     * Delete the current row.
     *
     * @method _deleteRow
     * @private
     */
    _deleteRow: function() {
        var row = this._lastTarget.ancestor('tr');

        if (row) {
            // We do not remove rows with no cells (all headers).
            if (row.one('td')) {
                row.remove(true);
            }
        }

        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Move row up
     *
     * @method _moveRowUp
     * @private
     */
    _moveRowUp: function() {
        var row = this._lastTarget.ancestor('tr');
        var prevrow = row.previous('tr');
        if (!row || !prevrow) {
            return;
        }

        row.swap(prevrow);
        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Move column left
     *
     * @method _moveColumnLeft
     * @private
     */
    _moveColumnLeft: function() {
        var columnindex = this._getColumnIndex(this._lastTarget);
        var rows = this._lastTarget.ancestor('table').all('tr');
        var columncells = new Y.NodeList();
        var prevcells = new Y.NodeList();
        var hastd = false;

        rows.each(function(row) {
            var cells = row.all('td, th');
            var cell = cells.item(columnindex),
                cellprev = cells.item(columnindex-1);
            columncells.push(cell);
            if (cellprev) {
                if (cellprev.get('tagName') === 'TD') {
                    hastd = true;
                }
                prevcells.push(cellprev);
            }
        });

        if (hastd && prevcells.size() > 0) {
            var i = 0;
            for (i = 0; i < columncells.size(); i++) {
                var cell = columncells.item(i);
                var prevcell = prevcells.item(i);

                cell.swap(prevcell);
            }
        }
        // Cleanup.
        this.markUpdated();
    },

    /**
     * Move column right.
     *
     * @method _moveColumnRight
     * @private
     */
    _moveColumnRight: function() {
        var columnindex = this._getColumnIndex(this._lastTarget);
        var rows = this._lastTarget.ancestor('table').all('tr');
        var columncells = new Y.NodeList();
        var nextcells = new Y.NodeList();
        var hastd = false;

        rows.each(function(row) {
            var cells = row.all('td, th');
            var cell = cells.item(columnindex),
                cellnext = cells.item(columnindex+1);
            if (cell.get('tagName') === 'TD') {
                hastd = true;
            }
            columncells.push(cell);
            if (cellnext) {
                nextcells.push(cellnext);
            }
        });

        if (hastd && nextcells.size() > 0) {
            var i = 0;
            for (i = 0; i < columncells.size(); i++) {
                var cell = columncells.item(i);
                var nextcell = nextcells.item(i);

                cell.swap(nextcell);
            }
        }
        // Cleanup.
        this.markUpdated();
    },

    /**
     * Move row down.
     *
     * @method _moveRowDown
     * @private
     */
    _moveRowDown: function() {
        var row = this._lastTarget.ancestor('tr');
        var nextrow = row.next('tr');
        if (!row || !nextrow) {
            return;
        }

        row.swap(nextrow);
        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Delete the current column.
     *
     * @method _deleteColumn
     * @private
     */
    _deleteColumn: function() {
        var columnindex = this._getColumnIndex(this._lastTarget);
        var rows = this._lastTarget.ancestor('table').all('tr');
        var columncells = new Y.NodeList();
        var hastd = false;

        rows.each(function(row) {
            var cells = row.all('td, th');
            var cell = cells.item(columnindex);
            if (cell.get('tagName') === 'TD') {
                hastd = true;
            }
            columncells.push(cell);
        });

        if (hastd) {
            columncells.remove(true);
        }

        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Add a row after the current row.
     *
     * @method _addRowAfter
     * @private
     */
    _addRowAfter: function() {
        var rowindex = this._getRowIndex(this._lastTarget);

        var tablebody = this._lastTarget.ancestor('table').one('tbody');
        if (!tablebody) {
            // Not all tables have tbody.
            tablebody = this._lastTarget.ancestor('table');
            rowindex += 1;
        }

        var firstrow = tablebody.one('tr');
        if (!firstrow) {
            firstrow = this._lastTarget.ancestor('table').one('tr');
        }
        if (!firstrow) {
            // Table has no rows. Boo.
            return;
        }
        newrow = firstrow.cloneNode(true);
        newrow.all('th, td').each(function (tablecell) {
            if (tablecell.get('tagName') === 'TH') {
                if (tablecell.getAttribute('scope') !== 'row') {
                    var newcell = Y.Node.create('<td></td>');
                    tablecell.replace(newcell);
                    tablecell = newcell;
                }
            }
            tablecell.setHTML('&nbsp;');
        });

        tablebody.insert(newrow, rowindex);

        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Add a column after the current column.
     *
     * @method _addColumnAfter
     * @private
     */
    _addColumnAfter: function() {
        var columnindex = this._getColumnIndex(this._lastTarget);

        var tablecell = this._lastTarget.ancestor('table');
        var rows = tablecell.all('tr');
        Y.each(rows, function(row) {
            // Clone the first cell from the row so it has the same type/attributes (e.g. scope).
            var newcell = row.one('td, th').cloneNode(true);
            // Clear the content of the cell.
            newcell.setHTML('&nbsp;');

            row.insert(newcell, columnindex + 1);
        }, this);

        // Clean the HTML.
        this.markUpdated();
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "moodle-editor_atto-menu", "event", "event-valuechange"]});
