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
 * @package    atto_equation
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Atto text editor equation plugin.
 */

/**
 * Atto equation editor.
 *
 * @namespace M.atto_equation
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_equation',
    CSS = {
        EQUATION_TEXT: 'atto_equation_equation',
        EQUATION_PREVIEW: 'atto_equation_preview',
        SUBMIT: 'atto_equation_submit',
        LIBRARY: 'atto_equation_library',
        LIBRARY_GROUP_PREFIX: 'atto_equation_library'
    },
    SELECTORS = {
        LIBRARY_GROUP_PREFIX: '.' + CSS.LIBRARY_GROUP_PREFIX,
        EQUATION_TEXT: '.' + CSS.EQUATION_TEXT,
        EQUATION_PREVIEW: '.' + CSS.EQUATION_PREVIEW,
        SUBMIT: '.' + CSS.SUBMIT,
        LIBRARY_BUTTON: '.' + CSS.LIBRARY + ' button'
    },
    TEMPLATES = {
        FORM: '' +
            '<form class="atto_form">' +
                '{{{library}}}' +
                '<label for="{{elementid}}_{{CSS.EQUATION_TEXT}}">{{{get_string "editequation" component texdocsurl}}}</label>' +
                '<textarea class="fullwidth {{CSS.EQUATION_TEXT}}" id="{{elementid}}_{{CSS.EQUATION_TEXT}}" rows="8"></textarea><br/>' +
                '<label for="{{elementid}}_{{CSS.EQUATION_PREVIEW}}">{{get_string "preview" component}}</label>' +
                '<div class="fullwidth {{CSS.EQUATION_PREVIEW}}" id="{{elementid}}_{{CSS.EQUATION_PREVIEW}}"></div>' +
                '<div class="mdl-align">' +
                    '<br/>' +
                    '<button class="{{CSS.SUBMIT}}">{{get_string "saveequation" component}}</button>' +
                '</div>' +
            '</form>',
        LIBRARY: '' +
            '<div class="{{CSS.LIBRARY}}">' +
                '<ul>' +
                    '{{#each library}}' +
                        '<li><a href="#{{elementid}}_{{../CSS.LIBRARY_GROUP_PREFIX}}{{@key}}">{{get_string groupname ../component}}</a></li>' +
                    '{{/each}}' +
                '</ul>' +
                '<div>' +
                    '{{#each library}}' +
                        '<div id="{{elementid}}_{{../CSS.LIBRARY_GROUP_PREFIX}}{{@key}}">' +
                        '{{#split "\n" elements}}' +
                            '<button data-tex="{{this}}" title="{{this}}">$${{this}}$$</button>' +
                        '{{/split}}' +
                        '</div>' +
                    '{{/each}}' +
                '</div>' +
            '</div>'
    };

Y.namespace('M.atto_equation').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * The selection object returned by the browser.
     *
     * @property _currentSelection
     * @type Range
     * @default null
     * @private
     */
    _currentSelection: null,

    /**
     * The cursor position in the equation textarea.
     *
     * @property _lastCursorPos
     * @type Number
     * @default 0
     * @private
     */
    _lastCursorPos: 0,

    /**
     * A reference to the dialogue content.
     *
     * @property _content
     * @type Node
     * @private
     */
    _content: null,

    initializer: function() {
        if (this.get('texfilteractive')) {
            // Add the button to the toolbar.
            this.addButton({
                icon: 'e/math',
                callback: this._displayDialogue
            });

            // We need custom highlight logic for this button.
            this.get('host').on('atto:selectionchanged', function() {
                if (this._resolveEquation()) {
                    this.highlightButtons();
                } else {
                    this.unHighlightButtons();
                }
            }, this);
        }
    },

    /**
     * Display the equation editor.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        this._currentSelection = this.get('host').getSelection();

        if (this._currentSelection === false) {
            return;
        }

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            focusAfterHide: true
        });

        var content = this._getDialogueContent();
        dialogue.set('bodyContent', content);

        var library = content.one(SELECTORS.LIBRARY_GROUP_PREFIX);

        var tabview = new Y.TabView({
            srcNode: library
        });

        tabview.render();
        dialogue.show();

        var equation = this._resolveEquation();
        if (equation) {
            content.one(SELECTORS.EQUATION_TEXT).set('text', equation);
        }
        this._updatePreview(false);
    },

    /**
     * If there is selected text and it is part of an equation,
     * extract the equation (and set it in the form).
     *
     * @method _resolveEquation
     * @private
     * @return {String|Boolean} The equation or false.
     */
    _resolveEquation: function() {

        // Find the equation in the surrounding text.
        var selectedNode = this.get('host').getSelectionParentNode(),
            text,
            equation;

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectedNode) {
            return false;
        }

        text = Y.one(selectedNode).get('text');
        // We use space or not space because . does not match new lines.
        pattern = /\$\$[\S\s]*\$\$/;
        equation = pattern.exec(text);
        if (equation && equation.length) {
            equation = equation.pop();
            // Replace the equation.
            equation = equation.substring(2, equation.length - 2);
            return equation;
        }
        return false;
    },

    /**
     * Handle insertion of a new equation, or update of an existing one.
     *
     * @method _setEquation
     * @param {EventFacade} e
     * @private
     */
    _setEquation: function(e) {
        var input,
            selectedNode,
            text,
            pattern,
            equation,
            value;

        var host = this.get('host');

        e.preventDefault();
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        input = e.currentTarget.ancestor('.atto_form').one('textarea');

        value = input.get('value');
        if (value !== '') {
            host.setSelection(this._currentSelection);

            value = '$$ ' + value.trim() + ' $$';
            selectedNode = Y.one(host.getSelectionParentNode());
            text = selectedNode.get('text');
            pattern = /\$\$[\S\s]*\$\$/;
            equation = pattern.exec(text);
            if (equation && equation.length) {
                // Replace the equation.
                equation = equation.pop();
                text = text.replace(equation, '$$' + value + '$$');
                selectedNode.set('text', text);
            } else {
                // Insert the new equation.
                host.insertContentAtFocusPoint(value);
            }

            // Clean the YUI ids from the HTML.
            this.markUpdated();
        }
    },

    /**
     * Update the preview div to match the current equation.
     *
     * @param {EventFacade} e
     * @method _updatePreview
     * @private
     */
    _updatePreview: function(e) {
        var textarea = this._content.one(SELECTORS.EQUATION_TEXT),
            equation = textarea.get('value'),
            url,
            preview,
            currentPos = textarea.get('selectionStart'),
            prefix = '',
            cursorLatex = '\\square ',
            isChar;


        if (e) {
            e.preventDefault();
        }

        if (!currentPos) {
            currentPos = 0;
        }
        // Move the cursor so it does not break expressions.
        //
        while (equation.charAt(currentPos) === '\\' && currentPos > 0) {
            currentPos -= 1;
        }
        isChar = /[\w\{\}]/;
        while (isChar.test(equation.charAt(currentPos)) && currentPos < equation.length) {
            currentPos += 1;
        }
        // Save the cursor position - for insertion from the library.
        this._lastCursorPos = currentPos;
        equation = prefix + equation.substring(0, currentPos) + cursorLatex + equation.substring(currentPos);
        url = M.cfg.wwwroot + '/lib/editor/atto/plugins/equation/ajax.php';
        params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'filtertext',
            text: '$$ ' + equation + ' $$'
        };

        preview = Y.io(url, { sync: true,
                              data: params });
        if (preview.status === 200) {
            this._content.one(SELECTORS.EQUATION_PREVIEW).setHTML(preview.responseText);
        }
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node}
     * @private
     */
    _getDialogueContent: function() {
        var library = this._getLibraryContent(),
            template = Y.Handlebars.compile(TEMPLATES.FORM);

        this._content = Y.Node.create(template({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            library: library,
            texdocsurl: this.get('texdocsurl'),
            CSS: CSS
        }));

        this._content.one(SELECTORS.SUBMIT).on('click', this._setEquation, this);
        this._content.one(SELECTORS.EQUATION_TEXT).on('valuechange', this._updatePreview, this);
        this._content.one(SELECTORS.EQUATION_TEXT).on('mouseup', this._updatePreview, this);
        this._content.one(SELECTORS.EQUATION_TEXT).on('keyup', this._updatePreview, this);
        this._content.delegate('click', this._selectLibraryItem, SELECTORS.LIBRARY_BUTTON, this);

        return this._content;
    },

    /**
     * Reponse to button presses in the TeX library panels.
     *
     * @method _selectLibraryItem
     * @param {EventFacade} e
     * @return {string}
     * @private
     */
    _selectLibraryItem: function(e) {
        var tex = e.currentTarget.getAttribute('data-tex');

        e.preventDefault();

        input = e.currentTarget.ancestor('.atto_form').one('textarea');

        value = input.get('value');

        value = value.substring(0, this._lastCursorPos) + tex + value.substring(this._lastCursorPos, value.length);

        input.set('value', value);
        input.focus();

        var focusPoint = this._lastCursorPos + tex.length,
            realInput = input.getDOMNode();
        if (typeof realInput.selectionStart === "number") {
            // Modern browsers have selectionStart and selectionEnd to control the cursor position.
            realInput.selectionStart = realInput.selectionEnd = focusPoint;
        } else if (typeof realInput.createTextRange !== "undefined") {
            // Legacy browsers (IE<=9) use createTextRange().
            var range = realInput.createTextRange();
            range.moveToPoint(focusPoint);
            range.select();
        }
        // Focus must be set before updating the preview for the cursor box to be in the correct location.
        this._updatePreview(false);
    },

    /**
     * Return the HTML for rendering the library of predefined buttons.
     *
     * @method _getLibraryContent
     * @return {string}
     * @private
     */
    _getLibraryContent: function() {
        var template = Y.Handlebars.compile(TEMPLATES.LIBRARY),
            library = this.get('library'),
            content = '';

        // Helper to iterate over a newline separated string.
        Y.Handlebars.registerHelper('split', function(delimiter, str, options) {
            var parts,
                current,
                out;
            if (typeof delimiter === "undefined" || typeof str === "undefined") {
                Y.log('Handlebars split helper: String and delimiter are required.', 'debug', 'moodle-atto_equation-button');
                return '';
            }

            out = '';
            parts = str.trim().split(delimiter);
            while (parts.length > 0) {
                current = parts.shift();
                out += options.fn(current);
            }

            return out;
        });
        content = template({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            library: library,
            CSS: CSS
        });

        var url = M.cfg.wwwroot + '/lib/editor/atto/plugins/equation/ajax.php';
        var params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'filtertext',
            text: content
        };

        preview = Y.io(url, {
            sync: true,
            data: params,
            method: 'POST'
        });

        if (preview.status === 200) {
            content = preview.responseText;
        }
        return content;
    }
}, {
    ATTRS: {
        /**
         * Whether the TeX filter is currently active.
         *
         * @attribute texfilteractive
         * @type Boolean
         */
        texfilteractive: {
            value: false
        },
        /**
         * The contextid to use when generating this preview.
         *
         * @attribute contextid
         * @type String
         */
        contextid: {
            value: null
        },

        /**
         * The content of the example library.
         *
         * @attribute library
         * @type object
         */
        library: {
            value: {}
        },

        /**
         * The link to the Moodle Docs page about TeX.
         *
         * @attribute texdocsurl
         * @type string
         */
        texdocsurl: {
            value: null
        }
    }
});
