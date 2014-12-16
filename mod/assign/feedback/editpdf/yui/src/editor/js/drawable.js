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

    /**
     * Update the positions of all absolutely positioned nodes, when the drawing canvas is scrolled
     * @public
     * @method scroll_update
     * @param scrollx int
     * @param scrolly int
     */
    this.scroll_update = function(scrollx, scrolly) {
        var i, x, y;
        for (i = 0; i < this.nodes.length; i++) {
            x = this.nodes[i].getData('x');
            y = this.nodes[i].getData('y');
            if (x !== undefined && y !== undefined) {
                this.nodes[i].setX(parseInt(x, 10) - scrollx);
                this.nodes[i].setY(parseInt(y, 10) - scrolly);
            }
        }
    };

    /**
     * Store the initial position of the node, so it can be updated when the drawing canvas is scrolled
     * @public
     * @method store_position
     * @param container
     * @param x
     * @param y
     */
    this.store_position = function(container, x, y) {
        var drawingregion, scrollx, scrolly;

        drawingregion = Y.one(SELECTOR.DRAWINGREGION);
        scrollx = parseInt(drawingregion.get('scrollLeft'), 10);
        scrolly = parseInt(drawingregion.get('scrollTop'), 10);
        container.setData('x', x + scrollx);
        container.setData('y', y + scrolly);
    };
};

M.assignfeedback_editpdf = M.assignfeedback_editpdf || {};
M.assignfeedback_editpdf.drawable = DRAWABLE;
