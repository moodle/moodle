YUI.add('moodle-atto_h5p-button', function (Y, NAME) {

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
        CONTENTWARNING: 'att_h5p_contentwarning',
        H5PBROWSER: 'openh5pbrowser',
        INPUTALT: 'atto_h5p_altentry',
        INPUTH5PFILE: 'atto_h5p_file',
        INPUTSUBMIT: 'atto_h5p_urlentrysubmit',
        OPTION_DOWNLOAD_BUTTON: 'atto_h5p_option_download_button',
        OPTION_COPYRIGHT_BUTTON: 'atto_h5p_option_copyright_button',
        OPTION_EMBED_BUTTON: 'atto_h5p_option_embed_button',
        URLWARNING: 'atto_h5p_warning'
    },
    SELECTORS = {
        CONTENTWARNING: '.' + CSS.CONTENTWARNING,
        H5PBROWSER: '.' + CSS.H5PBROWSER,
        INPUTH5PFILE: '.' + CSS.INPUTH5PFILE,
        INPUTSUBMIT: '.' + CSS.INPUTSUBMIT,
        OPTION_DOWNLOAD_BUTTON: '.' + CSS.OPTION_DOWNLOAD_BUTTON,
        OPTION_COPYRIGHT_BUTTON: '.' + CSS.OPTION_COPYRIGHT_BUTTON,
        OPTION_EMBED_BUTTON: '.' + CSS.OPTION_EMBED_BUTTON,
        URLWARNING: '.' + CSS.URLWARNING
    },

    COMPONENTNAME = 'atto_h5p',

    TEMPLATE = '' +
            '<form class="atto_form mform" id="{{elementid}}_atto_h5p_form">' +
                '<div style="display:none" role="alert" class="alert alert-warning mb-1 {{CSS.CONTENTWARNING}}">' +
                    '{{get_string "noh5pcontent" component}}' +
                '</div>' +
                '<div style="display:none" role="alert" class="alert alert-warning mb-1 {{CSS.URLWARNING}}">' +
                    '{{get_string "invalidh5purl" component}}' +
                '</div>' +
                '{{#if canUploadAndEmbed}}' +
                    '<div class="mt-2 mb-4 attoh5pinstructions">{{{get_string "instructions" component}}}</div>' +
                '{{/if}}' +
                '<div class="mb-4">' +
                    '<label for="{{elementid}}_{{CSS.H5PBROWSER}}">' +
                        '{{#if canUploadAndEmbed}}' +
                            '{{get_string "h5pfileorurl" component}}' +
                        '{{/if}}' +
                        '{{^if canUploadAndEmbed}}' +
                            '{{#if canUpload}}' +
                                '{{get_string "h5pfile" component}}' +
                            '{{/if}}' +
                            '{{#if canEmbed}}' +
                                '{{get_string "h5purl" component}}' +
                            '{{/if}}' +
                        '{{/if}}' +
                    '</label>' +
                    '<div class="input-group input-append w-100">' +
                        '<input class="form-control {{CSS.INPUTH5PFILE}}" type="url" value="{{fileURL}}" ' +
                        'id="{{elementid}}_{{CSS.INPUTH5PFILE}}" data-region="h5pfile" size="32"/>' +
                        '{{#if canUpload}}' +
                            '<span class="input-group-append">' +
                                '<button class="btn btn-secondary {{CSS.H5PBROWSER}}" type="button">' +
                                '{{get_string "browserepositories" component}}</button>' +
                            '</span>' +
                        '{{/if}}' +
                    '</div>' +
                    '{{#if canUpload}}' +
                        '<fieldset class="mt-2 collapsible" id="{{elementid}}_h5poptions">' +
                            '<legend class="d-flex align-items-center px-1">' +
                                '<div class="position-relative d-flex ftoggler align-items-center position-relative mr-1">' +
                                    '<a role="button" data-toggle="collapse" href="#h5poptions"' +
                                    'aria-expanded="{{#if showOptions}}true{{/if}}{{^if showOptions}}false{{/if}}"' +
                                        'aria-controls="h5poptions"' +
                                        'class="btn btn-icon mr-1 icons-collapse-expand stretched-link fheader collapsed">' +
                                        '<span class="expanded-icon icon-no-margin p-2"' +
                                            'title="{{get_string "collapse" "moodle"}}">' +
                                            '<i class="icon fa fa-chevron-down fa-fw " aria-hidden="true"></i>' +
                                        '</span>' +
                                        '<span class="collapsed-icon icon-no-margin p-2"' +
                                            'title="{{get_string "expand" "moodle"}}">' +
                                            '<span class="dir-rtl-hide">' +
                                                '<i class="icon fa fa-chevron-right fa-fw " aria-hidden="true"></i>' +
                                            '</span>' +
                                            '<span class="dir-ltr-hide">' +
                                                '<i class="icon fa fa-chevron-left fa-fw " aria-hidden="true"></i>' +
                                            '</span>' +
                                        '</span>' +
                                        '<span class="sr-only">{{get_string "h5poptions" component}}</span>' +
                                    '</a>' +
                                    '<h3 class="d-flex align-self-stretch align-items-center mb-0" aria-hidden="true">' +
                                        '{{get_string "h5poptions" component}}' +
                                    '</h3>' +
                                '</div>' +
                            '</legend>' +
                            '<div id="h5poptions" class="fcontainer collapseable collapse px-1 {{#if showOptions}}show{{/if}}">' +
                                '<div class="form-check">' +
                                    '<input type="checkbox" {{optionDownloadButton}} ' +
                                    'class="form-check-input {{CSS.OPTION_DOWNLOAD_BUTTON}}"' +
                                    'aria-label="{{get_string "downloadbutton" component}}" ' +
                                    'id="{{elementid}}_h5p-option-allow-download"/>' +
                                    '<label class="form-check-label" for="{{elementid}}_h5p-option-allow-download">' +
                                    '{{get_string "downloadbutton" component}}' +
                                    '</label>' +
                                '</div>' +
                                '<div class="form-check">' +
                                    '<input type="checkbox" {{optionEmbedButton}} ' +
                                    'class="form-check-input {{CSS.OPTION_EMBED_BUTTON}}" ' +
                                    'aria-label="{{get_string "embedbutton" component}}" ' +
                                        'id="{{elementid}}_h5p-option-embed-button"/>' +
                                    '<label class="form-check-label" for="{{elementid}}_h5p-option-embed-button">' +
                                    '{{get_string "embedbutton" component}}' +
                                    '</label>' +
                                '</div>' +
                                '<div class="form-check mb-2">' +
                                    '<input type="checkbox" {{optionCopyrightButton}} ' +
                                    'class="form-check-input {{CSS.OPTION_COPYRIGHT_BUTTON}}" ' +
                                    'aria-label="{{get_string "copyrightbutton" component}}" ' +
                                        'id="{{elementid}}_h5p-option-copyright-button"/>' +
                                    '<label class="form-check-label" for="{{elementid}}_h5p-option-copyright-button">' +
                                    '{{get_string "copyrightbutton" component}}' +
                                    '</label>' +
                                '</div>' +
                            '</div>' +
                        '</fieldset>' +
                    '{{/if}}' +
                '</div>' +
                '<div class="text-center">' +
                '<button class="btn btn-secondary {{CSS.INPUTSUBMIT}}" type="submit">' + '' +
                    '{{get_string "pluginname" component}}</button>' +
                '</div>' +
            '</form>',

        H5PTEMPLATE = '' +
            '{{#if addParagraphs}}<p><br></p>{{/if}}' +
            '<div class="h5p-placeholder" contenteditable="false">' +
                '{{{url}}}' +
            '</div>' +
            '{{#if addParagraphs}}<p><br></p>{{/if}}';

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
     * A reference to the currently selected H5P div.
     *
     * @param _form
     * @type Node
     * @private
     */
    _H5PDiv: null,

    /**
     * Allowed methods of adding H5P.
     *
     * @param _allowedmethods
     * @type String
     * @private
     */
    _allowedmethods: 'none',

    initializer: function() {
        this._allowedmethods = this.get('allowedmethods');
        if (this._allowedmethods === 'none') {
            // Plugin not available here.
            return;
        }
        this.addButton({
            icon: 'icon',
            iconComponent: 'atto_h5p',
            callback: this._displayDialogue,
            tags: '.h5p-placeholder',
            tagMatchRequiresAll: false
        });

        this.editor.all('.h5p-placeholder').setAttribute('contenteditable', 'false');
        this.editor.delegate('dblclick', this._handleDblClick, '.h5p-placeholder', this);
        this.editor.delegate('click', this._handleClick, '.h5p-placeholder', this);
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
        var selection = this.get('host').getSelectionFromNode(e.target);
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

        if (this._currentSelection === false) {
            return;
        }

        this._getH5PDiv();

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            width: 'auto',
            focusAfterHide: true
        });
        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
            .show();
        M.form.shortforms({formid: this.get('host').get('elementid') + '_atto_h5p_form'});
    },

    /**
     * Get the H5P iframe
     *
     * @method _resolveH5P
     * @return {Node} The H5P iframe selected.
     * @private
     */
    _getH5PDiv: function() {
        var selectednodes = this.get('host').getSelectedNodes();
        var H5PDiv = null;
        selectednodes.each(function(selNode) {
            if (selNode.hasClass('h5p-placeholder')) {
                H5PDiv = selNode;
            }
        });
        this._H5PDiv = H5PDiv;
    },

    /**
     * Get the H5P button permissions.
     *
     * @return {Object} H5P button permissions.
     * @private
     */
    _getPermissions: function() {
        var permissions = {
            'canEmbed': false,
            'canUpload': false,
            'canUploadAndEmbed': false
        };

        if (this.get('host').canShowFilepicker('h5p')) {
            if (this._allowedmethods === 'both') {
                permissions.canUploadAndEmbed = true;
                permissions.canUpload = true;
            } else if (this._allowedmethods === 'upload') {
                permissions.canUpload = true;
            }
        }

        if (this._allowedmethods === 'both' || this._allowedmethods === 'embed') {
            permissions.canEmbed = true;
        }
        return permissions;
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

        var permissions = this._getPermissions();

        var fileURL,
            optionDownloadButton,
            optionEmbedButton,
            optionCopyrightButton,
            showOptions = false;

        if (this._H5PDiv) {
            var H5PURL = this._H5PDiv.get('innerHTML');
            var fileBaseUrl = M.cfg.wwwroot + '/draftfile.php';
            if (fileBaseUrl == H5PURL.substring(0, fileBaseUrl.length)) {
                fileURL = H5PURL.split("?")[0];

                var parameters = H5PURL.split("?")[1];
                if (parameters) {
                    if (parameters.match(/export=1/)) {
                        optionDownloadButton = 'checked';
                        showOptions = true;
                    }

                    if (parameters.match(/embed=1/)) {
                        optionEmbedButton = 'checked';
                        showOptions = true;
                    }

                    if (parameters.match(/copyright=1/)) {
                        optionCopyrightButton = 'checked';
                        showOptions = true;
                    }
                }
            } else {
                fileURL = H5PURL;
            }
        }

        var template = Y.Handlebars.compile(TEMPLATE),
            content = Y.Node.create(template({
                elementid: this.get('host').get('elementid'),
                CSS: CSS,
                component: COMPONENTNAME,
                canUpload: permissions.canUpload,
                canEmbed: permissions.canEmbed,
                canUploadAndEmbed: permissions.canUploadAndEmbed,
                showOptions: showOptions,
                fileURL: fileURL,
                optionDownloadButton: optionDownloadButton,
                optionEmbedButton: optionEmbedButton,
                optionCopyrightButton: optionCopyrightButton
            }));

        this._form = content;

        // Listen to and act on Dialogue content events.
        this._setEventListeners();

        return content;
    },

    /**
     * Update the dialogue after an h5p was selected in the File Picker.
     *
     * @method _filepickerCallback
     * @param {object} params The parameters provided by the filepicker
     * containing information about the h5p.
     * @private
     */
    _filepickerCallback: function(params) {
        if (params.url !== '') {
            var input = this._form.one(SELECTORS.INPUTH5PFILE);
            input.set('value', params.url);
            this._removeWarnings();
        }
    },

    /**
     * Set event Listeners for Dialogue content actions.
     *
     * @method  _setEventListeners
     * @private
     */
    _setEventListeners: function() {
        var form = this._form;
        var permissions = this._getPermissions();

        form.one(SELECTORS.INPUTSUBMIT).on('click', this._setH5P, this);

        if (permissions.canUpload) {
            form.one(SELECTORS.H5PBROWSER).on('click', function() {
                this.get('host').showFilepicker('h5p', this._filepickerCallback, this);
            }, this);
        }

        if (permissions.canUploadAndEmbed) {
            form.one(SELECTORS.INPUTH5PFILE).on('change', function() {
                this._removeWarnings();
            }, this);
        }
    },

    /**
     * Remove warnings shown in the dialogue.
     *
     * @method _removeWarnings
     * @private
     */
    _removeWarnings: function() {
        var form = this._form;
        form.one(SELECTORS.URLWARNING).setStyle('display', 'none');
        form.one(SELECTORS.CONTENTWARNING).setStyle('display', 'none');
    },

    /**
     * Update the h5p in the contenteditable.
     *
     * @method _setH5P
     * @param {EventFacade} e
     * @private
     */
    _setH5P: function(e) {
        var form = this._form,
            h5phtml,
            host = this.get('host'),
            h5pfile = form.one(SELECTORS.INPUTH5PFILE).get('value'),
            permissions = this._getPermissions();

        e.preventDefault();

        // Check if there are any issues.
        if (this._updateWarning()) {
            return;
        }

        // Focus on the editor in preparation for inserting the H5P.
        host.focus();

        // Add an empty paragraph after new H5P container that can catch the cursor.
        var addParagraphs = true;

        // If a H5P placeholder was selected we can destroy it now.
        if (this._H5PDiv) {
            this._H5PDiv.remove();
            addParagraphs = false;
        }

        if (h5pfile !== '') {
            host.setSelection(this._currentSelection);

            if (h5pfile.startsWith(M.cfg.wwwroot)) {
                // It's a local file.
                var params = '';
                if (permissions.canUpload) {
                    var options = {};
                    if (form.one(SELECTORS.OPTION_DOWNLOAD_BUTTON).get('checked')) {
                        options['export'] = '1';
                    }
                    if (form.one(SELECTORS.OPTION_EMBED_BUTTON).get('checked')) {
                        options.embed = '1';
                    }
                    if (form.one(SELECTORS.OPTION_COPYRIGHT_BUTTON).get('checked')) {
                        options.copyright = '1';
                    }

                    for (var opt in options) {
                        if (params === "" && (h5pfile.indexOf("?") === -1)) {
                            params += "?";
                        } else {
                            params += "&amp;";
                        }
                        params += opt + "=" + options[opt];
                    }
                }

                var h5ptemplate = Y.Handlebars.compile(H5PTEMPLATE);

                h5phtml = h5ptemplate({
                    url: h5pfile + params,
                    addParagraphs: addParagraphs
                });
            } else {
                // It's a URL.
                var urltemplate = Y.Handlebars.compile(H5PTEMPLATE);
                h5phtml = urltemplate({
                    url: h5pfile
                });
            }

            host.insertContentAtFocusPoint(h5phtml);

            this.markUpdated();
        }

        this.getDialogue({
            focusAfterHide: null
        }).hide();
    },

    /**
     * Check if this could be a h5p embed.
     *
     * @method _validEmbed
     * @param {String} str
     * @return {boolean} whether this is a iframe tag.
     * @private
     */
    _validEmbed: function(str) {
        var pattern = new RegExp('^(<iframe).*(<\\/iframe>)'); // Port and path.
        return !!pattern.test(str);
    },

    /**
     * Check if this could be a h5p URL.
     *
     * @method _validURL
     * @param {String} str
     * @return {boolean} whether this is a valid URL.
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
            h5pfile,
            permissions = this._getPermissions();

        if (permissions.canUpload || permissions.canEmbed) {
            h5pfile = form.one(SELECTORS.INPUTH5PFILE).get('value');
            if (h5pfile !== '') {
                form.one(SELECTORS.CONTENTWARNING).setStyle('display', 'none');
                if (h5pfile.startsWith(M.cfg.wwwroot) || this._validURL(h5pfile)) {
                    // Only external URLs have to be validated.
                    form.one(SELECTORS.URLWARNING).setStyle('display', 'none');
                    state = false;
                } else {
                    form.one(SELECTORS.URLWARNING).setStyle('display', 'block');
                    state = true;
                }
            } else {
                form.one(SELECTORS.CONTENTWARNING).setStyle('display', 'block');
                state = true;
            }
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


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
