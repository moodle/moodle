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

/*
 * @package    atto_accessibilityhelper
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_accessibilityhelper-button
 */

/**
 * Atto text editor accessibilityhelper plugin.
 *
 * This plugin adds some functions to do things that screen readers do not do well.
 * Specifically, listing the active styles for the selected text,
 * listing the images in the page, listing the links in the page.
 *
 *
 * @namespace M.atto_accessibilityhelper
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENT = 'atto_accessibilityhelper',
    TEMPLATE = '' +
        // The list of styles.
        '<div><p id="{{elementid}}_{{CSS.STYLESLABEL}}">' +
            '{{get_string "liststyles" component}}<br/>' +
            '<span aria-labelledby="{{elementid}}_{{CSS.STYLESLABEL}}" />' +
        '</p></div>' +
        '<span class="listStyles"></span>' +

        '<p id="{{elementid}}_{{CSS.LINKSLABEL}}">' +
            '{{get_string "listlinks" component}}<br/>' +
            '<span aria-labelledby="{{elementid}}_{{CSS.LINKSLABEL}}"/>' +
        '</p>' +
        '<span class="listLinks"></span>' +

        '<p id="{{elementid}}_{{CSS.IMAGESLABEL}}">' +
            '{{get_string "listimages" component}}<br/>' +
            '<span aria-labelledby="{{elementid}}_{{CSS.IMAGESLABEL}}"/>' +
        '</p>' +
        '<span class="listImages"></span>',

    CSS = {
        STYLESLABEL: COMPONENT + '_styleslabel',
        LINKSLABEL: COMPONENT + '_linkslabel',
        IMAGESLABEL: COMPONENT + '_imageslabel'
    };

Y.namespace('M.atto_accessibilityhelper').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * The warnings which are displayed.
     *
     * @property _displayedWarnings
     * @type Object
     * @private
     */
    _displayedWarnings: {},

    initializer: function() {
        this.addButton({
            icon: 'e/screenreader_helper',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the Accessibility Helper tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENT),
            width: '800px',
            focusAfterHide: true
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
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
        var template = Y.Handlebars.compile(TEMPLATE),
            content = Y.Node.create(template({
                CSS: CSS,
                component: COMPONENT
            }));

        // Add the data.
        content.one('.listStyles')
                .empty()
                .appendChild(this._listStyles());
        content.one('.listLinks')
                .empty()
                .appendChild(this._listLinks());
        content.one('.listImages')
                .empty()
                .appendChild(this._listImages());

        // Add ability to select problem areas in the editor.
        content.delegate('click', function(e) {
            e.preventDefault();

            var host = this.get('host'),
                index = e.target.getAttribute("data-index"),
                node = this._displayedWarnings[index],
                dialogue = this.getDialogue();


            if (node) {
                // Clear the dialogue's focusAfterHide to ensure we focus
                // on the selection.
                dialogue.set('focusAfterHide', null);
                host.setSelection(host.getSelectionFromNode(node));
            }

            // Hide the dialogue.
            dialogue.hide();

        }, 'a', this);

        return content;
    },

    /**
     * List the styles present for the selection.
     *
     * @method _listStyles
     * @return {String} The list of styles in use.
     * @private
     */
    _listStyles: function() {
        // Clear the status node.
        var list = [],
            host = this.get('host'),
            current = host.getSelectionParentNode(),
            tagname;

        if (current) {
            current = Y.one(current);
        }

        while (current && (current !== this.editor)) {
            tagname = current.get('tagName');
            if (typeof tagname !== 'undefined') {
                list.push(Y.Escape.html(tagname));
            }
            current = current.ancestor();
        }
        if (list.length === 0) {
            list.push(M.util.get_string('nostyles', COMPONENT));
        }

        list.reverse();

        // Append the list of current styles.
        return list.join(', ');
    },

    /**
     * List the links for the current editor
     *
     * @method _listLinks
     * @return {string}
     * @private
     */
    _listLinks: function() {
        var list = Y.Node.create('<ol />'),
            listitem,
            selectlink;

        this.editor.all('a').each(function(link) {
            selectlink = Y.Node.create('<a href="#" title="' +
                    M.util.get_string('selectlink', COMPONENT) + '">' +
                    Y.Escape.html(link.get('text')) +
                    '</a>');

            selectlink.setData('sourcelink', link);
            selectlink.on('click', this._linkSelected, this);

            listitem = Y.Node.create('<li></li>');
            listitem.appendChild(selectlink);

            list.appendChild(listitem);
        }, this);

        if (!list.hasChildNodes()) {
            list.append('<li>' + M.util.get_string('nolinks', COMPONENT) + '</li>');
        }

        // Append the list of current styles.
        return list;
    },

    /**
     * List the images used in the editor.
     *
     * @method _listImages
     * @return {Node} A Node containing all of the images present in the editor.
     * @private
     */
    _listImages: function() {
        var list = Y.Node.create('<ol/>'),
            listitem,
            selectimage;

        this.editor.all('img').each(function(image) {
            // Get the alt or title or img url of the image.
            var imgalt = image.getAttribute('alt');
            if (imgalt === '') {
                imgalt = image.getAttribute('title');
                if (imgalt === '') {
                    imgalt = image.getAttribute('src');
                }
            }

            selectimage = Y.Node.create('<a href="#" title="' +
                    M.util.get_string('selectimage', COMPONENT) + '">' +
                    Y.Escape.html(imgalt) +
                    '</a>');

            selectimage.setData('sourceimage', image);
            selectimage.on('click', this._imageSelected, this);

            listitem = Y.Node.create('<li></li>');
            listitem.append(selectimage);
            list.append(listitem);
        }, this);
        if (!list.hasChildNodes()) {
            list.append('<li>' + M.util.get_string('noimages', COMPONENT) + '</li>');
        }

        // Append the list of current styles.
        return list;
    },

    /**
     * Event handler for selecting an image.
     *
     * @method _imageSelected
     * @param {EventFacade} e
     * @private
     */
    _imageSelected: function(e) {
        e.preventDefault();

        this.getDialogue({
            focusAfterNode: null
        }).hide();

        var host = this.get('host'),
            target = e.target.getData('sourceimage');

        this.editor.focus();
        host.setSelection(host.getSelectionFromNode(target));
    },

    /**
     * Event handler for selecting a link.
     *
     * @method _linkSelected
     * @param {EventFacade} e
     * @private
     */
    _linkSelected: function(e) {
        e.preventDefault();

        this.getDialogue({
            focusAfterNode: null
        }).hide();

        var host = this.get('host'),
            target = e.target.getData('sourcelink');

        this.editor.focus();
        host.setSelection(host.getSelectionFromNode(target));
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
