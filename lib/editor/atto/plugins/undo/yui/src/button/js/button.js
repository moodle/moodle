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
 * Atto text editor undo plugin.
 *
 * @package    editor-undo
 * @copyright  2014 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_undo = M.atto_undo || {

    /**
     * Property used to cache the result of testing the undo/redo browser support.
     *
     * @property browsersupportsundo
     * @type {Boolean} or null
     * @default null
     */
    browsersupportsundo : null,

    /**
     * Handle a click on either button (passed via cmd)
     *
     * @method click_handler
     * @param {Y.Event} The click event
     * @param {String} The id for the editor
     * @param {String} The button clicked (undo or redo)
     */
    click_handler : function(e, elementid, cmd) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        document.execCommand(cmd, false, null);
        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Handle a click on undo
     *
     * @method undo_handler
     * @param {Y.Event} The click event
     * @param {String} The id for the editor
     */
    undo_handler : function(e, elementid) {
        M.atto_undo.click_handler(e, elementid, 'undo');
    },

    /**
     * Handle a click on redo
     *
     * @method redo_handler
     * @param {Y.Event} The click event
     * @param {String} The id for the editor
     */
    redo_handler : function(e, elementid) {
        M.atto_undo.click_handler(e, elementid, 'redo');
    },

    /**
     * Do a feature test to see if undo/redo is buggy in this browser.
     *
     * @method test_undo_support
     * @return {Boolean} true if undo/redo is functional.
     */
    test_undo_support : function() {

        // Check now if other browser supports it.
        // Save the focussed element.
        var activeelement = document.activeElement;

        // Creating a temp div to test if the browser support the undo execCommand.
        var undosupport = false;
        var foo = Y.Node.create('<div id="attoundotesting" contenteditable="true"' +
            'style="position: fixed; top: 0px; height:0px">a</div>');
        Y.one('body').prepend(foo);
        foo.focus();

        try {
            document.execCommand('insertText', false, 'b');
            if (foo.getHTML() === 'ba') {
                document.execCommand('undo', false);
                if (foo.getHTML() === 'a') {
                    document.execCommand('redo', false);
                    if (foo.getHTML() === 'ba') {
                        undosupport = true;
                    }
                }
            }
        } catch (undosupportexception) {
            // IE9 gives us an invalid parameter error on document.execCommand('insertText'...).
            // The try/catch catches when the execCommands fail.
            return false;
        }

        // Remove the tmp contenteditable and reset the focussed element.
        Y.one('body').removeChild(foo);
        activeelement.focus();

        return undosupport;
    },

    /**
     * Add the buttons to the toolbar
     *
     * @method init
     * @param {object} params containing elementid and group
     */
    init : function(params) {

        // Retrieve undobrowsersupport global variable.
        if (M.atto_undo.browsersupportsundo === null) {
            M.atto_undo.browsersupportsundo = M.atto_undo.test_undo_support();
        }

        // Add the undo/redo buttons.
        if (M.atto_undo.browsersupportsundo) {
            // Undo button.
            var iconurl = M.util.image_url('e/undo', 'core');
            M.editor_atto.add_toolbar_button(params.elementid, 'undo', iconurl, params.group, M.atto_undo.undo_handler, 'undo', M.util.get_string('undo', 'atto_undo'));

            // Redo button.
            iconurl = M.util.image_url('e/redo', 'core');
            M.editor_atto.add_toolbar_button(params.elementid, 'undo', iconurl, params.group, M.atto_undo.redo_handler, 'redo', M.util.get_string('redo', 'atto_undo'));
            M.editor_atto.add_button_shortcut({action: 'redo', keys: 89});
        }
    }
};
