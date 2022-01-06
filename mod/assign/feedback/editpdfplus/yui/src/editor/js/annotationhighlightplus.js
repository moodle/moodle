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
 * Class representing a highlight.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class annotationhighlightplus
 * @extends M.assignfeedback_editpdfplus.annotation
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var ANNOTATIONHIGHLIGHTPLUS = function (config) {
    ANNOTATIONHIGHLIGHTPLUS.superclass.constructor.apply(this, [config]);
};

ANNOTATIONHIGHLIGHTPLUS.NAME = "annotationhighlightplus";
ANNOTATIONHIGHLIGHTPLUS.ATTRS = {};

Y.extend(ANNOTATIONHIGHLIGHTPLUS, M.assignfeedback_editpdfplus.annotation, {

    /**
     * Margin to let for resize area
     * @type Number
     * @protected
     */
    marginDivResize: 4,
    /**
     * Draw a highlight annotation
     * @protected
     * @method draw
     * @return M.assignfeedback_editpdfplus.drawable
     */
    draw: function () {
        var drawable,
                shape,
                bounds,
                highlightcolour;

        highlightcolour = this.get_color();
        this.init_shape_id('hightlightplus');

        drawable = new M.assignfeedback_editpdfplus.drawable(this.editor);
        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(this.x, this.y),
            new M.assignfeedback_editpdfplus.point(this.endx, this.endy)]);

        shape = this.editor.graphic.addShape({
            id: this.shape_id,
            type: Y.Rect,
            width: bounds.width,
            height: bounds.height,
            stroke: false,
            fill: {
                color: highlightcolour,
                opacity: 0.5
            },
            x: bounds.x,
            y: bounds.y
        });

        drawable.shapes.push(shape);
        this.drawable = drawable;

        this.draw_catridge();

        this.draw_resizeAreas();

        return ANNOTATIONHIGHLIGHTPLUS.superclass.draw.apply(this);
    },
    /**
     * Draw the in progress edit.
     *
     * @public
     * @method draw_current_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     */
    draw_current_edit: function (edit) {
        var drawable = new M.assignfeedback_editpdfplus.drawable(this.editor),
                shape,
                bounds,
                highlightcolour;

        bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([new M.assignfeedback_editpdfplus.point(edit.start.x, edit.start.y),
            new M.assignfeedback_editpdfplus.point(edit.end.x, edit.end.y)]);

        // Set min. width of highlight.
        if (!bounds.has_min_width()) {
            bounds.set_min_width();
        }

        highlightcolour = this.get_color();

        // We will draw a box with the current background colour.
        shape = this.editor.graphic.addShape({
            type: Y.Rect,
            width: bounds.width,
            height: 16,
            stroke: false,
            fill: {
                color: highlightcolour,
                opacity: 0.5
            },
            x: bounds.x,
            y: edit.start.y - 8
        });

        drawable.shapes.push(shape);

        return drawable;
    },
    /**
     * Promote the current edit to a real annotation.
     *
     * @public
     * @method init_from_edit
     * @param M.assignfeedback_editpdfplus.edit edit
     * @return bool true if highlight bound is more than min width/height, else false.
     */
    init_from_edit: function (edit) {
        var bounds = new M.assignfeedback_editpdfplus.rect();
        bounds.bound([edit.start, edit.end]);

        this.gradeid = this.editor.get('gradeid');
        this.pageno = this.editor.currentpage;
        this.x = bounds.x;
        this.y = edit.start.y - 8;
        this.endx = bounds.x + bounds.width;
        this.endy = edit.start.y + 16 - 8;
        this.page = '';

        return (bounds.has_min_width());
    },
    /**
     * Display cartridge and toolbox for the annotation
     * @returns {Boolean} res
     */
    draw_catridge: function () {
        var divdisplay;
        if (this.divcartridge === '') {
            this.init_div_cartridge_id();
            var drawingregion = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);

            //init cartridge
            var colorcartridge = this.get_color_cartridge();
            divdisplay = this.get_div_cartridge(colorcartridge);
            divdisplay.addClass('assignfeedback_editpdfplus_hightlightplus');

            // inscription entete
            var divcartridge = this.get_div_cartridge_label(colorcartridge);
            divdisplay.append(divcartridge);

            //creation input
            var divconteneurdisplay = this.get_div_container(colorcartridge);
            var toolbar = this.get_toolbar();
            divconteneurdisplay.append(toolbar);
            divdisplay.append(divconteneurdisplay);

            //creation de la div d'edition
            if (!this.editor.get('readonly')) {
                var diveditiondisplay = this.get_div_edition();
                divconteneurdisplay.append(diveditiondisplay);
            } else {
                var divvisudisplay = this.get_div_visu(colorcartridge);
                divconteneurdisplay.append(divvisudisplay);
            }

            //positionnement de la div par rapport a l'annotation
            if (!this.cartridgex || this.cartridgex === 0) {
                this.cartridgex = parseInt(this.tooltypefamille.cartridge_x, 10);
            }
            if (!this.cartridgey || this.cartridgey === 0) {
                this.cartridgey = parseInt(this.tooltypefamille.cartridge_y, 10);
            }
            divdisplay.setX(this.x + this.cartridgex);
            divdisplay.setY(this.y + this.cartridgey);
            drawingregion.append(divdisplay);

            this.apply_visibility_annot();
        } else {
            this.replacement_cartridge();
        }
        return true;
    },
    /**
     * Replacement of the cartridge after move or resize
     */
    replacement_cartridge: function () {
        var offsetcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS).getXY();
        var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
        if (divdisplay) {
            divdisplay.setX(offsetcanvas[0] + this.x + this.cartridgex);
            divdisplay.setY(offsetcanvas[1] + this.y + this.cartridgey);
        }
    },
    /**
     * Draw empty resize area on left and right
     */
    draw_resizeAreas: function () {
        this.push_div_resizearea('left', this.x - this.marginDivResize, this.y);
        this.push_div_resizearea('right', this.endx - this.marginDivResize, this.y);
    },
    /**
     * Actions when resizing a shape:
     * - on left, new x and width
     * - on right, new width
     * New placement of resize area (div)
     * @param {Event} e
     * @param {Point} point current position
     * @param {div} divresize id of resize area
     */
    mousemoveResize: function (e, point, divresize) {
        if (this.drawable.shapes.length === 0) {
            return;
        }
        var shape = this.drawable.shapes[0];
        if (!shape) {
            return;
        }
        var direction = divresize.getData('direction');
        var width = this.minresizewidth;
        var canvasDim = this.editor.get_canvas_bounds();
        var newpointx = point.x;
        //sortie de cadre
        if (newpointx < 0) {
            newpointx = 0;
        } else if (canvasDim.width < newpointx) {
            newpointx = canvasDim.width;
        }
        var decalage = canvasDim.x;
        if (direction === 'right') {
            width = Math.max(newpointx - this.x, this.minresizewidth);
            shape.set('width', width);
            divresize.setX(this.x + width + decalage - this.marginDivResize);
        } else if (direction === 'left') {
            width = Math.max(this.endx - newpointx, this.minresizewidth);
            shape.set('x', Math.min(newpointx, this.endx - this.minresizewidth));
            shape.set('width', width);
            divresize.setX(this.endx - width + decalage - this.marginDivResize);
        }
    },
    /**
     * Delete an annotation
     * @protected
     * @method remove
     * @param event
     */
    remove: function (e) {
        var annotations,
                i;

        e.preventDefault();

        annotations = this.editor.pages[this.editor.currentpage].annotations;
        for (i = 0; i < annotations.length; i++) {
            if (annotations[i] === this) {
                if (this.divcartridge !== '') {
                    var divdisplay = this.editor.get_dialogue_element('#' + this.divcartridge);
                    divdisplay.remove();
                }
                this.remove_resizearea();
                annotations.splice(i, 1);
                if (this.drawable) {
                    this.drawable.erase();
                }
                this.editor.currentannotation = false;
                this.editor.save_current_page();
                return;
            }
        }
    }

});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.annotationhighlightplus = ANNOTATIONHIGHLIGHTPLUS;
