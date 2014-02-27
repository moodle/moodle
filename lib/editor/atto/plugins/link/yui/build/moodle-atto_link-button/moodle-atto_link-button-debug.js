YUI.add('moodle-atto_link-button', function (Y, NAME) {

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
 * Selectors.
 *
 * @type {Object}
 */
var SELECTORS = {
    TAGS : 'a'
};

/**
 * Atto text editor link plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_link = M.atto_link || {
    /**
     * The window used to get the link details.
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
        M.atto_link.selection = M.editor_atto.get_selection();
        if (M.atto_link.selection !== false && (!M.atto_link.selection.collapsed)) {
            var dialogue;
            if (!M.atto_link.dialogue) {
                dialogue = new M.core.dialogue({
                    visible: false,
                    modal: true,
                    close: true,
                    draggable: true
                });
            } else {
                dialogue = M.atto_link.dialogue;
            }

            dialogue.render();
            dialogue.set('bodyContent', M.atto_link.get_form_content(elementid));
            dialogue.set('headerContent', M.util.get_string('createlink', 'atto_link'));

            M.atto_link.resolve_anchors();

            dialogue.show();
            M.atto_link.dialogue = dialogue;
        }
    },

    /**
     * Add this button to the form.
     *
     * @method init
     * @param {Object} params
     */
    init : function(params) {
        var iconurl = M.util.image_url('e/insert_edit_link', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'link', iconurl, params.group, this.display_chooser);
        // Attach an event listner to watch for "changes" in the contenteditable.
        // This includes cursor changes, we check if the button should be active or not, based
        // on the text selection.
        M.editor_atto.on('atto:selectionchanged', function(e) {
            if (M.editor_atto.selection_filter_matches(e.elementid, SELECTORS.TAGS, e.selectedNodes)) {
                M.editor_atto.add_widget_highlight(e.elementid, 'link');
            } else {
                M.editor_atto.remove_widget_highlight(e.elementid, 'link');
            }
        });
    },

    /**
     * If there is selected text and it is part of an anchor link,
     * extract the url (and target) from the link (and set them in the form).
     *
     * @method resolve_anchors
     */
    resolve_anchors : function() {
        // Find the first anchor tag in the selection.
        var selectednode = M.editor_atto.get_selection_parent_node(),
            anchornodes,
            anchornode,
            url;

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectednode) {
            return;
        }

        anchornodes = M.atto_link.find_selected_anchors(Y.one(selectednode));

        if (anchornodes.length > 0) {
            anchornode = anchornodes[0];
            M.atto_link.selection = M.editor_atto.get_selection_from_node(anchornode);
            url = anchornode.getAttribute('href');
            target = anchornode.getAttribute('target');
            if (url !== '') {
                Y.one('#atto_link_urlentry').set('value', url);
            }
            if (target === '_blank') {
                Y.one('#atto_link_openinnewwindow').set('checked', 'checked');
            } else {
                Y.one('#atto_link_openinnewwindow').set('checked', '');
            }
        }
    },

    /**
     * Open the repository file picker.
     *
     * @method open_filepicker
     * @param Event e
     */
    open_filepicker : function(e) {
        var elementid = this.getAttribute('data-editor');
        e.preventDefault();

        M.editor_atto.show_filepicker(elementid, 'link', M.atto_link.filepicker_callback);
    },

    /**
     * Called by the file picker when a link has been chosen.
     *
     * @method filepicker_callback
     * @param {Object} params - contains selected url.
     */
    filepicker_callback : function(params) {
        M.atto_link.dialogue.hide();
        if (params.url !== '') {
            M.editor_atto.set_selection(M.atto_link.selection);
            document.execCommand('unlink', false, null);
            document.execCommand('createLink', false, params.url);
        }
    },

    /**
     * The OK button has been pressed - make the changes to the source.
     *
     * @method set_link
     * @param Event e
     */
    set_link : function(e, elementid) {
        var input,
            target,
            selectednode,
            anchornodes,
            value;

        e.preventDefault();
        M.atto_link.dialogue.hide();

        input = e.currentTarget.ancestor('.atto_form').one('input[type=url]');

        value = input.get('value');
        if (value !== '') {
            M.editor_atto.set_selection(M.atto_link.selection);
            document.execCommand('unlink', false, null);
            document.execCommand('createLink', false, value);

            // Now set the target.
            selectednode = M.editor_atto.get_selection_parent_node();

            // Note this is a document fragment and YUI doesn't like them.
            if (!selectednode) {
                return;
            }

            anchornodes = M.atto_link.find_selected_anchors(Y.one(selectednode));
            Y.Array.each(anchornodes, function(anchornode) {
                target = e.currentTarget.ancestor('.atto_form').one('input[type=checkbox]');
                if (target.get('checked')) {
                    anchornode.setAttribute('target', '_blank');
                } else {
                    anchornode.removeAttribute('target');
                }
            });
            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        }
    },

    /**
     * Look up and down for the nearest anchor tags that are least partly contained in the selection.
     *
     * @method find_selected_anchors
     * @param Node node
     * @return Node|false
     */
    find_selected_anchors : function(node) {
        var tagname = node.get('tagName'), hit, hits;
        // Direct hit.
        if (tagname && tagname.toLowerCase() === 'a') {
            return [node];
        }
        // Search down but check that each node is part of the selection.
        hits = [];
        node.all('a').each(function(n) {
            if (!hit && M.editor_atto.selection_contains_node(n)) {
                hits.push(n);
            }
        });
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
     * Return the HTML of the form to show in the dialogue.
     *
     * @method get_form_content
     * @param string elementid
     * @return string
     */
    get_form_content : function(elementid) {
        var html = '<form class="atto_form">' +
                   '<label for="atto_link_urlentry">' + M.util.get_string('enterurl', 'atto_link') +
                   '</label>' +
                   '<input class="fullwidth" type="url" value="" id="atto_link_urlentry" size="32"/><br/>';
        if (M.editor_atto.can_show_filepicker(elementid, 'link')) {
            html += '<button id="openlinkbrowser" data-editor="' + Y.Escape.html(elementid) + '" type="button" >' +
                    M.util.get_string('browserepositories', 'atto_link') +
                    '</button>' +
                    '<br/>';
        }
        html += '<input type="checkbox" id="atto_link_openinnewwindow"/>' +
                '<label class="sameline" for="atto_link_openinnewwindow">' + M.util.get_string('openinnewwindow', 'atto_link') +
                '</label>' +
                '<br/>' +
                '<div class="mdl-align">' +
                '<br/>' +
                '<button type="submit" id="atto_link_urlentrysubmit">' +
                M.util.get_string('createlink', 'atto_link') +
                '</button>' +
                '</div>' +
                '</form>';

        var content = Y.Node.create(html);

        content.one('#atto_link_urlentrysubmit').on('click', M.atto_link.set_link, this, elementid);
        if (M.editor_atto.can_show_filepicker(elementid, 'link')) {
            content.one('#openlinkbrowser').on('click', M.atto_link.open_filepicker);
        }
        return content;
    }
};


}, '@VERSION@', {"requires": ["node", "escape"]});
