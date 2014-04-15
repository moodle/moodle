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
    EDITTEMPLATE = '' +
        '<form class="{{CSS.FORM}}">' +
            '<label for="{{elementid}}_atto_table_caption">{{get_string "caption" component}}</label>' +
            '<input class="{{CSS.CAPTION}} fullwidth" id="{{elementid}}_atto_table_caption" required />' +
            '<br/>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_headers" class="sameline">{{get_string "headers" component}}</label>' +
            '<select class="{{CSS.HEADERS}}" id="{{elementid}}_atto_table_headers">' +
                '<option value="columns">{{get_string "columns" component}}' + '</option>' +
                '<option value="rows">{{get_string "rows" component}}' + '</option>' +
                '<option value="both">{{get_string "both" component}}' + '</option>' +
            '</select>' +
            '<br/>' +
            '<div class="mdl-align">' +
                '<br/>' +
                '<button class="submit" type="submit">{{get_string "updatetable" component}}</button>' +
            '</div>' +
        '</form>',
    TEMPLATE = '' +
        '<form class="{{CSS.FORM}}">' +
            '<label for="{{elementid}}_atto_table_caption">{{get_string "caption" component}}</label>' +
            '<input class="{{CSS.CAPTION}} fullwidth" id="{{elementid}}_atto_table_caption" required />' +
            '<br/>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_headers" class="sameline">{{get_string "headers" component}}</label>' +
            '<select class="{{CSS.HEADERS}}" id="{{elementid}}_atto_table_headers">' +
                '<option value="columns">{{get_string "columns" component}}' + '</option>' +
                '<option value="rows">{{get_string "rows" component}}' + '</option>' +
                '<option value="both">{{get_string "both" component}}' + '</option>' +
            '</select>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_rows" class="sameline">{{get_string "numberofrows" component}}</label>' +
            '<input class="{{CSS.ROWS}}" type="number" value="3" id="{{elementid}}_atto_table_rows" size="8" min="1" max="50"/>' +
            '<br/>' +
            '<label for="{{elementid}}_atto_table_columns" class="sameline">{{get_string "numberofcolumns" component}}</label>' +
            '<input class="{{CSS.COLUMNS}}" type="number" value="3" id="{{elementid}}_atto_table_columns" size="8" min="1" max="20"/>' +
            '<br/>' +
            '<div class="mdl-align">' +
                '<br/>' +
                '<button class="{{CSS.SUBMIT}}" type="submit">{{get_string "createtable" component}}</button>' +
            '</div>' +
        '</form>',
    CSS = {
        CAPTION: 'caption',
        HEADERS: 'headers',
        ROWS: 'rows',
        COLUMNS: 'columns',
        SUBMIT: 'submit',
        FORM: 'atto_form'
    },
    SELECTORS = {
        CAPTION: '.' + CSS.CAPTION,
        HEADERS: '.' + CSS.HEADERS,
        ROWS: '.' + CSS.ROWS,
        COLUMNS: '.' + CSS.COLUMNS,
        SUBMIT: '.' + CSS.SUBMIT,
        FORM: '.atto_form'
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

    /**
     * The list of menu items.
     *
     * @property _menuOptions
     * @type Object
     * @private
     */
    _menuOptions: null,

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
        var cell = this._getSuitableTableCell();
        if (cell) {
            // Add the cell to the EventFacade to save duplication in when showing the menu.
            e.tableCell = cell;
            return this._showTableMenu(e);
        }
        return this._displayDialogue(e);
    },

    /**
     * Returns whether or not the parameter node exists within the editor.
     *
     * @method _stopAtContentEditableFilter
     * @param  {Node} node
     * @private
     * @return {boolean} whether or not the parameter node exists within the editor.
     */
    _stopAtContentEditableFilter: function(node) {
        this.editor.contains(node);
    },

    /**
     * Return the edit table dialogue content, attaching any required
     * events.
     *
     * @method _getEditDialogueContent
     * @private
     * @return {Node} The content to place in the dialogue.
     */
    _getEditDialogueContent: function() {
        var template = Y.Handlebars.compile(EDITTEMPLATE);
        this._content = Y.Node.create(template({
                CSS: CSS,
                elementid: this.get('host').get('elementid'),
                component: COMPONENT
            }));

        // Handle table setting.
        this._content.one('.submit').on('click', this._updateTable, this);

        return this._content;
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
     * Given the current selection, return a table cell suitable for table editing
     * purposes, i.e. the first table cell selected, or the first cell in the table
     * that the selection exists in, or null if not within a table.
     *
     * @method _getSuitableTableCell
     * @private
     * @return {Node} suitable target cell, or null if not within a table
     */
    _getSuitableTableCell: function() {
        var targetcell = null,
            host = this.get('host');

        host.getSelectedNodes().some(function (node) {
            if (node.ancestor('td, th, caption', true, this._stopAtContentEditableFilter)) {
                targetcell = node;

                var caption = node.ancestor('caption', true, this._stopAtContentEditableFilter);
                if (caption) {
                    var table = caption.get('parentNode');
                    if (table) {
                        targetcell = table.one('td, th');
                    }
                }

                // Once we've found a cell to target, we shouldn't need to keep looking.
                return true;
            }
        });

        if (targetcell) {
            var selection = host.getSelectionFromNode(targetcell);
            host.setSelection(selection);
        }

        return targetcell;
    },

    /**
     * Change a node from one type to another, copying all attributes and children.
     *
     * @method _changeNodeType
     * @param {Y.Node} node
     * @param {String} new node type
     * @private
     * @chainable
     */
    _changeNodeType: function(node, newType) {
        var newNode = Y.Node.create('<' + newType + '></' + newType + '>');
        newNode.setAttrs(node.getAttrs());
        node.get('childNodes').each(function(child) {
            newNode.append(child.remove());
        });
        node.replace(newNode);
        return newNode;
    },

    /**
     * Handle updating an existing table.
     *
     * @method _updateTable
     * @param {EventFacade} e
     * @private
     */
    _updateTable: function(e) {
        var caption,
            headers,
            table,
            captionnode;

        e.preventDefault();
        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        // Add/update the caption.
        caption = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTION);
        headers = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.HEADERS);

        table = this._lastTarget.ancestor('table');

        captionnode = table.one('caption');
        if (!captionnode) {
            captionnode = Y.Node.create('<caption></caption');
            table.insert(captionnode, 0);
        }
        captionnode.setHTML(caption.get('value'));

        // Add the row headers.
        if (headers.get('value') === 'rows' || headers.get('value') === 'both') {
            table.all('tr').each(function (row) {
                var cells = row.all('th, td'),
                    firstCell = cells.shift(),
                    newCell;

                if (firstCell.get('tagName') === 'TD') {
                    // Cell is a td but should be a th - change it.
                    newCell = this._changeNodeType(firstCell, 'th');
                    newCell.setAttribute('scope', 'row');
                } else {
                    firstCell.setAttribute('scope', 'row');
                }

                // Now make sure all other cells in the row are td.
                cells.each(function (cell) {
                    if (cell.get('tagName') === 'TH') {
                        newCell = this._changeNodeType(cell, 'td');
                        newCell.removeAttribute('scope');
                    }
                }, this);

            }, this);
        }
        // Add the col headers. These may overrule the row headers in the first cell.
        if (headers.get('value') === 'columns' || headers.get('value') === 'both') {
            var rows = table.all('tr'),
                firstRow = rows.shift(),
                newCell;

            firstRow.all('td, th').each(function (cell) {
                if (cell.get('tagName') === 'TD') {
                    // Cell is a td but should be a th - change it.
                    newCell = this._changeNodeType(cell, 'th');
                    newCell.setAttribute('scope', 'col');
                } else {
                    cell.setAttribute('scope', 'col');
                }
            }, this);
            // Change all the cells in the rest of the table to tds (unless they are row headers).
            rows.each(function(row) {
                var cells = row.all('th, td');

                if (headers.get('value') === 'both') {
                    // Ignore the first cell because it's a row header.
                    cells.shift();
                }
                cells.each(function(cell) {
                    if (cell.get('tagName') === 'TH') {
                        newCell = this._changeNodeType(cell, 'td');
                        newCell.removeAttribute('scope');
                    }
                }, this);

            }, this);
        }
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

        caption = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTION);
        rows = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.ROWS);
        cols = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.COLUMNS);
        headers = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.HEADERS);

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
     * Search for all the cells in the current, next and previous columns.
     *
     * @method _findColumnCells
     * @private
     * @return {Object} containing current, prev and next {Y.NodeList}s
     */
    _findColumnCells: function() {
        var columnindex = this._getColumnIndex(this._lastTarget),
            rows = this._lastTarget.ancestor('table').all('tr'),
            currentcells = new Y.NodeList(),
            prevcells = new Y.NodeList(),
            nextcells = new Y.NodeList();

        rows.each(function(row) {
            var cells = row.all('td, th'),
                cell = cells.item(columnindex),
                cellprev = cells.item(columnindex-1),
                cellnext = cells.item(columnindex+1);
            currentcells.push(cell);
            if (cellprev) {
                prevcells.push(cellprev);
            }
            if (cellnext) {
                nextcells.push(cellnext);
            }
        });

        return {
            current: currentcells,
            prev: prevcells,
            next: nextcells
        };
    },

    /**
     * Hide the entries in the context menu that don't make sense with the
     * current selection.
     *
     * @method _hideInvalidEntries
     * @param {Y.Node} node - The node containing the menu.
     * @private
     */
    _hideInvalidEntries: function(node) {
        // Moving rows.
        var table = this._lastTarget.ancestor('table'),
            row = this._lastTarget.ancestor('tr'),
            rows = table.all('tr'),
            rowindex = rows.indexOf(row),
            prevrow = rows.item(rowindex - 1),
            prevrowhascells = prevrow ? prevrow.one('td') : null;

        if (!row || !prevrowhascells) {
            node.one('[data-change="moverowup"]').hide();
        } else {
            node.one('[data-change="moverowup"]').show();
        }

        var nextrow = rows.item(rowindex + 1),
            rowhascell = row ? row.one('td') : false;

        if (!row || !nextrow || !rowhascell) {
            node.one('[data-change="moverowdown"]').hide();
        } else {
            node.one('[data-change="moverowdown"]').show();
        }

        // Moving columns.
        var cells = this._findColumnCells();
        if (cells.prev.filter('td').size() > 0) {
            node.one('[data-change="movecolumnleft"]').show();
        } else {
            node.one('[data-change="movecolumnleft"]').hide();
        }

        var colhascell = cells.current.filter('td').size() > 0;
        if ((cells.next.size() > 0) && colhascell) {
            node.one('[data-change="movecolumnright"]').show();
        } else {
            node.one('[data-change="movecolumnright"]').hide();
        }

        // Delete col
        if (cells.current.filter('td').size() > 0) {
            node.one('[data-change="deletecolumn"]').show();
        } else {
            node.one('[data-change="deletecolumn"]').hide();
        }
        // Delete row
        if (!row || !row.one('td')) {
            node.one('[data-change="deleterow"]').hide();
        } else {
            node.one('[data-change="deleterow"]').show();
        }
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
            this._menuOptions = [
                {
                    text: M.util.get_string("addcolumnafter", COMPONENT),
                    data: {
                        change: "addcolumnafter"
                    }
                }, {
                    text: M.util.get_string("addrowafter", COMPONENT),
                    data: {
                        change: "addrowafter"
                    }
                }, {
                    text: M.util.get_string("moverowup", COMPONENT),
                    data: {
                        change: "moverowup"
                    }
                }, {
                    text: M.util.get_string("moverowdown", COMPONENT),
                    data: {
                        change: "moverowdown"
                    }
                }, {
                    text: M.util.get_string("movecolumnleft", COMPONENT),
                    data: {
                        change: "movecolumnleft"
                    }
                }, {
                    text: M.util.get_string("movecolumnright", COMPONENT),
                    data: {
                        change: "movecolumnright"
                    }
                }, {
                    text: M.util.get_string("deleterow", COMPONENT),
                    data: {
                        change: "deleterow"
                    }
                }, {
                    text: M.util.get_string("deletecolumn", COMPONENT),
                    data: {
                        change: "deletecolumn"
                    }
                }, {
                    text: M.util.get_string("edittable", COMPONENT),
                    data: {
                        change: "edittable"
                    }
                }
            ];

            this._contextMenu = new Y.M.editor_atto.Menu({
                items: this._menuOptions
            });

            // Add event handlers for table control menus.
            boundingBox = this._contextMenu.get('boundingBox');
            boundingBox.delegate('click', this._handleTableChange, 'a', this);
        }

        boundingBox = this._contextMenu.get('boundingBox');

        // We store the cell of the last click (the control node is transient).
        this._lastTarget = e.tableCell.ancestor('.editor_atto_content td, .editor_atto_content th', true);

        this._hideInvalidEntries(boundingBox);

        // Clear the focusAfterHide for any other menus which may be open.
        Y.Array.each(this.get('host').openMenus, function(menu) {
            menu.set('focusAfterHide', null);
        });

        // Ensure that we focus on the button in the toolbar when we tab back to the menu.
        var creatorButton = this.buttons[this.name];
        this.get('host')._setTabFocus(creatorButton);

        // Show the context menu, and align to the current position.
        this._contextMenu.show();
        this._contextMenu.align(this.buttons.table, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);
        this._contextMenu.set('focusAfterHide', creatorButton);

        // If there are any anchors in the bounding box, focus on the first.
        if (boundingBox.one('a')) {
            boundingBox.one('a').focus();
        }

        // Add this menu to the list of open menus.
        this.get('host').openMenus = [this._contextMenu];
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

        this._contextMenu.set('focusAfterHide', this.get('host').editor);
        // Hide the context menu.
        this._contextMenu.hide(e);

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
            case 'edittable':
                this._editTable();
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

        if (row && row.one('td')) {
            // Only delete rows with at least one non-header cell.
            row.remove(true);
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
        var row = this._lastTarget.ancestor('tr'),
            prevrow = row.previous('tr');
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
        var cells = this._findColumnCells();

        if (cells.current.size() > 0 && cells.prev.size() > 0 && cells.current.size() === cells.prev.size()) {
            var i = 0;
            for (i = 0; i < cells.current.size(); i++) {
                var cell = cells.current.item(i),
                    prevcell = cells.prev.item(i);

                cell.swap(prevcell);
            }
        }
        // Cleanup.
        this.markUpdated();
    },

    /**
     * Add a caption to the table if it doesn't have one.
     *
     * @method _addCaption
     * @private
     */
    _addCaption: function() {
        var table = this._lastTarget.ancestor('table'),
            caption = table.one('caption');

        if (!caption) {
            table.insert(Y.Node.create('<caption>&nbsp;</caption>'), 1);
        }
    },

    /**
     * Remove a caption from the table if has one.
     *
     * @method _removeCaption
     * @private
     */
    _removeCaption: function() {
        var table = this._lastTarget.ancestor('table'),
            caption = table.one('caption');

        if (caption) {
            caption.remove(true);
        }
    },

    /**
     * Move column right.
     *
     * @method _moveColumnRight
     * @private
     */
    _moveColumnRight: function() {
        var cells = this._findColumnCells();

        // Check we have some tds in this column, and one exists to the right.
        if ( (cells.next.size() > 0) &&
                (cells.current.size() === cells.next.size()) &&
                (cells.current.filter('td').size() > 0)) {
            var i = 0;
            for (i = 0; i < cells.current.size(); i++) {
                var cell = cells.current.item(i),
                    nextcell = cells.next.item(i);

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
        var row = this._lastTarget.ancestor('tr'),
            nextrow = row.next('tr');
        if (!row || !nextrow || !row.one('td')) {
            return;
        }

        row.swap(nextrow);
        // Clean the HTML.
        this.markUpdated();
    },

    /**
     * Edit table (show the dialogue).
     *
     * @method _editTable
     * @private
     */
    _editTable: function() {
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('edittable', COMPONENT),
            focusAfterHide: false
        });

        // Set the dialogue content, and then show the dialogue.
        var node = this._getEditDialogueContent(),
            captioninput = node.one(SELECTORS.CAPTION),
            headersinput = node.one(SELECTORS.HEADERS),
            table = this._lastTarget.ancestor('table'),
            captionnode = table.one('caption');

        if (captionnode) {
            captioninput.set('value', captionnode.getHTML());
        } else {
            captioninput.set('value', '');
        }

        var headersvalue = 'columns';
        if (table.one('th[scope="row"]')) {
            headersvalue = 'rows';
            if (table.one('th[scope="col"]')) {
                headersvalue = 'both';
            }
        }
        headersinput.set('value', headersvalue);
        dialogue.set('bodyContent', node).show();
    },


    /**
     * Delete the current column.
     *
     * @method _deleteColumn
     * @private
     */
    _deleteColumn: function() {
        var columnindex = this._getColumnIndex(this._lastTarget),
            table = this._lastTarget.ancestor('table'),
            rows = table.all('tr'),
            columncells = new Y.NodeList(),
            hastd = false;

        rows.each(function(row) {
            var cells = row.all('td, th');
            var cell = cells.item(columnindex);
            if (cell.get('tagName') === 'TD') {
                hastd = true;
            }
            columncells.push(cell);
        });

        // Do not delete all the headers.
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
        var cells = this._findColumnCells(),
            before = true,
            clonecells = cells.next;
        if (cells.next.size() <= 0) {
            before = false;
            clonecells = cells.current;
        }

        Y.each(clonecells, function(cell) {
            var newcell = cell.cloneNode();
            // Clear the content of the cell.
            newcell.setHTML('&nbsp;');

            if (before) {
                cell.get('parentNode').insert(newcell, cell);
            } else {
                cell.get('parentNode').insert(newcell, cell);
                cell.swap(newcell);
            }
        }, this);

        // Clean the HTML.
        this.markUpdated();
    }

});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "moodle-editor_atto-menu", "event", "event-valuechange"]});
