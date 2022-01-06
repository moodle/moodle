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
    DEFAULT = {
        BORDERSTYLE: 'none',
        BORDERWIDTH: '1'
    },
    DIALOGUE = {
        WIDTH: '480px'
    },
    TEMPLATE = '' +
        '<form class="{{CSS.FORM}}">' +
            '<div class="mb-1 form-group row">' +
            '<div class="col-sm-4">' +
            '<label for="{{elementid}}_atto_table_caption">{{get_string "caption" component}}</label>' +
            '</div><div class="col-sm-8">' +
            '<input type="text" class="form-control {{CSS.CAPTION}}" id="{{elementid}}_atto_table_caption" required />' +
            '</div>' +
            '</div>' +
            '<div class="mb-1 form-group row">' +
            '<div class="col-sm-4">' +
            '<label for="{{elementid}}_atto_table_captionposition">' +
            '{{get_string "captionposition" component}}</label>' +
            '</div><div class="col-sm-8">' +
            '<select class="custom-select {{CSS.CAPTIONPOSITION}}" id="{{elementid}}_atto_table_captionposition">' +
                '<option value=""></option>' +
                '<option value="top">{{get_string "top" "editor"}}</option>' +
                '<option value="bottom">{{get_string "bottom" "editor"}}</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="mb-1 form-group row">' +
            '<div class="col-sm-4">' +
            '<label for="{{elementid}}_atto_table_headers">{{get_string "headers" component}}</label>' +
            '</div><div class="col-sm-8">' +
            '<select class="custom-select {{CSS.HEADERS}}" id="{{elementid}}_atto_table_headers">' +
                '<option value="columns">{{get_string "columns" component}}' + '</option>' +
                '<option value="rows">{{get_string "rows" component}}' + '</option>' +
                '<option value="both">{{get_string "both" component}}' + '</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '{{#if nonedit}}' +
                '<div class="mb-1 form-group row">' +
                '<div class="col-sm-4">' +
                '<label for="{{elementid}}_atto_table_rows">{{get_string "numberofrows" component}}</label>' +
                '</div><div class="col-sm-8">' +
                '<input class="form-control w-auto {{CSS.ROWS}}" type="number" value="3" ' +
                'id="{{elementid}}_atto_table_rows" size="8" min="1" max="50"/>' +
                '</div>' +
                '</div>' +
                '<div class="mb-1 form-group row">' +
                '<div class="col-sm-4">' +
                '<label for="{{elementid}}_atto_table_columns" ' +
                '>{{get_string "numberofcolumns" component}}</label>' +
                '</div><div class="col-sm-8">' +
                '<input class="form-control w-auto {{CSS.COLUMNS}}" type="number" value="3" ' +
                    'id="{{elementid}}_atto_table_columns"' +
                'size="8" min="1" max="20"/>' +
                '</div>' +
                '</div>' +
            '{{/if}}' +
            '{{#if allowStyling}}' +
                '<fieldset>' +
                '<legend class="mdl-align">{{get_string "appearance" component}}</legend>' +
                '{{#if allowBorders}}' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_borders">{{get_string "borders" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<select name="borders" class="custom-select {{CSS.BORDERS}}" id="{{elementid}}_atto_table_borders">' +
                        '<option value="default">{{get_string "themedefault" component}}' + '</option>' +
                        '<option value="outer">{{get_string "outer" component}}' + '</option>' +
                        '<option value="all">{{get_string "all" component}}' + '</option>' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_borderstyle">' +
                    '{{get_string "borderstyles" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<select name="borderstyles" class="custom-select {{CSS.BORDERSTYLE}}" ' +
                        'id="{{elementid}}_atto_table_borderstyle">' +
                        '{{#each borderStyles}}' +
                            '<option value="' + '{{this}}' + '">' + '{{get_string this ../component}}' + '</option>' +
                        '{{/each}}' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_bordersize">' +
                    '{{get_string "bordersize" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<div class="form-inline">' +
                    '<input name="bordersize" id="{{elementid}}_atto_table_bordersize" ' +
                    'class="form-control w-auto mr-1 {{CSS.BORDERSIZE}}"' +
                    'type="number" value="1" size="8" min="1" max="50"/>' +
                    '<label>{{CSS.BORDERSIZEUNIT}}</label>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_bordercolour">' +
                    '{{get_string "bordercolour" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<div id="{{elementid}}_atto_table_bordercolour"' +
                    'class="form-inline {{CSS.BORDERCOLOUR}} {{CSS.AVAILABLECOLORS}}" size="1">' +
                        '<div class="tablebordercolor" style="background-color:transparent;color:transparent">' +
                            '<input id="{{../elementid}}_atto_table_bordercolour_-1"' +
                            'type="radio" class="m-0" name="borderColour" value="none" checked="checked"' +
                            'title="{{get_string "themedefault" component}}"></input>' +
                            '<label for="{{../elementid}}_atto_table_bordercolour_-1" class="accesshide">' +
                            '{{get_string "themedefault" component}}</label>' +
                        '</div>' +
                        '{{#each availableColours}}' +
                            '<div class="tablebordercolor" style="background-color:{{this}};color:{{this}}">' +
                                '<input id="{{../elementid}}_atto_table_bordercolour_{{@index}}"' +
                                'type="radio" class="m-0" name="borderColour" value="' + '{{this}}' + '" title="{{this}}">' +
                                '<label for="{{../elementid}}_atto_table_bordercolour_{{@index}}" class="accesshide">' +
                                '{{this}}</label>' +
                            '</div>' +
                        '{{/each}}' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                '{{/if}}' +
                '{{#if allowBackgroundColour}}' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_backgroundcolour">' +
                    '{{get_string "backgroundcolour" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<div id="{{elementid}}_atto_table_backgroundcolour"' +
                    'class="form-inline {{CSS.BACKGROUNDCOLOUR}} {{CSS.AVAILABLECOLORS}}" size="1">' +
                        '<div class="tablebackgroundcolor" style="background-color:transparent;color:transparent">' +
                            '<input id="{{../elementid}}_atto_table_backgroundcolour_-1"' +
                            'type="radio" class="m-0" name="backgroundColour" value="none" checked="checked"' +
                            'title="{{get_string "themedefault" component}}"></input>' +
                            '<label for="{{../elementid}}_atto_table_backgroundcolour_-1" class="accesshide">' +
                            '{{get_string "themedefault" component}}</label>' +
                        '</div>' +

                        '{{#each availableColours}}' +
                            '<div class="tablebackgroundcolor" style="background-color:{{this}};color:{{this}}">' +
                                '<input id="{{../elementid}}_atto_table_backgroundcolour_{{@index}}"' +
                                'type="radio" class="m-0" name="backgroundColour" value="' + '{{this}}' + '" title="{{this}}">' +
                                '<label for="{{../elementid}}_atto_table_backgroundcolour_{{@index}}" class="accesshide">' +
                                '{{this}}</label>' +
                            '</div>' +
                        '{{/each}}' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                '{{/if}}' +
                '{{#if allowWidth}}' +
                    '<div class="mb-1 form-group row">' +
                    '<div class="col-sm-4">' +
                    '<label for="{{elementid}}_atto_table_width">' +
                    '{{get_string "width" component}}</label>' +
                    '</div><div class="col-sm-8">' +
                    '<div class="form-inline">' +
                    '<input name="width" id="{{elementid}}_atto_table_width" ' +
                        'class="form-control w-auto mr-1 {{CSS.WIDTH}}" size="8" ' +
                        'type="number" min="0" max="100"/>' +
                    '<label>{{CSS.WIDTHUNIT}}</label>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                '{{/if}}' +
                '</fieldset>' +
            '{{/if}}' +
            '<div class="mdl-align">' +
            '<br/>' +
            '{{#if edit}}' +
                '<button class="btn btn-secondary submit" type="submit">{{get_string "updatetable" component}}</button>' +
            '{{/if}}' +
            '{{#if nonedit}}' +
                '<button class="btn btn-secondary submit" type="submit">{{get_string "createtable" component}}</button>' +
            '{{/if}}' +
            '</div>' +
        '</form>',
    CSS = {
        CAPTION: 'caption',
        CAPTIONPOSITION: 'captionposition',
        HEADERS: 'headers',
        ROWS: 'rows',
        COLUMNS: 'columns',
        SUBMIT: 'submit',
        FORM: 'atto_form',
        BORDERS: 'borders',
        BORDERSIZE: 'bordersize',
        BORDERSIZEUNIT: 'px',
        BORDERCOLOUR: 'bordercolour',
        BORDERSTYLE: 'borderstyle',
        BACKGROUNDCOLOUR: 'backgroundcolour',
        WIDTH: 'customwidth',
        WIDTHUNIT: '%',
        AVAILABLECOLORS: 'availablecolors',
        COLOURROW: 'colourrow'
    },
    SELECTORS = {
        CAPTION: '.' + CSS.CAPTION,
        CAPTIONPOSITION: '.' + CSS.CAPTIONPOSITION,
        HEADERS: '.' + CSS.HEADERS,
        ROWS: '.' + CSS.ROWS,
        COLUMNS: '.' + CSS.COLUMNS,
        SUBMIT: '.' + CSS.SUBMIT,
        BORDERS: '.' + CSS.BORDERS,
        BORDERSIZE: '.' + CSS.BORDERSIZE,
        BORDERCOLOURS: '.' + CSS.BORDERCOLOUR + ' input[name="borderColour"]',
        SELECTEDBORDERCOLOUR: '.' + CSS.BORDERCOLOUR + ' input[name="borderColour"]:checked',
        BORDERSTYLE: '.' + CSS.BORDERSTYLE,
        BACKGROUNDCOLOURS: '.' + CSS.BACKGROUNDCOLOUR + ' input[name="backgroundColour"]',
        SELECTEDBACKGROUNDCOLOUR: '.' + CSS.BACKGROUNDCOLOUR + ' input[name="backgroundColour"]:checked',
        FORM: '.atto_form',
        WIDTH: '.' + CSS.WIDTH,
        AVAILABLECOLORS: '.' + CSS.AVAILABLECOLORS
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
        var button = this.addButton({
            icon: 'e/table',
            callback: this._displayTableEditor,
            tags: 'table'
        });

        // Listen for toggled highlighting.
        require(['editor_atto/events'], function(attoEvents) {
            var domButton = button.getDOMNode();
            domButton.addEventListener(attoEvents.eventTypes.attoButtonHighlightToggled, function(e) {
                this._setAriaAttributes(e.detail.buttonName, e.detail.highlight);
            }.bind(this));
        }.bind(this));

        // Disable mozilla table controls.
        if (Y.UA.gecko) {
            document.execCommand("enableInlineTableEditing", false, false);
            document.execCommand("enableObjectResizing", false, false);
        }
    },

    /**
     * Sets the appropriate ARIA attributes for the table button when it switches roles between a button and a menu button.
     *
     * @param {String} buttonName The button name.
     * @param {Boolean} highlight True when the button was highlighted. False, otherwise.
     * @private
     */
    _setAriaAttributes: function(buttonName, highlight) {
        var menuButton = this.buttons[buttonName];
        if (menuButton) {
            if (highlight) {
                // This button becomes a menu button. Add appropriate ARIA attributes.
                var id = menuButton.getAttribute('id');
                menuButton.setAttribute('aria-haspopup', true);
                menuButton.setAttribute('aria-controls', id + '_menu');
                menuButton.setAttribute('aria-expanded', true);
            } else {
                menuButton.removeAttribute('aria-haspopup');
                menuButton.removeAttribute('aria-controls');
                menuButton.removeAttribute('aria-expanded');
            }
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
                focusAfterHide: true,
                focusOnShowSelector: SELECTORS.CAPTION,
                width: DIALOGUE.WIDTH
            });

            // Set the dialogue content, and then show the dialogue.
            dialogue.set('bodyContent', this._getDialogueContent(false))
                    .show();

            this._updateAvailableSettings();
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
        var menuButton = e.currentTarget.ancestor('button', true);
        if (cell) {
            var id = menuButton.getAttribute('id');

            // Indicate that the menu is expanded.
            menuButton.setAttribute('aria-expanded', true);

            // Add the cell to the EventFacade to save duplication in when showing the menu.
            e.tableCell = cell;
            return this._showTableMenu(e, id);
        } else {
            // Dialog mode. Remove aria-expanded attribute.
            menuButton.removeAttribute('aria-expanded');
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
        return this.editor.contains(node);
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @private
     * @return {Node} The content to place in the dialogue.
     */
    _getDialogueContent: function(edit) {
        var template = Y.Handlebars.compile(TEMPLATE);
        var allowBorders = this.get('allowBorders');

        this._content = Y.Node.create(template({
                CSS: CSS,
                elementid: this.get('host').get('elementid'),
                component: COMPONENT,
                edit: edit,
                nonedit: !edit,
                allowStyling: this.get('allowStyling'),
                allowBorders: allowBorders,
                borderStyles: this.get('borderStyles'),
                allowBackgroundColour: this.get('allowBackgroundColour'),
                availableColours: this.get('availableColors'),
                allowWidth: this.get('allowWidth')
            }));

        // Handle table setting.
        if (edit) {
            this._content.one('.submit').on('click', this._updateTable, this);
        } else {
            this._content.one('.submit').on('click', this._setTable, this);
        }

        if (allowBorders) {
            this._content.one('[name="borders"]').on('change', this._updateAvailableSettings, this);
        }

        return this._content;
    },

    /**
     * Disables options within the dialogue if they shouldn't be available.
     * E.g.
     * If borders are set to "Theme default" then the border size, style and
     * colour options are disabled.
     *
     * @method _updateAvailableSettings
     * @private
     */
    _updateAvailableSettings: function() {
        var tableForm = this._content,
            enableBorders = tableForm.one('[name="borders"]'),
            borderStyle = tableForm.one('[name="borderstyles"]'),
            borderSize = tableForm.one('[name="bordersize"]'),
            borderColour = tableForm.all('[name="borderColour"]'),
            disabledValue = 'removeAttribute';

        if (!enableBorders) {
            return;
        }

        if (enableBorders.get('value') === 'default') {
            disabledValue = 'setAttribute';
        }

        if (borderStyle) {
            borderStyle[disabledValue]('disabled');
        }

        if (borderSize) {
            borderSize[disabledValue]('disabled');
        }

        if (borderColour) {
            borderColour[disabledValue]('disabled');
        }

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
        var stopAtContentEditableFilter = Y.bind(this._stopAtContentEditableFilter, this);

        host.getSelectedNodes().some(function(node) {
            if (node.ancestor('td, th, caption', true, stopAtContentEditableFilter)) {
                targetcell = node;

                var caption = node.ancestor('caption', true, stopAtContentEditableFilter);
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
            captionposition,
            headers,
            borders,
            bordersize,
            borderstyle,
            bordercolour,
            backgroundcolour,
            table,
            width,
            captionnode;

        e.preventDefault();
        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        // Add/update the caption.
        caption = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTION);
        captionposition = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTIONPOSITION);
        headers = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.HEADERS);
        borders = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERS);
        bordersize = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERSIZE);
        bordercolour = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.SELECTEDBORDERCOLOUR);
        borderstyle = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERSTYLE);
        backgroundcolour = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.SELECTEDBACKGROUNDCOLOUR);
        width = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.WIDTH);

        table = this._lastTarget.ancestor('table');
        this._setAppearance(table, {
            width: width,
            borders: borders,
            borderColour: bordercolour,
            borderSize: bordersize,
            borderStyle: borderstyle,
            backgroundColour: backgroundcolour
        });

        captionnode = table.one('caption');
        if (!captionnode) {
            captionnode = Y.Node.create('<caption></caption>');
            table.insert(captionnode, 0);
        }
        captionnode.setHTML(caption.get('value'));
        captionnode.setStyle('caption-side', captionposition.get('value'));
        if (!captionnode.getAttribute('style')) {
            captionnode.removeAttribute('style');
        }

        // Add the row headers.
        if (headers.get('value') === 'rows' || headers.get('value') === 'both') {
            table.all('tr').each(function(row) {
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
                cells.each(function(cell) {
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

            firstRow.all('td, th').each(function(cell) {
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
        // Clean the HTML.
        this.markUpdated();
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
            captionposition,
            borders,
            bordersize,
            borderstyle,
            bordercolour,
            rows,
            cols,
            headers,
            tablehtml,
            backgroundcolour,
            width,
            i, j;

        e.preventDefault();

        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        caption = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTION);
        captionposition = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.CAPTIONPOSITION);
        borders = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERS);
        bordersize = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERSIZE);
        bordercolour = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.SELECTEDBORDERCOLOUR);
        borderstyle = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.BORDERSTYLE);
        backgroundcolour = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.SELECTEDBACKGROUNDCOLOUR);
        rows = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.ROWS);
        cols = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.COLUMNS);
        headers = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.HEADERS);
        width = e.currentTarget.ancestor(SELECTORS.FORM).one(SELECTORS.WIDTH);

        // Set the selection.
        this.get('host').setSelection(this._currentSelection);

        // Note there are some spaces inserted in the cells and before and after, so that users have somewhere to click.
        var nl = "\n";
        var tableId = Y.guid();
        tablehtml = '<br/>' + nl + '<table id="' + tableId + '">' + nl;

        var captionstyle = '';
        if (captionposition.get('value')) {
            captionstyle = ' style="caption-side: ' + captionposition.get('value') + '"';
        }
        tablehtml += '<caption' + captionstyle + '>' + Y.Escape.html(caption.get('value')) + '</caption>' + nl;
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
                    tablehtml += '<td ></td>' + nl;
                }
            }
            tablehtml += '</tr>' + nl;
        }
        tablehtml += '</tbody>' + nl;
        tablehtml += '</table>' + nl + '<br/>';

        this.get('host').insertContentAtFocusPoint(tablehtml);

        var tableNode = Y.one('#' + tableId);
        this._setAppearance(tableNode, {
            width: width,
            borders: borders,
            borderColour: bordercolour,
            borderSize: bordersize,
            borderStyle: borderstyle,
            backgroundColour: backgroundcolour
        });
        tableNode.removeAttribute('id');

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
                cellprev = cells.item(columnindex - 1),
                cellnext = cells.item(columnindex + 1);
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
     * @param {String} buttonId The ID of the menu button associated with this menu.
     * @private
     */
    _showTableMenu: function(e, buttonId) {
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
                items: this._menuOptions,
                buttonId: buttonId
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
        if ((cells.next.size() > 0) &&
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
     * Obtain values for the table borders
     *
     * @method _getBorderConfiguration
     * @param {Node} node
     * @private
     * @return {Array} or {Boolean} Returns the settings, if presents, or else returns false
     */
    _getBorderConfiguration: function(node) {
        // We need to make a clone of the node in order to avoid grabbing any
        // of the computed styles from the DOM. We only want inline styles set by us.
        var shadowNode = node.cloneNode(true);
        var borderStyle = shadowNode.getStyle('borderStyle'),
            borderColor = shadowNode.getStyle('borderColor'),
            borderWidth = shadowNode.getStyle('borderWidth');

        if (borderStyle || borderColor || borderWidth) {
            var hexColour = Y.Color.toHex(borderColor);
            var width = parseInt(borderWidth, 10);
            return {
                borderStyle: borderStyle,
                borderColor: hexColour === "#" ? null : hexColour,
                borderWidth: isNaN(width) ? null : width
            };
        }

        return false;
    },

    /**
     * Set the appropriate styles on the given table node according to
     * the provided configuration.
     *
     * @method _setAppearance
     * @param {Node} The table node to be modified.
     * @param {Object} Configuration object (associative array) containing the form nodes for
     *                 border styling.
     * @private
     */
    _setAppearance: function(tableNode, configuration) {
        var borderhex,
            borderSizeValue,
            borderStyleValue,
            backgroundcolourvalue;

        if (configuration.borderColour) {
            borderhex = configuration.borderColour.get('value');
        }

        if (configuration.borderSize) {
            borderSizeValue = configuration.borderSize.get('value');
        }

        if (configuration.borderStyle) {
            borderStyleValue = configuration.borderStyle.get('value');
        }

        if (configuration.backgroundColour) {
            backgroundcolourvalue = configuration.backgroundColour.get('value');
        }

        // Clear the inline border styling
        tableNode.removeAttribute('style');
        tableNode.all('td, th').each(function(cell) {
            cell.removeAttribute('style');
        }, this);

        if (configuration.borders) {
            if (configuration.borders.get('value') === 'outer') {
                tableNode.setStyle('borderWidth', borderSizeValue + CSS.BORDERSIZEUNIT);
                tableNode.setStyle('borderStyle', borderStyleValue);

                if (borderhex !== 'none') {
                    tableNode.setStyle('borderColor', borderhex);
                }
            } else if (configuration.borders.get('value') === 'all') {
                tableNode.all('td, th').each(function(cell) {
                    cell.setStyle('borderWidth', borderSizeValue + CSS.BORDERSIZEUNIT);
                    cell.setStyle('borderStyle', borderStyleValue);

                    if (borderhex !== 'none') {
                        cell.setStyle('borderColor', borderhex);
                    }
                }, this);
            }
        }

        if (backgroundcolourvalue !== 'none') {
            tableNode.setStyle('backgroundColor', backgroundcolourvalue);
        }

        if (configuration.width && configuration.width.get('value')) {
            tableNode.setStyle('width', configuration.width.get('value') + CSS.WIDTHUNIT);
        }
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
            focusAfterHide: false,
            focusOnShowSelector: SELECTORS.CAPTION,
            width: DIALOGUE.WIDTH
        });

        // Set the dialogue content, and then show the dialogue.
        var node = this._getDialogueContent(true),
            captioninput = node.one(SELECTORS.CAPTION),
            captionpositioninput = node.one(SELECTORS.CAPTIONPOSITION),
            headersinput = node.one(SELECTORS.HEADERS),
            borderinput = node.one(SELECTORS.BORDERS),
            borderstyle = node.one(SELECTORS.BORDERSTYLE),
            bordercolours = node.all(SELECTORS.BORDERCOLOURS),
            bordersize = node.one(SELECTORS.BORDERSIZE),
            backgroundcolours = node.all(SELECTORS.BACKGROUNDCOLOURS),
            width = node.one(SELECTORS.WIDTH),
            table = this._lastTarget.ancestor('table'),
            captionnode = table.one('caption'),
            hexColour,
            matchedInput;

        if (captionnode) {
            captioninput.set('value', captionnode.getHTML());
        } else {
            captioninput.set('value', '');
        }

        if (width && table.getStyle('width').indexOf('px') === -1) {
            width.set('value', parseInt(table.getStyle('width'), 10));
        }

        if (captionpositioninput && captionnode && captionnode.getAttribute('style')) {
            captionpositioninput.set('value', captionnode.getStyle('caption-side'));
        } else {
            // Default to none.
            captionpositioninput.set('value', '');
        }

        if (table.getStyle('backgroundColor') && this.get('allowBackgroundColour')) {
            hexColour = Y.Color.toHex(table.getStyle('backgroundColor'));
            matchedInput = backgroundcolours.filter('[value="' + hexColour + '"]');

            if (matchedInput) {
                matchedInput.set("checked", true);
            }
        }

        if (this.get('allowBorders')) {
            var borderValue = 'default',
                borderConfiguration = this._getBorderConfiguration(table);

            if (borderConfiguration) {
                borderValue = 'outer';
            } else {
                borderConfiguration = this._getBorderConfiguration(table.one('td'));
                if (borderConfiguration) {
                     borderValue = 'all';
                }
            }

            if (borderConfiguration) {
                var borderStyle = borderConfiguration.borderStyle || DEFAULT.BORDERSTYLE;
                var borderSize = borderConfiguration.borderWidth || DEFAULT.BORDERWIDTH;
                borderstyle.set('value', borderStyle);
                bordersize.set('value', borderSize);
                borderinput.set('value', borderValue);

                hexColour = borderConfiguration.borderColor;
                matchedInput = bordercolours.filter('[value="' + hexColour + '"]');

                if (matchedInput) {
                    matchedInput.set("checked", true);
                }
            }
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
        this._updateAvailableSettings();
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
        var target = this._lastTarget.ancestor('tr'),
            tablebody = this._lastTarget.ancestor('table').one('tbody');
        if (!tablebody) {
            // Not all tables have tbody.
            tablebody = this._lastTarget.ancestor('table');
        }

        var firstrow = tablebody.one('tr');
        if (!firstrow) {
            firstrow = this._lastTarget.ancestor('table').one('tr');
        }
        if (!firstrow) {
            // Table has no rows. Boo.
            return;
        }
        var newrow = firstrow.cloneNode(true);
        newrow.all('th, td').each(function(tablecell) {
            if (tablecell.get('tagName') === 'TH') {
                if (tablecell.getAttribute('scope') !== 'row') {
                    var newcell = Y.Node.create('<td></td>');
                    tablecell.replace(newcell);
                    tablecell = newcell;
                }
            }
            tablecell.setHTML('&nbsp;');
        });

        if (target.ancestor('thead')) {
            target = firstrow;
            tablebody.insert(newrow, target);
        } else {
            target.insert(newrow, 'after');
        }

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

}, {
    ATTRS: {
        /**
         * Whether or not to allow borders
         *
         * @attribute allowBorder
         * @type Boolean
         */
        allowBorders: {
            value: true
        },

        /**
         * What border styles to allow
         *
         * @attribute borderStyles
         * @type Array
         */
        borderStyles: {
            value: [
                'none',
                'solid',
                'dashed',
                'dotted'
            ]
        },

        /**
         * Whether or not to allow colourizing the background
         *
         * @attribute allowBackgroundColour
         * @type Boolean
         */
        allowBackgroundColour: {
            value: true
        },

        /**
         * Whether or not to allow setting the table width
         *
         * @attribute allowWidth
         * @type Boolean
         */
        allowWidth: {
            value: true
        },

        /**
         * Whether we allow styling
         * @attribute allowStyling
         * @type Boolean
         */
        allowStyling: {
            readOnly: true,
            getter: function() {
                return this.get('allowBorders') || this.get('allowBackgroundColour') || this.get('allowWidth');
            }
        },

        /**
         * Available colors
         * @attribute availableColors
         * @type Array
         */
        availableColors: {
            value: [
                '#FFFFFF',
                '#EF4540',
                '#FFCF35',
                '#98CA3E',
                '#7D9FD3',
                '#333333'
            ],
            readOnly: true
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "moodle-editor_atto-menu", "event", "event-valuechange"]});
