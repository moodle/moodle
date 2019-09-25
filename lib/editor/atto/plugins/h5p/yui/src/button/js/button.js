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

/*
 * @package    atto_h5p
 * @copyright  2019 Bas Brands  <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_h5p-button
 */

/**
 * Atto h5p content tool.
 *
 * @namespace M.atto_h5p
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var CSS = {
        INPUTALT: 'atto_h5p_altentry',
        INPUTSUBMIT: 'atto_h5p_urlentrysubmit',
        INPUTH5PURL: 'atto_h5p_url',
        URLWARNING: 'atto_h5p_warning'
    },
    SELECTORS = {
        INPUTH5PURL: '.' + CSS.INPUTH5PURL
    },

    COMPONENTNAME = 'atto_h5p',

    TEMPLATE = '' +
            '<form class="atto_form">' +
                '<div class="mb-4">' +
                    '<label for="{{elementid}}_{{CSS.INPUTH5PURL}}">{{get_string "enterurl" component}}</label>' +
                    '<div style="display:none" role="alert" class="alert alert-warning mb-1 {{CSS.URLWARNING}}">' +
                        '{{get_string "invalidh5purl" component}}' +
                    '</div>' +
                    '<input class="form-control fullwidth {{CSS.INPUTH5PURL}}" type="url" ' +
                    'id="{{elementid}}_{{CSS.INPUTH5PURL}}" size="32"/>' +
                '</div>' +
                '<div class="text-center">' +
                '<button class="btn btn-secondary {{CSS.INPUTSUBMIT}}" type="submit">' + '' +
                    '{{get_string "saveh5p" component}}</button>' +
                '</div>' +
            '</form>',

        H5PTEMPLATE = '' +
            '<div class="position-relative h5p-embed-placeholder">' +
                '<div class="attoh5poverlay"></div>' +
                '<iframe id="h5pcontent" class="h5pcontent" src="{{url}}/embed" ' +
                    'width="100%" height="637" frameborder="0"' +
                    'allowfullscreen="{{allowfullscreen}}" allowmedia="{{allowmedia}}">' +
                '</iframe>' +
                '<script src="' + M.cfg.wwwroot + '/lib/editor/atto/plugins/h5p/js/h5p-resizer.js"' +
                    'charset="UTF-8"></script>' +
                '</div>' +
            '</div>' +
            '<p><br></p>';

Y.namespace('M.atto_h5p').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
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
     * A reference to the currently open form.
     *
     * @param _form
     * @type Node
     * @private
     */
    _form: null,

    /**
     * A reference to the currently selected H5P placeholder.
     *
     * @param _form
     * @type Node
     * @private
     */
    _placeholderH5P: null,

    initializer: function() {
        var allowedmethods = this.get('allowedmethods');
        if (allowedmethods !== 'embed') {
            // Plugin not available here.
            return;
        }

        this.addButton({
            icon: 'icon',
            iconComponent: 'atto_h5p',
            callback: this._displayDialogue,
            tags: '.attoh5poverlay',
            tagMatchRequiresAll: false
        });

        this.editor.delegate('dblclick', this._handleDblClick, '.attoh5poverlay', this);
        this.editor.delegate('click', this._handleClick, '.attoh5poverlay', this);
    },

    /**
     * Handle a double click on a H5P Placeholder.
     *
     * @method _handleDblClick
     * @private
     */
    _handleDblClick: function() {
        this._displayDialogue();
    },

    /**
     * Handle a click on a H5P Placeholder.
     *
     * @method _handleClick
     * @param {EventFacade} e
     * @private
     */
    _handleClick: function(e) {
        var h5pplaceholder = e.target;

        var selection = this.get('host').getSelectionFromNode(h5pplaceholder);
        if (this.get('host').getSelection() !== selection) {
            this.get('host').setSelection(selection);
        }
    },

    /**
     * Display the h5p editing tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current selection.
        this._currentSelection = this.get('host').getSelection();
        this._placeholderH5P = this._getH5PIframe();

        if (this._currentSelection === false) {
            return;
        }
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('h5pproperties', COMPONENTNAME),
            width: 'auto',
            focusAfterHide: true,
            focusOnShowSelector: SELECTORS.INPUTH5PURL
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
            .show();
    },

    /**
     * Get the H5P iframe
     *
     * @method _resolveH5P
     * @return {Node} The H5P iframe selected.
     * @private
     */
    _getH5PIframe: function() {
        var selectednode = this.get('host').getSelectionParentNode();
        if (!selectednode) {
            return;
        }
        return Y.one(selectednode).one('iframe.h5pcontent');
    },


    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATE),
            content = Y.Node.create(template({
                elementid: this.get('host').get('elementid'),
                CSS: CSS,
                component: COMPONENTNAME
            }));

        this._form = content;

        if (this._placeholderH5P) {
            var oldurl = this._placeholderH5P.getAttribute('src');
            this._form.one(SELECTORS.INPUTH5PURL).setAttribute('value', oldurl);
        }

        this._form.one('.' + CSS.INPUTSUBMIT).on('click', this._setH5P, this);

        return content;
    },

    /**
     * Set the h5p in the contenteditable.
     *
     * @method _setH5P
     * @param {EventFacade} e
     * @private
     */
    _setH5P: function(e) {
        var form = this._form,
            url = form.one(SELECTORS.INPUTH5PURL).get('value'),
            h5phtml,
            host = this.get('host');

        e.preventDefault();

        // Check if there are any issues.
        if (this._updateWarning()) {
            return;
        }

        // Focus on the editor in preparation for inserting the h5p.
        host.focus();

        // If a H5P placeholder was selected we only update the placeholder.
        if (this._placeholderH5P) {
            this._placeholderH5P.setAttribute('src', url);

        } else if (url !== '') {

            host.setSelection(this._currentSelection);

            var template = Y.Handlebars.compile(H5PTEMPLATE);
            h5phtml = template({
                url: url,
                allowfullscreen: 'allowfullscreen',
                allowmedia: 'geolocation *; microphone *; camera *; midi *; encrypted-media *'
            });

            this.get('host').insertContentAtFocusPoint(h5phtml);

            this.markUpdated();
        }

        this.getDialogue({
            focusAfterHide: null
        }).hide();
    },

    /**
     * Check if this could be a h5p URL.
     *
     * @method _updateWarning
     * @param {String} str
     * @return {boolean} whether a warning should be displayed.
     * @private
     */
    _validURL: function(str) {
        var pattern = new RegExp('^(https?:\\/\\/)?' + // Protocol.
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // Domain name.
            '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address.
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'); // Port and path.
        return !!pattern.test(str);
    },

    /**
     * Update the url warning.
     *
     * @method _updateWarning
     * @return {boolean} whether a warning should be displayed.
     * @private
     */
    _updateWarning: function() {
        var form = this._form,
            state = true,
            url = form.one('.' + CSS.INPUTH5PURL).get('value');
        if (this._validURL(url)) {
            form.one('.' + CSS.URLWARNING).setStyle('display', 'none');
            state = false;
        } else {
            form.one('.' + CSS.URLWARNING).setStyle('display', 'block');
            state = true;
        }
        return state;
    }
}, {
    ATTRS: {
        /**
         * The allowedmethods of adding h5p content.
         *
         * @attribute allowedmethods
         * @type String
         */
        allowedmethods: {
            value: null
        }
    }
});
