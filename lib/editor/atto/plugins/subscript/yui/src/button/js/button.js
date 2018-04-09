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
 * @package    atto_subscript
 * @copyright  2014 Rosiana Wijaya <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_subscript-button
 */

/**
 * Atto text editor subscript plugin.
 *
 * @namespace M.atto_subscript
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_subscript').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A rangy object to alter CSS classes.
     *
     * @property _subscriptApplier
     * @type Object
     * @private
     */
    _subscriptApplier: null,

    /**
     * A rangy object to alter CSS classes.
     *
     * @property _superscriptApplier
     * @type Object
     * @private
     */
    _superscriptApplier: null,

    initializer: function() {
        this.addButton({
            buttonName: 'subscript',
            callback: this.toggleSubscript,
            icon: 'e/subscript',
            inlineFormat: true,

            // Watch the following tags and add/remove highlighting as appropriate:
            tags: 'sub'
        });
        this._subscriptApplier = window.rangy.createClassApplier("editor-subscript");
        this._superscriptApplier = window.rangy.createClassApplier("editor-superscript");
    },

    /**
     * Toggle subscripts in selection
     *
     * @method toggleSubscript
     */
    toggleSubscript: function() {
        // Replace all the sub and sup tags.
        this.get('host').changeToCSS('sup', 'editor-superscript');
        this.get('host').changeToCSS('sub', 'editor-subscript');

        // Remove all superscripts inselection and toggle subscript.
        this._superscriptApplier.undoToSelection();
        this._subscriptApplier.toggleSelection();

        // Replace CSS classes with tags.
        this.get('host').changeToTags('editor-subscript', 'sub');
        this.get('host').changeToTags('editor-superscript', 'sup');
    }
});
