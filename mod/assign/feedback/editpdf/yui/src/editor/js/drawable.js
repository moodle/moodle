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
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */

/**
 * Class representing a drawable thing which contains both Y.Nodes, and Y.Shapes.
 *
 * @namespace M.assignfeedback_editpdf
 * @param M.assignfeedback_editpdf.editor editor
 * @class drawable
 */
DRAWABLE = function(editor) {

    /**
     * Reference to M.assignfeedback_editpdf.editor.
     * @property editor
     * @type M.assignfeedback_editpdf.editor
     * @public
     */
    this.editor = editor;

    /**
     * Array of Y.Shape
     * @property shapes
     * @type Y.Shape[]
     * @public
     */
    this.shapes = [];

    /**
     * Array of Y.Node
     * @property nodes
     * @type Y.Node[]
     * @public
     */
    this.nodes = [];

    /**
     * Delete the shapes from the drawable.
     * @protected
     * @method erase_drawable
     */
    this.erase = function() {
        if (this.shapes) {
            while (this.shapes.length > 0) {
                this.editor.graphic.removeShape(this.shapes.pop());
            }
        }
        if (this.nodes) {
            while (this.nodes.length > 0) {
                this.nodes.pop().remove();
            }
        }
    };

};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.drawable = DRAWABLE;
