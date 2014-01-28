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
 * Atto text editor table plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_table = M.atto_table || {

    /**
     * The window used to get the table details.
     *
     * @property dialogue
     * @type M.core.dialogue
     * @default null
     */
    dialogue : null,

    /**
     * The selection object returned by the browser.
     *
     * @property selection
     * @type Range
     * @default null
     */
    selection : null,

    /**
     * Yui image for table editing controls.
     *
     * @property menunode
     * @type Y.Node
     * @default null
     */
    menunode : null,

    /**
     * Popup menu for table controls
     *
     * @property controlmenu
     * @type M.editor_atto.controlmenu
     * @default null
     */
    controlmenu : null,

    /**
     * Last clicked cell that opened the context menu.
     *
     * @property lasttarget
     * @type Y.Node
     * @default null
     */
    lasttarget : null,

    /**
     * Display the chooser dialogue.
     *
     * @method init
     * @param Event e
     * @param string elementid
     */
    display_chooser : function(e, elementid) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        M.atto_table.selection = M.editor_atto.get_selection();
        if (M.atto_table.selection !== false && (!M.atto_table.selection.collapsed)) {
            var dialogue;
            if (!M.atto_table.dialogue) {
                dialogue = new M.core.dialogue({
                    visible: false,
                    modal: true,
                    close: true,
                    draggable: true
                });
            } else {
                dialogue = M.atto_table.dialogue;
            }

            dialogue.render();
            dialogue.set('bodyContent', M.atto_table.get_form_content(elementid));
            dialogue.set('headerContent', M.util.get_string('createtable', 'atto_table'));

            dialogue.show();
            M.atto_table.dialogue = dialogue;
        }

    },

    /**
     * Show the context menu
     *
     * @method show_menu
     * @param Event e
     * @param string elementid
     */
    show_menu : function(e, elementid) {
        var addhandlers = false;

        e.preventDefault();

        if (this.controlmenu === null) {
            addhandlers = true;
            // Add event handlers for table control menus.
            var bodycontent = '<ul>';
            bodycontent += '<li><a href="#" id="addcolumnafter">' + M.util.get_string('addcolumnafter', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="addrowafter">' + M.util.get_string('addrowafter', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="moverowup">' + M.util.get_string('moverowup', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="moverowdown">' + M.util.get_string('moverowdown', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="movecolumnleft">' + M.util.get_string('movecolumnleft', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="movecolumnright">' + M.util.get_string('movecolumnright', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="deleterow">' + M.util.get_string('deleterow', 'atto_table') + '</a></li>';
            bodycontent += '<li><a href="#" id="deletecolumn">' + M.util.get_string('deletecolumn', 'atto_table') + '</a></li>';
            bodycontent += '</ul>';

            this.controlmenu = new M.editor_atto.controlmenu({
                headerText : M.util.get_string('edittable', 'atto_table'),
                bodyContent : bodycontent
            });
        }
        // We store the cell of the last click (the control node is transient).
        this.lasttarget = e.target.ancestor('td, th');
        this.controlmenu.show();
        this.controlmenu.align(e.target, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);

        if (addhandlers) {
            var bodynode = this.controlmenu.get('boundingBox');
            bodynode.delegate('click', this.handle_table_control, 'a', this, elementid);
            bodynode.delegate('key', this.handle_table_control, 'down:enter,space', 'a', this, elementid);
        }
    },

    /**
     * Determine the index of a row in a table column.
     *
     * @method get_row_index
     * @param Y.Node node
     */
    get_row_index : function(cell) {
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
     * @method get_column_index
     * @param Y.Node node
     */
    get_column_index : function(cellnode) {
        var rownode = cellnode.ancestor('tr');

        if (!rownode) {
            return;
        }

        var cells = rownode.all('td, th');

        return cells.indexOf(cellnode);
    },

    /**
     * Delete the current row
     *
     * @method delete_row
     * @param string elementid
     */
    delete_row : function(elementid) {
        var row = this.lasttarget.ancestor('tr');

        if (row) {
            // We do not remove rows with no cells (all headers).
            if (row.one('td')) {
                row.remove(true);
            }
        }

        // Clean the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Move row up
     *
     * @method move_row_up
     * @param string elementid
     */
    move_row_up : function(elementid) {
        var row = this.lasttarget.ancestor('tr');
        var prevrow = row.previous('tr');
        if (!row || !prevrow) {
            return;
        }

        row.swap(prevrow);
        // Clean the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Move column left
     *
     * @method move_column_left
     * @param string elementid
     */
    move_column_left : function(elementid) {
        var columnindex = this.get_column_index(this.lasttarget);
        var rows = this.lasttarget.ancestor('table').all('tr');
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
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Move column right
     *
     * @method move_column_right
     * @param string elementid
     */
    move_column_right : function(elementid) {
        var columnindex = this.get_column_index(this.lasttarget);
        var rows = this.lasttarget.ancestor('table').all('tr');
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
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Move row down
     *
     * @method move_row_down
     * @param string elementid
     */
    move_row_down : function(elementid) {
        var row = this.lasttarget.ancestor('tr');
        var nextrow = row.next('tr');
        if (!row || !nextrow) {
            return;
        }

        row.swap(nextrow);
        // Clean the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Delete the current column
     *
     * @method delete_column
     * @param string elementid
     */
    delete_column : function(elementid) {
        var columnindex = this.get_column_index(this.lasttarget);
        var rows = this.lasttarget.ancestor('table').all('tr');
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
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Add a row after the current row.
     *
     * @method add_row_after
     * @param string elementid
     */
    add_row_after : function(elementid) {
        var rowindex = this.get_row_index(this.lasttarget);

        var tablebody = this.lasttarget.ancestor('table').one('tbody');
        if (!tablebody) {
            // Not all tables have tbody.
            tablebody = this.lasttarget.ancestor('table');
            rowindex += 1;
        }

        var firstrow = tablebody.one('tr');
        if (!firstrow) {
            firstrow = this.lasttarget.ancestor('table').one('tr');
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
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Add a column after the current column.
     *
     * @method add_column_after
     * @param string elementid
     */
    add_column_after : function(elementid) {
        var columnindex = this.get_column_index(this.lasttarget);

        var tablecell = this.lasttarget.ancestor('table');
        var rows = tablecell.all('tr');
        Y.each(rows, function(row) {
            // Clone the first cell from the row so it has the same type/attributes (e.g. scope).
            var newcell = row.one('td, th').cloneNode(true);
            // Clear the content of the cell.
            newcell.setHTML('&nbsp;');

            row.insert(newcell, columnindex + 1);
        }, this);

        // Clean the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Handle a selection from the table control menu.
     *
     * @method handle_table_control
     * @param Y.Event event
     * @param string elementid
     */
    handle_table_control : function(event, elementid) {
        event.preventDefault();

        this.controlmenu.hide();

        switch (event.target.get('id')) {
            case 'addcolumnafter':
                this.add_column_after(elementid);
                break;
            case 'addrowafter':
                this.add_row_after(elementid);
                break;
            case 'deleterow':
                this.delete_row(elementid);
                break;
            case 'deletecolumn':
                this.delete_column(elementid);
                break;
            case 'moverowdown':
                this.move_row_down(elementid);
                break;
            case 'moverowup':
                this.move_row_up(elementid);
                break;
            case 'movecolumnleft':
                this.move_column_left(elementid);
                break;
            case 'movecolumnright':
                this.move_column_right(elementid);
                break;
        }
    },

    /**
     * Add this button to the form.
     *
     * @method init
     * @param {Object} params
     */
    init : function(params) {

        if (!M.atto_table.menunode) {
            // Used for inline table editing controls.
            var img = Y.Node.create('<img/>');
            img.setAttrs({
                alt : M.util.get_string('edittable', 'atto_table'),
                src : M.util.image_url('t/contextmenu', 'core'),
                width : '12',
                height : '12'
            });
            var anchor = Y.Node.create('<a href="#" contenteditable="false"/>');
            anchor.appendChild(img);
            anchor.addClass('atto_control');
            M.atto_table.menunode = anchor;
        }

        M.editor_atto.add_toolbar_button(params.elementid, 'table', params.icon, params.group, this.display_chooser, this);
        //Y.one('#' + params.elementid + 'editable').on('valuechange', this.insert_table_controls, this, params.elementid);

        var contenteditable = Y.one('#' + params.elementid + 'editable');
        contenteditable.delegate('click', this.show_menu, 'td > .atto_control, th > .atto_control', this, params.elementid);
        contenteditable.delegate('key', this.show_menu, 'down:enter,space', 'td > .atto_control, th > .atto_control', this, params.elementid);
        // Disable mozilla table controls.
        if (Y.UA.gecko) {
            document.execCommand("enableInlineTableEditing", false, "false");
            document.execCommand("enableObjectResizing", false, false);
        }

        this.insert_table_controls(params.elementid);

        // Re-add the table controls whenever the content is updated.
        M.editor_atto.add_text_updated_handler(params.elementid, this.insert_table_controls);
    },

    /**
     * Add the table editing controls to the content area.
     *
     * @method insert_table_controls
     * @param String elementid - The id of the text area backed by the content editable field.
     */
    insert_table_controls : function(elementid) {
        var contenteditable = Y.one('#' + elementid + 'editable'),
            allcells = contenteditable.all('td .atto_control,th .atto_control'),
            cells = contenteditable.all('td:last-child,th:last-child,tbody tr:last-child > td, tbody tr:last-child > th');

        allcells.each(function(node) {
            if (cells.indexOf(node) === -1) {
               node.remove(true);
            }
        });

        cells.each(function(node) {
            if (!node.one('.atto_control')) {
                node.append(M.atto_table.menunode.cloneNode(true));
            }
        }, this);
    },

    /**
     * The OK button has been pressed - make the changes to the source.
     *
     * @method set_table
     * @param Event e
     */
    set_table : function(e, elementid) {
        var caption,
            rows,
            cols,
            headers,
            tablehtml,
            i, j;

        e.preventDefault();
        M.atto_table.dialogue.hide();

        caption = e.currentTarget.ancestor('.atto_form').one('#atto_table_caption');
        rows = e.currentTarget.ancestor('.atto_form').one('#atto_table_rows');
        cols = e.currentTarget.ancestor('.atto_form').one('#atto_table_columns');
        headers = e.currentTarget.ancestor('.atto_form').one('#atto_table_headers');

        M.editor_atto.set_selection(M.atto_table.selection);

        // Note there are some spaces inserted in the cells and before and after, so that users have somewhere to click.
        tablehtml = '<br/><table>';
        tablehtml += '<caption>' + Y.Escape.html(caption.get('value')) + '</caption>';

        i = 0;
        if (headers.get('value') === 'columns' || headers.get('value') === 'both') {
            i = 1;
            tablehtml += '<thead><tr>';
            for (j = 0; j < parseInt(cols.get('value'), 10); j++) {
                tablehtml += '<th scope="col"></th>';
            }
            tablehtml += '</tr></thead>';
        }
        tablehtml += '<tbody>';
        for (; i < parseInt(rows.get('value'), 10); i++) {
            tablehtml += '<tr>';
            for (j = 0; j < parseInt(cols.get('value'), 10); j++) {
                if (j === 0 && (headers.get('value') === 'rows' || headers.get('value') === 'both')) {
                    tablehtml += '<th scope="row"></th>';
                } else {
                    tablehtml += '<td></td>';
                }
            }
            tablehtml += '</tr>';
        }
        tablehtml += '</tbody>';
        tablehtml += '</table><br/>';

        document.execCommand('insertHTML', false, tablehtml);

        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Return the HTML of the form to show in the dialogue.
     *
     * @method get_form_content
     * @param string elementid
     * @return string
     */
    get_form_content : function(elementid) {
        var content = Y.Node.create('<form class="atto_form">' +
                             '<label for="atto_table_caption">' + M.util.get_string('caption', 'atto_table') +
                             '</label>' +
                             '<textarea id="atto_table_caption" rows="4" class="fullwidth" required></textarea>' +
                             '<br/>' +
                             '<label for="atto_table_headers" class="sameline">' + M.util.get_string('headers', 'atto_table') +
                             '</label>' +
                             '<select id="atto_table_headers">' +
                             '<option value="columns">' + M.util.get_string('columns', 'atto_table') + '</option>' +
                             '<option value="rows">' + M.util.get_string('rows', 'atto_table') + '</option>' +
                             '<option value="both">' + M.util.get_string('both', 'atto_table') + '</option>' +
                             '</select>' +
                             '<br/>' +
                             '<label for="atto_table_rows" class="sameline">' + M.util.get_string('numberofrows', 'atto_table') +
                             '</label>' +
                             '<input type="number" value="3" id="atto_table_rows" size="8" min="1" max="50"/>' +
                             '<br/>' +
                             '<label for="atto_table_columns" class="sameline">' + M.util.get_string('numberofcolumns', 'atto_table') +
                             '</label>' +
                             '<input type="number" value="3" id="atto_table_columns" size="8" min="1" max="20"/>' +
                             '<br/>' +
                             '<div class="mdl-align">' +
                             '<br/>' +
                             '<button id="atto_table_submit">' +
                             M.util.get_string('createtable', 'atto_table') +
                             '</button>' +
                             '</div>' +
                             '</form>' +
                             '<hr/>' + M.util.get_string('accessibilityhint', 'atto_table'));

        content.one('#atto_table_submit').on('click', M.atto_table.set_table, this, elementid);
        return content;
    }
};


}, '@VERSION@', {"requires": ["node", "escape", "event", "event-valuechange"]});
