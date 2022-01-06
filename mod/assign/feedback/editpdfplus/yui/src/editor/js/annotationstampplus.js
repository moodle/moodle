/* global M, Y, SELECTOR */

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
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * Class representing a stamp.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationstampplus
 * @extends M.assignfeedback_editpdfplus.annotation
 */
var ANNOTATIONSTAMPPLUS = function (config) {
    ANNOTATIONSTAMPPLUS.superclass.constructor.apply(this, [config]);
};

ANNOTATIONSTAMPPLUS.NAME = "annotationstampplus";
ANNOTATIONSTAMPPLUS.ATTRS = {};

Y.extend(ANNOTATIONSTAMPPLUS, M.assignfeedback_editpdfplus.annotation, {
    /**
     * Draw a stamp annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS),
                node,
                position;

        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(this.x, this.y));
        node = Y.Node.create('<div>' + this.tooltype.label + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'color': this.colour,
            'border': '2px solid ' + this.colour,
            'padding': '0 2px'
        });

        drawingcanvas.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);
        drawable.nodes.push(node);

        this.drawable = drawable;
        return ANNOTATIONSTAMPPLUS.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect(),
                drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGREGION),
                node,
                position;

        bounds.bound([edit.start, edit.end]);
        position = this.editor.get_window_coordinates(new M.assignfeedback_editpdfplus.point(bounds.x, bounds.y));

        node = Y.Node.create('<div>' + this.tooltype.label + '</div>');
        node.setStyles({
            'position': 'absolute',
            'display': 'inline-block',
            'color': this.colour,
            'border': '2px solid ' + this.colour,
            'padding': '0 2px'
        });

        drawingregion.append(node);
        node.setX(position.x);
        node.setY(position.y);
        drawable.store_position(node, position.x, position.y);

        drawable.nodes.push(node);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool if width/height is more than min. required.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        if (bounds.width < 40) {
            bounds.width = 40;
        }
        if (bounds.height < 40) {
            bounds.height = 40;
        }
        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x - 5;
        this.y = bounds.y - 10;
        this.endx = bounds.x + bounds.width;
        this.endy = bounds.y + bounds.height;

        // Min width and height is always more than 40px.
        return true;
    },
    /**
     * display annotation edditing view
     * No edit annot for this annotation
     */
    edit_annot: function () {
        return true;
    },
    /**
     * Move an annotation to a new location.
     * @public
     * @param int newx
     * @param int newy
     * @method move_annotation
     */
    move: function (newx, newy) {
        var diffx = newx - this.x,
                diffy = newy - this.y;

        this.x += diffx;
        this.y += diffy;
        this.endx += diffx;
        this.endy += diffy;

        if (this.drawable) {
            this.drawable.erase();
        }
        this.editor.drawables.push(this.draw());
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationstampplus = ANNOTATIONSTAMPPLUS;
