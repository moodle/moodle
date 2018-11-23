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
 * @package    atto_link
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_link-button
 */

/**
 * Atto text editor link plugin.
 *
 * @namespace M.atto_link
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_link',
    CSS = {
        NEWWINDOW: 'atto_link_openinnewwindow',
        URLINPUT: 'atto_link_urlentry'
    },
    SELECTORS = {
        URLINPUT: '.atto_link_urlentry'
    },
    TEMPLATE = '' +
            '<form class="atto_form">' +
                '{{#if showFilepicker}}' +
                    '<label for="{{elementid}}_atto_link_urlentry">{{get_string "enterurl" component}}</label>' +
                    '<div class="input-group input-append w-100 m-b-1">' +
                        '<input class="form-control url {{CSS.URLINPUT}}" type="url" ' +
                        'id="{{elementid}}_atto_link_urlentry"/>' +
                        '<span class="input-group-append">' +
                            '<button class="btn btn-default openlinkbrowser" type="button">' +
                            '{{get_string "browserepositories" component}}</button>' +
                        '</span>' +
                    '</div>' +
                '{{else}}' +
                    '<div class="m-b-1">' +
                        '<label for="{{elementid}}_atto_link_urlentry">{{get_string "enterurl" component}}</label>' +
                        '<input class="form-control fullwidth url {{CSS.URLINPUT}}" type="url" ' +
                        'id="{{elementid}}_atto_link_urlentry" size="32"/>' +
                    '</div>' +
                '{{/if}}' +
                '<div class="form-check">' +
                    '<input type="checkbox" class="form-check-input newwindow" id="{{elementid}}_{{CSS.NEWWINDOW}}"/>' +
                    '<label class="form-check-label" for="{{elementid}}_{{CSS.NEWWINDOW}}">' +
                    '{{get_string "openinnewwindow" component}}' +
                    '</label>' +
                '</div>' +
                '<div class="mdl-align">' +
                    '<br/>' +
                    '<button type="submit" class="btn btn-default submit">{{get_string "createlink" component}}</button>' +
                '</div>' +
            '</form>';
Y.namespace('M.atto_link').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

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
     * A reference to the dialogue content.
     *
     * @property _content
     * @type Node
     * @private
     */
    _content: null,

    initializer: function() {
        // Add the link button first.
        this.addButton({
            icon: 'e/insert_edit_link',
            keys: '75',
            callback: this._displayDialogue,
            tags: 'a',
            tagMatchRequiresAll: false
        });

        // And then the unlink button.
        this.addButton({
            buttonName: 'unlink',
            callback: this._unlink,
            icon: 'e/remove_link',
            title: 'unlink',

            // Watch the following tags and add/remove highlighting as appropriate:
            tags: 'a',
            tagMatchRequiresAll: false
        });
    },

    /**
     * Display the link editor.
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

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('createlink', COMPONENTNAME),
            width: 'auto',
            focusAfterHide: true,
            focusOnShowSelector: SELECTORS.URLINPUT
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent());

        // Resolve anchors in the selected text.
        this._resolveAnchors();
        dialogue.show();
    },

    /**
     * If there is selected text and it is part of an anchor link,
     * extract the url (and target) from the link (and set them in the form).
     *
     * @method _resolveAnchors
     * @private
     */
    _resolveAnchors: function() {
        // Find the first anchor tag in the selection.
        var selectednode = this.get('host').getSelectionParentNode(),
            anchornodes,
            anchornode,
            url,
            target;

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectednode) {
            return;
        }

        anchornodes = this._findSelectedAnchors(Y.one(selectednode));
        if (anchornodes.length > 0) {
            anchornode = anchornodes[0];
            this._currentSelection = this.get('host').getSelectionFromNode(anchornode);
            url = anchornode.getAttribute('href');
            target = anchornode.getAttribute('target');
            if (url !== '') {
                this._content.one('.url').setAttribute('value', url);
            }
            if (target === '_blank') {
                this._content.one('.newwindow').setAttribute('checked', 'checked');
            } else {
                this._content.one('.newwindow').removeAttribute('checked');
            }
        }
    },

    /**
     * Update the dialogue after a link was selected in the File Picker.
     *
     * @method _filepickerCallback
     * @param {object} params The parameters provided by the filepicker
     * containing information about the link.
     * @private
     */
    _filepickerCallback: function(params) {
        this.getDialogue()
                .set('focusAfterHide', null)
                .hide();

        if (params.url !== '') {
            // Add the link.
            this._setLinkOnSelection(params.url);

            // And mark the text area as updated.
            this.markUpdated();
        }
    },

    /**
     * The link was inserted, so make changes to the editor source.
     *
     * @method _setLink
     * @param {EventFacade} e
     * @private
     */
    _setLink: function(e) {
        var input,
            value;

        e.preventDefault();
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        input = this._content.one('.url');

        value = input.get('value');
        if (value !== '') {

            // We add a prefix if it is not already prefixed.
            value = value.trim();
            var expr = new RegExp(/^[a-zA-Z]*\.*\/|^#|^[a-zA-Z]*:/);
            if (!expr.test(value)) {
                value = 'http://' + value;
            }

            // Add the link.
            this._setLinkOnSelection(value);

            this.markUpdated();
        }
    },

    /**
     * Final step setting the anchor on the selection.
     *
     * @private
     * @method _setLinkOnSelection
     * @param  {String} url URL the link will point to.
     * @return {Node} The added Node.
     */
    _setLinkOnSelection: function(url) {
        var host = this.get('host'),
            link,
            selectednode,
            target,
            anchornodes;

        this.editor.focus();
        host.setSelection(this._currentSelection);

        if (this._currentSelection[0].collapsed) {
            // Firefox cannot add links when the selection is empty so we will add it manually.
            link = Y.Node.create('<a>' + url + '</a>');
            link.setAttribute('href', url);

            // Add the node and select it to replicate the behaviour of execCommand.
            selectednode = host.insertContentAtFocusPoint(link.get('outerHTML'));
            host.setSelection(host.getSelectionFromNode(selectednode));
        } else {
            document.execCommand('unlink', false, null);
            document.execCommand('createLink', false, url);

            // Now set the target.
            selectednode = host.getSelectionParentNode();
        }

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectednode) {
            return;
        }

        anchornodes = this._findSelectedAnchors(Y.one(selectednode));
        // Add new window attributes if requested.
        Y.Array.each(anchornodes, function(anchornode) {
            target = this._content.one('.newwindow');
            if (target.get('checked')) {
                anchornode.setAttribute('target', '_blank');
            } else {
                anchornode.removeAttribute('target');
            }
        }, this);

        return selectednode;
    },

    /**
     * Look up and down for the nearest anchor tags that are least partly contained in the selection.
     *
     * @method _findSelectedAnchors
     * @param {Node} node The node to search under for the selected anchor.
     * @return {Node|Boolean} The Node, or false if not found.
     * @private
     */
    _findSelectedAnchors: function(node) {
        var tagname = node.get('tagName'),
            hit, hits;

        // Direct hit.
        if (tagname && tagname.toLowerCase() === 'a') {
            return [node];
        }

        // Search down but check that each node is part of the selection.
        hits = [];
        node.all('a').each(function(n) {
            if (!hit && this.get('host').selectionContainsNode(n)) {
                hits.push(n);
            }
        }, this);
        if (hits.length > 0) {
            return hits;
        }
        // Search up.
        hit = node.ancestor('a');
        if (hit) {
            return [hit];
        }
        return [];
    },

    /**
     * Generates the content of the dialogue.
     *
     * @method _getDialogueContent
     * @return {Node} Node containing the dialogue content
     * @private
     */
    _getDialogueContent: function() {
        var canShowFilepicker = this.get('host').canShowFilepicker('link'),
            template = Y.Handlebars.compile(TEMPLATE);

        this._content = Y.Node.create(template({
            showFilepicker: canShowFilepicker,
            component: COMPONENTNAME,
            CSS: CSS
        }));

        this._content.one('.submit').on('click', this._setLink, this);
        if (canShowFilepicker) {
            this._content.one('.openlinkbrowser').on('click', function(e) {
                e.preventDefault();
                this.get('host').showFilepicker('link', this._filepickerCallback, this);
            }, this);
        }

        return this._content;
    },

    /**
     * Unlinks the current selection.
     * If the selection is empty (e.g. the cursor is placed within a link),
     * then the whole link is unlinked.
     *
     * @method _unlink
     * @private
     */
    _unlink: function() {
        var host = this.get('host'),
            range = host.getSelection();

        if (range && range.length) {
            if (range[0].startOffset === range[0].endOffset) {
                // The cursor was placed in the editor but there was no selection - select the whole parent.
                var nodes = host.getSelectedNodes();
                if (nodes) {
                    // We need to unlink each anchor individually - we cannot select a range because it may only consist of a
                    // fragment of an anchor. Selecting the parent would be dangerous because it may contain other links which
                    // would then be unlinked too.
                    nodes.each(function(node) {
                        // We need to select the whole anchor node for this to work in some browsers.
                        // We only need to search up because getSeletedNodes returns all Nodes in the selection.
                        var anchor = node.ancestor('a', true);
                        if (anchor) {
                            // Set the selection to the whole of the first anchro.
                            host.setSelection(host.getSelectionFromNode(anchor));

                            // Call the browser unlink.
                            document.execCommand('unlink', false, null);
                        }
                    }, this);

                    // And mark the text area as updated.
                    this.markUpdated();
                }
            } else {
                // Call the browser unlink.
                document.execCommand('unlink', false, null);

                // And mark the text area as updated.
                this.markUpdated();
            }
        }
    }
});
