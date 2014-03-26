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
 * A Menu for the Atto editor.
 *
 * @module     moodle-editor_atto-menu
 * @submodule  menu-base
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A Menu for the Atto editor used in Moodle.
 *
 * This is a drop down list of buttons triggered (and aligned to) a
 * location.
 *
 * @namespace M.editor_atto
 * @class Menu
 * @main
 * @constructor
 * @extends M.core.dialogue
 */
Menu = function() {
    Menu.superclass.constructor.apply(this, arguments);
};

Y.extend(Menu, M.core.dialogue, {

    initializer: function(config) {
        var body, headertext, bb;
        Menu.superclass.initializer.call(this, config);

        bb = this.get('boundingBox');
        bb.addClass('editor_atto_controlmenu');

        // Close the menu when clicked outside (excluding the button that opened the menu).
        body = this.bodyNode;

        headertext = Y.Node.create('<h3/>')
                .addClass('accesshide')
                .setHTML(this.get('headerText'));
        body.prepend(headertext);

        this.get('hideOn');
    }

}, {
    NAME: "menu",
    ATTRS: {
        /**
         * The header for the drop down (only accessible to screen readers).
         *
         * @attribute headerText
         * @type String
         * @default ''
         */
        headerText: {
            value: ''
        }

    }
});

Y.Base.modifyAttrs(Menu, {
    /**
     * The width for this menu.
     *
     * @attribute width
     * @default 'auto'
     */
    width: {
        value: 'auto'
    },

    /**
     * TODO - chekc that this is needed
     * The footer content.
     *
     * @attribute footerContent
     * @default ''
     */
    footerContent: {
        value: ''
    },

    /**
     * When to hide this menu.
     *
     * By default, this attribute consists of:
     * <ul>
     * <li>an object which will cause the menu to hide when the user clicks outside of the menu</li>
     * </ul>
     *
     * @attribute hideOn
     */
    hideOn: {
        value: [
            {
                eventName: 'clickoutside'
            }
        ]
    }
});

Y.namespace('M.editor_atto').Menu = Menu;
