YUI.add('moodle-atto_indent-button', function (Y, NAME) {

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
 * @package    atto_indent
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module     moodle-atto_indent-button
 */

/**
 * Atto text editor indent plugin.
 *
 * @namespace M.atto_indent
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_indent').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            // This is adding a <blockquote> which is not ideal but that is the easiest to put in place
            // for now. When disabling the styleWithCSS, some browser will use <blockquote> so we cannot
            // rely on it for <div>s, and that would not work when indenting lists either....
            // Handling it ourselves is even worse as it would require to get a parent and wrap
            // a div with a margin around it. Considering that multiple <p> should end up in the
            // same <div>, that table cells should not be wrapped, and that lists work differently too.
            icon: 'e/increase_indent',
            title: 'indent',
            buttonName: 'indent',
            callback: function() {
                document.execCommand('indent', false, null);

                // Some browsers add style attributes to the blockquote, let's get rid of them.
                // It is really tricky to figure out what blockquote was just added, so removing
                // the styles on all of them seems OK.
                // Eg. Chrome changes the selection after adding the blockquote, so we cannot target it.
                // IE adds a dir attribute to the blockquote too, but it's probably OK to leave it...
                this.editor.all('blockquote').removeAttribute('style');

                // Mark the text as having been updated.
                this.markUpdated();
            }
        });

        this.addBasicButton({
            exec: 'outdent',
            icon: 'e/decrease_indent',
            title: 'outdent'
        });
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
