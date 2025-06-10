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
 * @package    atto_teamsmeeting
 * @copyright  2020 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_teamsmeeting-button
 */

/**
 * Atto text editor teamsmeeting plugin.
 *
 * @namespace M.atto_teamsmeeting
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */
var COMPONENTNAME = 'atto_teamsmeeting',
    CSS = {
        NEWWINDOW: 'atto_teamsmeeting_openinnewwindow',
        URLINPUT: 'atto_teamsmeeting_urlentry'
    },
    SELECTORS = {
        URLINPUT: '.atto_teamsmeeting_urlentry'
    },
    TEMPLATE = '' +
            '<form class="atto_form">' +
                '<div class="meeting-app">' +
                    '<label class="meeting-app-label" for="meetingapp">' +
                    '{{get_string "createteamsmeeting" component}}' +
                    '</label>' +
                    '<iframe id="meetingapp" src="{{appurl}}?url={{clientdomain}}&locale={{locale}}&msession={{msession}}&editor=atto"></iframe>' +
                '</div>' +
                '<div class="mb-1">' +
                    '<label for="{{elementid}}_atto_teamsmeeting_urlentry">{{get_string "meetingurl" component}}</label>' +
                    '<input class="form-control fullwidth url {{CSS.URLINPUT}}" type="url" ' +
                    'id="{{elementid}}_atto_teamsmeeting_urlentry" size="32" disabled="disabled"/>' +
                '</div>' +
                '<div class="form-check">' +
                    '<input type="checkbox" class="form-check-input newwindow" id="{{elementid}}_{{CSS.NEWWINDOW}}"/>' +
                    '<label class="form-check-label" for="{{elementid}}_{{CSS.NEWWINDOW}}">' +
                    '{{get_string "openinnewwindow" component}}' +
                    '</label>' +
                '</div>' +
                '<div class="mdl-align">' +
                    '<br/>' +
                    '<button type="submit" class="btn btn-secondary submit">{{get_string "addlink" component}}</button>' +
                '</div>' +
            '</form>';
Y.namespace('M.atto_teamsmeeting').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

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

    /**
     * Moodle base url to pass for Meetings app.
     *
     * @param _clientdomain
     * @type String
     * @private
     */
    _clientdomain: null,

    /**
     * The Meetings app url.
     *
     * @param _appurl
     * @type String
     * @private
     */
    _appurl: null,

    /**
     * Moodle user language to pass for Meetings app.
     *
     * @param _locale
     * @type String
     * @private
     */
    _locale: null,

    /**
     * Moodle sessionkey to pass for Meetings app.
     *
     * @param _msession
     * @type String
     * @private
     */
    _msession: null,

    initializer: function() {
        this._clientdomain = this.get('clientdomain');
        this._appurl = this.get('appurl');
        this._locale = this.get('locale');
        this._msession = this.get('msession');
        // Add the teamsmeeting button first.
        this.addButton({
            icon: 'icon',
            iconComponent: 'atto_teamsmeeting',
            callback: this._displayDialogue,
            tags: 'a',
            tagMatchRequiresAll: false
        });
    },

    /**
     * Display the teamsmeeting editor.
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
            headerContent: M.util.get_string('createteamsmeeting', COMPONENTNAME),
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
     * If there is selected text and it is part of an anchor teamsmeeting,
     * extract the url (and target) from the teamsmeeting (and set them in the form).
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
                // Make an ajax request to db for links.
                var ajaxurl = M.cfg.wwwroot + '/lib/editor/atto/plugins/teamsmeeting/ajax.php';
                var params = {
                    url: url
                };
                Y.io(ajaxurl, {
                    context: this,
                    data: params,
                    timeout: 500,
                    on: {
                        complete: this._updateIframe
                    }
                });
            }
            if (target === '_blank') {
                this._content.one('.newwindow').setAttribute('checked', 'checked');
            } else {
                this._content.one('.newwindow').removeAttribute('checked');
            }
        }
    },

    /**
     * The teamsmeeting iframe check to get meeting link.
     *
     * @method _meetingCheck
     * @param {EventFacade} e
     * @private
     */
    _meetingcheck: function() {
        if(document.getElementById('meetingapp').contentDocument) {
            var url = document.getElementById('meetingapp').contentDocument.location;
            if (url !== '' && url.pathname.indexOf("teamsmeeting") > -1) {
                var link = this._getqueryvariable('link', url);
                var input = this._content.one('.url');
                input.set('value', link);
            }
        }
    },

    /**
     * The teamsmeeting iframe update.
     *
     * @method _meetingCheck
     * @param {String} id
     * @param {EventFacade} e
     * @private
     */
    _updateIframe:  function(id, data) {
        if (data.status === 200) {
            var dataobject = JSON.parse(data.responseText);
            if (dataobject[2] !== null) {
                var url = dataobject[0]+'?title=' + dataobject[1] + '&link=' + encodeURIComponent(dataobject[2]) +
                    '&options=' + encodeURIComponent(dataobject[3]);
                this._content.one('#meetingapp').set('src', url);
            }
        }
    },

    /**
     * Gets parameter from url.
     *
     * @method _getqueryvariable
     * @param {string} variable
     * @param {object} url
     * @private
     */
    _getqueryvariable: function(variable, url) {
        var query = url.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split('=');
            if (decodeURIComponent(pair[0]) == variable) {
                return decodeURIComponent(pair[1]);
            }
        }
    },

    /**
     * The teamsmeeting was inserted, so make changes to the editor source.
     *
     * @method _setteamsmeeting
     * @param {EventFacade} e
     * @private
     */
    _setteamsmeeting: function(e) {
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

            // Add the teamsmeeting.
            this._setteamsmeetingOnSelection(value);

            this.markUpdated();
        }
    },

    /**
     * Final step setting the anchor on the selection.
     *
     * @private
     * @method _setteamsmeetingOnSelection
     * @param  {String} url URL the teamsmeeting will point to.
     * @return {Node} The added Node.
     */
    _setteamsmeetingOnSelection: function(url) {
        var host = this.get('host'),
            teamsmeeting,
            selectednode,
            target,
            anchornodes;

        this.editor.focus();
        host.setSelection(this._currentSelection);

        if (this._currentSelection[0].collapsed) {
            // Firefox cannot add teamsmeetings when the selection is empty so we will add it manually.
            teamsmeeting = Y.Node.create('<a>' + url + '</a>');
            teamsmeeting.setAttribute('href', url);

            // Add the node and select it to replicate the behaviour of execCommand.
            selectednode = host.insertContentAtFocusPoint(teamsmeeting.get('outerHTML'));
            host.setSelection(host.getSelectionFromNode(selectednode));
        } else {
            document.execCommand('unlink', false, null);
            document.execCommand('createlink', false, url);

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
        var template = Y.Handlebars.compile(TEMPLATE);

        this._content = Y.Node.create(template({
            clientdomain: this._clientdomain,
            appurl: this._appurl,
            locale: this._locale,
            msession: this._msession,
            component: COMPONENTNAME,
            CSS: CSS
        }));

        this._content.one('.submit').on('click', this._setteamsmeeting, this);

        this._content.one('iframe').on('load', this._meetingcheck, this);

        return this._content;
    }
}, {
    ATTRS: {
        /**
         * The domain of client.
         *
         * @attribute allowedmethods
         * @type String
         */
        clientdomain: {
            value: null
        },
        /**
         * The meeting app url.
         *
         * @attribute allowedmethods
         * @type String
         */
        appurl: {
            value: null
        },
        /**
         * User locale.
         *
         * @attribute allowedmethods
         * @type String
         */
        locale: {
            value: null
        },
        /**
         * User locale.
         *
         * @attribute allowedmethods
         * @type String
         */
        msession: {
            value: null
        }
    }
});
