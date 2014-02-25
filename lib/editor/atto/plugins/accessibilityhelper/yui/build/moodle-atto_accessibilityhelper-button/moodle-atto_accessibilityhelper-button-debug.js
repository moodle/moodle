YUI.add('moodle-atto_accessibilityhelper-button', function (Y, NAME) {

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
* CSS classes and IDs.
*
* @type {Object}
*/
var CSS = {
    STYLESLABEL: 'atto_accessibilityhelper_styleslabel',
    LISTSTYLES: 'atto_accessibilityhelper_liststyles',
    LINKSLABEL: 'atto_accessibilityhelper_linkslabel',
    LISTLINKS: 'atto_accessibilityhelper_listlinks',
    IMAGESLABEL: 'atto_accessibilityhelper_imageslabel',
    LISTIMAGES: 'atto_accessibilityhelper_listimages'
};

 /**
 * Selectors.
 *
 * @type {Object}
 */
var SELECTORS = {
    LISTSTYLES: '#atto_accessibilityhelper_liststyles',
    LISTLINKS: '#atto_accessibilityhelper_listlinks',
    LISTIMAGES: '#atto_accessibilityhelper_listimages'
};
/**
 * Atto text editor accessibilityhelper plugin.
 *
 * This plugin adds some functions to do things that screen readers do not do well.
 * Specifically, listing the active styles for the selected text,
 * listing the images in the page, listing the links in the page.
 *
 * @package    editor-atto
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_accessibilityhelper = M.atto_accessibilityhelper || {
    /**
     * The window used to display the accessibility ui.
     *
     * @property dialogue
     * @type M.core.dialogue
     * @default null
     */
    dialogue : null,

    /**
     * Display the ui dialogue.
     *
     * @method init
     * @param Event e
     * @param string elementid
     */
    display_ui : function(e, elementid) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        var dialogue;
        if (!M.atto_accessibilityhelper.dialogue) {
            dialogue = new M.core.dialogue({
                visible: false,
                modal: true,
                close: true,
                draggable: true
            });
            dialogue.set('headerContent', M.util.get_string('pluginname', 'atto_accessibilityhelper'));
            dialogue.render();
        } else {
            dialogue = M.atto_accessibilityhelper.dialogue;
        }

        dialogue.set('bodyContent', M.atto_accessibilityhelper.get_content(elementid));
        dialogue.centerDialogue();

        dialogue.show();
        M.atto_accessibilityhelper.dialogue = dialogue;
    },

    /**
     * Add this button to the form.
     *
     * @method init
     * @param {Object} params
     */
    init : function(params) {
        var iconurl = M.util.image_url('e/visual_aid', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'accessibilityhelper', iconurl, params.group, this.display_ui);
    },

    /**
     * Event handler for selecting an image.
     *
     * @method image_selected
     * @param Event e
     * @param string elementid
     */
    image_selected : function(e, elementid) {
        e.preventDefault();

        M.atto_accessibilityhelper.dialogue.hide();

        var image = e.target.getData('sourceimage');
        var selection = M.editor_atto.get_selection_from_node(image);

        M.editor_atto.selections[elementid] = selection;
        M.editor_atto.focus(elementid);
    },

    /**
     * List the images for the current editor
     *
     * @method list_images
     * @param string elementid
     * @return String
     */
    list_images : function(elementid) {

        var list = Y.Node.create('<ol/>');

        var editable = M.editor_atto.get_editable_node(elementid),
            listitem, selectimage;

        editable.all('img').each(function(image) {

            // Get the alt or title or img url of the image.
            var imgalt = image.getAttribute('alt');
            if (imgalt === '') {
                imgalt = image.getAttribute('title');
                if (imgalt === '') {
                    imgalt = image.getAttribute('src');
                }
            }

            selectimage = Y.Node.create('<a href="#" title="' +
                                       M.util.get_string('selectimage', 'atto_accessibilityhelper') + '">' +
                                       Y.Escape.html(imgalt) +
                                       '</a>');

            selectimage.setData('sourceimage', image);
            selectimage.on('click', this.image_selected, this, elementid);

            listitem = Y.Node.create('<li></li>');
            listitem.append(selectimage);
            list.append(listitem);
        }, this);
        if (!list.hasChildNodes()) {
            list.append('<li>' + M.util.get_string('noimages', 'atto_accessibilityhelper') + '</li>');
        }
        // Append the list of current styles.
        return list;
    },

    /**
     * Event handler for selecting a link.
     *
     * @method link_selected
     * @param Event e
     * @param string elementid
     */
    link_selected : function(e, elementid) {
        e.preventDefault();

        M.atto_accessibilityhelper.dialogue.hide();

        var link = e.target.getData('sourcelink');
        var selection = M.editor_atto.get_selection_from_node(link);

        M.editor_atto.selections[elementid] = selection;
        M.editor_atto.focus(elementid);
    },

    /**
     * List the links for the current editor
     *
     * @method list_links
     * @param string elementid
     * @return String
     */
    list_links : function(elementid) {

        var list = Y.Node.create('<ol/>');

        var editable = M.editor_atto.get_editable_node(elementid),
            listitem, selectlink;

        editable.all('a').each(function(link) {
            selectlink = Y.Node.create('<a href="#" title="' +
                                       M.util.get_string('selectlink', 'atto_accessibilityhelper') + '">' +
                                       Y.Escape.html(link.get('text')) +
                                       '</a>');

            selectlink.setData('sourcelink', link);
            selectlink.on('click', this.link_selected, this, elementid);

            listitem = Y.Node.create('<li></li>');
            listitem.append(selectlink);
            list.append(listitem);
        }, this);
        if (!list.hasChildNodes()) {
            list.append('<li>' + M.util.get_string('nolinks', 'atto_accessibilityhelper') + '</li>');
        }
        // Append the list of current styles.
        return list;
    },

    /**
     * List the styles for the current selection.
     *
     * @method list_styles
     * @param string elementid
     * @return String
     */
    list_styles : function(elementid) {

        // Clear the status node.

        var list = [];

        var current = M.editor_atto.get_selection_parent_node();
        var editable = M.editor_atto.get_editable_node(elementid);
        var tagname;

        if (current) {
            current = Y.one(current);
        }
        while (current && (current !== editable)) {
            tagname = current.get('tagName');
            if (typeof tagname !== 'undefined') {
                list.push(Y.Escape.html(tagname));
            }
            current = current.ancestor();
        }
        if (list.length === 0) {
            list.push(M.util.get_string('nostyles', 'atto_accessibilityhelper'));
        }

        list.reverse();
        // Append the list of current styles.
        return list.join(', ');
    },

    /**
     * Return the HTML of the form to show in the dialogue.
     *
     * @method get_content
     * @param string elementid
     * @return string
     */
    get_content : function(elementid) {
        // Current styles.
        var html = '<div><p id="' + CSS.STYLESLABEL + '">' +
                M.util.get_string('liststyles', 'atto_accessibilityhelper') +
                '<br/>' +
                '<span id="' + CSS.LISTSTYLES + '" ' +
                'aria-labelledby="' + CSS.STYLESLABEL + '"/></p></div>';


        var content = Y.Node.create(html);

        content.one(SELECTORS.LISTSTYLES).append(this.list_styles(elementid));

        // Current links.
        html = '<p id="' + CSS.LINKSLABEL + '">' +
                M.util.get_string('listlinks', 'atto_accessibilityhelper') +
                '<br/>' +
                '<span id="' + CSS.LISTLINKS + '" ' +
                'aria-labelledby="' + CSS.LINKSLABEL + '"/></p>';

        content.append(html);
        content.one(SELECTORS.LISTLINKS).append(this.list_links(elementid));

        // Current images.
        html = '<p id="' + CSS.IMAGESLABEL + '">' +
                M.util.get_string('listimages', 'atto_accessibilityhelper') +
                '<br/>' +
                '<span id="' + CSS.LISTIMAGES + '" ' +
                'aria-labelledby="' + CSS.IMAGESLABEL + '"/></p>';

        content.append(html);
        content.one(SELECTORS.LISTIMAGES).append(this.list_images(elementid));
        return content;
    }

};


}, '@VERSION@', {"requires": ["node", "escape"]});
