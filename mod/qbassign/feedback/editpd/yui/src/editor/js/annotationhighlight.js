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
 * @module moodle-qbassignfeedback_editpd-editor
*/

/**
 * Class representing a highlight.
 *
 * @namespace M.qbassignfeedback_editpd
 * @class annotationhighlight
 * @extends M.qbassignfeedback_editpd.annotation
 * @module moodle-qbassignfeedback_editpd-editor
 */
var ANNOTATIONHIGHLIGHT = function(config) {
    ANNOTATIONHIGHLIGHT.superclass.constructor.apply(this, [config]);
};

ANNOTATIONHIGHLIGHT.NAME = "annotationhighlight";
ANNOTATIONHIGHLIGHT.ATTRS = {};

Y.extend(ANNOTATIONHIGHLIGHT, M.qbassignfeedback_editpd.annotation, {
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.qbassignfeedback_editpd.drawable
     */
    draw: function() {
        var drawable,
            shape,
            bounds,
            highlightcolour;

        drawable = new M.qbassignfeedback_editpd.drawable(this.editor);
        bounds = new M.qbassignfeedback_editpd.rect();
        bounds.bound([new M.qbassignfeedback_editpd.point(this.x, this.y),
                      new M.qbassignfeedback_editpd.point(this.endx, this.endy)]);

        highlightcolour = ANNOTATIONCOLOUR[this.colour];

        // Add an alpha channel to the rgb colour.

        highlightcolour = highlightcolour.replace('rgb', 'rgba');
        highlightcolour = highlightcolour.replace(')', ',0.5)');

        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: false,
            fill: {
                color: highlightcolour
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);
        this.drawable = drawable;

        return ANNOTATIONHIGHLIGHT.superclass.draw.apply(this);
    },

    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.qbassignfeedback_editpd.edit edit
     */
    draw_current_edit: function(edit) {
        var drawable = new M.qbassignfeedback_editpd.drawable(this.editor),
            shape,
            bounds,
            highlightcolour;

        bounds = new M.qbassignfeedback_editpd.rect();
        bounds.bound([new M.qbassignfeedback_editpd.point(edit.start.x, edit.start.y),
                      new M.qbassignfeedback_editpd.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = ANNOTATIONCOLOUR[edit.annotationcolour];
        // Add an alpha channel to the rgb colour.

        highlightcolour = highlightcolour.replace('rgb', 'rgba');
        highlightcolour = highlightcolour.replace(')', ',0.5)');

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 20,
            stroke: false,
            fill: {
               color: highlightcolour
            },
            x: bounds.x,
            y: edit.start.y - 10
        });

        drawable.shapes.push(shape);

        return drawable;
    },

    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.qbassignfeedback_editpd.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function(edit) {
        var bounds = new M.qbassignfeedback_editpd.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y - 10;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 10;
        this.colour = edit.annotationcolour;
        this.page = '';

        return (bounds.has_min_width());
    }

});

M.qbassignfeedback_editpd = M.qbassignfeedback_editpd || {};
M.qbassignfeedback_editpd.annotationhighlight = ANNOTATIONHIGHLIGHT;
